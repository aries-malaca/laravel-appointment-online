<?php
namespace App\Http\Controllers;
use App\Transaction;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\User;
use App\Config;
use App\TextMessage;
use App\Menu;
use Mail;
use Curl;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use App\Notification;

class Controller extends BaseController{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    //returns true if authenticated,
    //returns error message response if not authenticated
    public function authenticateAPI(){
        try{
            if (! $user = JWTAuth::parseToken()->authenticate()) 
                return ["result"=>"failed","error"=>"token_user_not_found","status_code"=>401];
        }
        catch(TokenExpiredException $e){
            return ["result"=>"failed","error"=>"token_expired", "status_code"=>401];
        }
        catch(TokenInvalidException $e){
            return ["result"=>"failed","error"=>"token_invalid", "status_code"=>401];
        }
        catch(JWTException $e){
            return ["result"=>"failed","error"=>"token_absent","status_code"=>401];
        }

        $parsed = JWTAuth::getToken();
        $tokens = json_decode($user['device_data'],true);
        if(sizeof($tokens) == 0)
            return ["result"=>"failed","error"=>"no_token_registered","status_code"=>401];
        else{
            foreach($tokens as $key=>$value){
                if($parsed == $value['token']){
                    // token is valid and update the last activity
                    $this->updateToken($value['token'], $user);
                    return ["result"=>"success", "user"=>$user, "status_code"=>200];
                }
            }
        }
        return ["result"=>"failed","error"=>"token_not_found" ,"status_code"=>401];
    }

    function updateToken($token, $user){
        User::where('id', $user['id'])->update(['last_activity'=>date('Y-m-d H:i')]);

        $u = User::find($user['id']);
        $data = json_decode($u->device_data,true);
        foreach($data as $key=>$value){
            if($value['token'] == $token) {
                $data[$key]['last_activity'] = date('Y-m-d H:i');
                $u->device_data = json_encode($data);
            }
        }

        $u->save();
    }

    public function getUserMenus($user){
        if($user['is_client'] === 1){
            return Menu::whereIn('menu_group',['client','both'])
                        ->orderBy('order')
                        ->get()->toArray();
        }
        else{
            return Menu::whereIn('menu_group',['admin','both'])
                        ->orderBy('order')
                        ->get()->toArray();
        }
    }

    function generateNewPassword($length = 9, $add_dashes = false, $available_sets = 'luds'){
        $sets = array();
        if(strpos($available_sets, 'l') !== false)
            $sets[] = 'abcdefghjkmnpqrstuvwxyz';
        if(strpos($available_sets, 'u') !== false)
            $sets[] = 'ABCDEFGHJKMNPQRSTUVWXYZ';
        if(strpos($available_sets, 'd') !== false)
            $sets[] = '23456789';
        if(strpos($available_sets, 's') !== false)
            $sets[] = '!@#$%&*?';
        $all = '';
        $password = '';
        foreach($sets as $set) {
            $password .= $set[array_rand(str_split($set))];
            $all .= $set;
        }
        $all = str_split($all);
        for($i = 0; $i < $length - count($sets); $i++)
            $password .= $all[array_rand($all)];
        $password = str_shuffle($password);
        if(!$add_dashes)
            return $password;
        $dash_len = floor(sqrt($length));
        $dash_str = '';
        while(strlen($password) > $dash_len) {
            $dash_str .= substr($password, 0, $dash_len) . '-';
            $password = substr($password, $dash_len);
        }
        $dash_str .= $password;
        return $dash_str;
    }

    /**
     * @param $user_id
     * @param $token
     * @param string $type
     * @param null $device_info
     */
    public function registerToken($user_id, $token, $type='WEB', $device_info=null,$device_unique_id = null){
        if($type == 'WEB'){
            $device_info = $_SERVER['HTTP_USER_AGENT'];
        }

        $user   = User::find($user_id);
        $tokens = json_decode($user->device_data, true);
        $key_find    = false;
        foreach ($tokens as $key => $value) {
            $token_unique_id = $value["unique_device_id"];
            if($token_unique_id == $device_unique_id && $device_unique_id != null && $token_unique_id != null){
                $key_find    = $key;
            }
        }
        $data   = array(  
                      "token"           => $token,
                      "type"            => $type,
                      "device_info"     => $device_info,
                      "registered"      => date('Y-m-d H:i'),
                      "last_activity"   => date('Y-m-d H:i'),
                      "unique_device_id"=> $device_unique_id
                    );

        if($key_find !== false){
            $tokens[$key_find] = $data;
        }
        else{
            array_unshift($tokens, $data);
        }
        $user->last_login  = date('Y-m-d H:i');
        $user->device_data = json_encode($tokens);
        $user->save();
    }

    public function selfMigrateClient($email, $password=null, $birth_date=null){
        if($password !== null)
            $client = DB::connection('old_mysql')->select("SELECT * FROM clients WHERE cusemail='". $email ."' AND password='". md5($password) ."'");
        elseif($password === null && $birth_date === null)
            $client = DB::connection('old_mysql')->select("SELECT * FROM clients WHERE cusemail='". $email ."'");
        else
            $client = DB::connection('old_mysql')->select("SELECT * FROM clients WHERE cusemail='". $email ."' AND cusbday LIKE '". $birth_date."%'");

        if(!empty($client))
            $client = $client[0];

        if(isset($client->cusid)){
            $boss_data = $this->getBossClient($email);

            $user = new User;
            $user->email = $email;
            $user->password = bcrypt($password);
            $user->first_name = ($client->cusfname != '') ? $client->cusfname : $boss_data['firstname'];
            $user->middle_name = ($client->cusmname != '') ? $client->cusmname : $boss_data['middlename'];
            $user->last_name = ($client->cuslname != '') ? $client->cuslname : $boss_data['lastname'];
            $user->username = $user->first_name .' ' . $user->last_name;
            $user->birth_date = date('Y-m-d',strtotime($client->cusbday));
            $user->user_mobile = $client->cusmob;
            $user->gender = ($boss_data['gender']=='m') ? 'male':'female';
            $user->level = 0;
            $user->user_data = json_encode(array("premier_status"=>($boss_data['premier'] != null ? $boss_data['premier']:0),
                "premier_branch"=>($boss_data['premier_branch'] != null ? $boss_data['premier_branch']:0),
                "home_branch"=>($boss_data['branch_id']!=null ? $boss_data['branch_id']:10 ),
                "notifications"=>["email"]
            ));
            $user->device_data = '[]';
            $user->last_activity = date('Y-m-d H:i');
            $user->last_login = date('Y-m-d H:i');
            $user->is_confirmed = ($client->confirmed == 'Confirmed') ? 1:0;
            $user->is_active = 1;
            $user->is_client = 1;
            $user->transaction_data = '[]';
            $user->notifications_read = '[]';
            $user->is_agreed = ($client->confirmed=='Confirmed'?1:0);
            $user->user_picture = 'no photo '. ($boss_data['gender']=='m' ? 'male':'female') .'.jpg';
            $user->save();
            //end self migration
            return ['token'=>JWTAuth::fromUser(User::find($user->id)), 'id'=> $user->id];
        }
        return false;
    }

    function getConfigs(){
        $data = Config::get()->toArray();
        $array = array();
        foreach($data as $key=>$value){
            $array[$value['config_name']] = $value['config_value'];
        }

        return $array;
    }

    public function getBossClient($email){
        $response = Curl::to(Config::where('config_name', 'SEARCH_BOSS_CLIENT')->get()->first()['config_value'] . $email)
                        ->returnResponseObject()
                        ->get();
        if($response->status >= 200 && $response->status <= 210) {
            $boss_data = $response->content;

            if($boss_data !== false)
                return json_decode($boss_data,true);
        }
        return false;
    }

    function incrementConfigVersion($config_name){
        $config = Config::where('config_name', $config_name)->get()->first();
        $config->config_value = number_format((float)$config->config_value + 0.1,1);
        $config->save();
    }

    function emailReceiver($email){
        if(env('APP_MAILING_ENV')==='development')
            return env('APP_MAILING_DEV_ADDRESS');

        return $email;
    }

    function getLastSignature($client_id){
        $transaction = Transaction::where('client_id', $client_id)
                                    ->where('acknowledgement_data', 'LIKE', '%"signature":"data:%')
                                    ->get()->first();

        if(isset($transaction['id']))
            return json_decode($transaction['acknowledgement_data'])->signature;

        $transaction = Transaction::where('client_id', $client_id)
                                    ->where('waiver_data', 'LIKE', '%"signature":"data:%')
                                    ->get()->first();

        if(isset($transaction['id']))
            return json_decode($transaction['waiver_data'])->signature;

        return 'data:image/png;base64,' . base64_encode(file_get_contents(public_path('images/na.png')));
    }

    function sendMail($template, $content_data, $headers, $attachments=null){

        Mail::send($template, $content_data, function ($mail) use($headers, $attachments) {
            $mail->from(env('MAIL_USERNAME'), env('APP_NAME'));
            $mail->subject($headers['subject']);

            foreach($headers['to'] as $to)
                $mail->to($this->emailReceiver($to['email']), $to['name']);

            if(isset($headers['cc']))
                foreach($headers['cc'] as $cc)
                    $mail->cc($this->emailReceiver($cc['email']), $cc['name']);

            if(env('APP_MAILING_BCC_DEV'))
                $mail->bcc(env('APP_MAILING_DEV_ADDRESS'));
            if($attachments !== null)
                foreach($attachments as $att)
                    $mail->attach(public_path($att));
        });
    }

    function sendSMS($message, $mobile, $title, $api, $shortcode){
        // Send a POST request to: http://www.foo.com/bar
        $response = Curl::to('https://devapi.globelabs.com.ph/smsmessaging/v1/outbound/'.$shortcode.'/requests')
            ->withData( array( 'app_id' => env('GLOBE_API_APP_ID'),
                'app_secret' => env('GLOBE_API_APP_SECRET'),
                'message' => $message,
                'address' => $mobile,
                'passphrase' => env('GLOBE_API_PASSPHRASE') ) )
            ->asJson()
            ->post();

        if(isset($response->outboundSMSMessageRequest)){
            $text = new TextMessage;
            $text->message = $message;
            $text->recipient = $mobile;
            $text->title = $title;
            $text->sent_by = $api['user']['id'];
            $text->save();

            return $response;
        }

        return false;
    }

    function createNotification($type, $user_id, $data, $send_mail = false){
        $notification = new Notification;
        $notification->notification_type = $type;
        $notification->notification_data = json_encode($data);
        $notification->user_id = $user_id;
        $notification->is_read = 0;
        $notification->save();

        $user = User::find($user_id);
        if(isset($user->id)){
            $d = json_decode($user->user_data);
            if(in_array('email', $d->notifications) && $send_mail) {
                if(isset($data['message']) && isset($data['title'])){

                }
            }
        }
    }

    // public function sendPushNotification(Request $request){

    //     $hub   = new NotificationHub("Endpoint=sb://laybarenotifnamespace.servicebus.windows.net/;SharedAccessKeyName=DefaultFullSharedAccessSignature;SharedAccessKey=kzEkLjz8LR8zlgorOAh4/QJrAAci/x1leu7evDZOPto=", "LayBareNotificationHub"); 
        
    //     //android
    //     $message        = '{"data":{"user_id":57427,"unique_id":1,"notification_type":"notification"}}';
    //     $notification   = new Notification_Azure("gcm", $message);
    //     $hub->sendNotification($notification, null);    

    //     // //ios
    //     // $alert = '{"aps":{"alert":"Hello from PHP!"}}';
    //     // $notification = new Notification_Azure("apple", $alert);
    //     // $hub->sendNotification($notification, null);
    // }
}