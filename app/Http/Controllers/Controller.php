<?php
namespace App\Http\Controllers;
use App\Transaction;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\User;
use App\Config;
use App\Service;
use App\ServiceType;
use App\ServicePackage;
use App\Product;
use App\ProductGroup;
use App\TextMessage;
use App\TransactionItem;
use App\Menu;
use Mail;
use DB;
use Curl;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use App\Notification;
use App\Jobs\SendEmailJob;
use App\Libraries\NotificationHub;
use App\Libraries\Notification_Azure;

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

    function getAppointmentItems($id){
        $items = TransactionItem::leftJoin('transactions', 'transaction_items.transaction_id', '=', 'transactions.id')
            ->where('transaction_id', $id)
            ->select('transaction_items.*','transactions.serve_time', 'transactions.complete_time')
            ->get()->toArray();
        foreach($items as $key=>$value){
            $items[$key]['item_data'] = json_decode($value['item_data']);
            if($value['item_type'] === 'service'){
                $service = Service::find($value['item_id']);
                $service_name = $service->service_type_id !== 0 ? ServiceType::find($service->service_type_id)->service_name:ServicePackage::find($service->service_package_id)->package_name;

                if($service->service_type_id !== 0)
                    $service_image = ServiceType::find($service->service_type_id)->service_picture;
                else
                    $service_image  = ServicePackage::find($service->service_package_id)->package_image;

                $items[$key]['item_name']       = $service_name;
                $items[$key]['item_image']      = $service_image;
                $items[$key]['item_duration']   = $service->service_minutes;
                $items[$key]['item_info']['gender'] = $service->service_gender;
            }
            else{
                $product       = Product::find($value['item_id']);
                $product_name  = ProductGroup::find($product->product_group_id)->product_group_name;
                $product_image = ProductGroup::find($product->product_group_id)->product_picture;
                $items[$key]['item_name']            = $product_name;
                $items[$key]['item_info']['size']    = $product->product_size;
                $items[$key]['item_info']['variant'] = $product->product_variant;
                $items[$key]['item_image']           = $product_image;
            }
        }
        return $items;
    }

    /**
     * @param $user_id
     * @param $token
     * @param string $type
     * @param null $device_info
     */
    public function registerToken($user_id, $token, $type='WEB', $device_info=null,$device_unique_id = null){
        if($type == 'WEB')
            $device_info = $_SERVER['HTTP_USER_AGENT'];
        

        $user   = User::find($user_id);
        $tokens = json_decode($user->device_data, true);
        $key_find    = false;
        foreach ($tokens as $key => $value) {
            $token_unique_id = "";
            if(isset($value["unique_device_id"]))
                $token_unique_id = $value["unique_device_id"];
            
            if($token_unique_id == $device_unique_id && $device_unique_id != null && $token_unique_id != null)
                $key_find    = $key;
        }
        $data   = array(  
                      "token"           => $token,
                      "type"            => $type,
                      "device_info"     => $device_info,
                      "registered"      => date('Y-m-d H:i'),
                      "last_activity"   => date('Y-m-d H:i'),
                      "unique_device_id"=> $device_unique_id
                    );

        if($key_find !== false)
            $tokens[$key_find] = $data;
        else
            array_unshift($tokens, $data);
        
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
            if($boss_data === false)
                $user->user_data = json_encode(array("premier_status"=>0,
                    "premier_branch"=>0,
                    "home_branch"=>10,
                    "notifications"=>["email"]
                ));
            else
                $user->user_data = json_encode(array("premier_status"=>($boss_data['premier'] != null ? $boss_data['premier']:0),
                    "premier_branch"=>($boss_data['premier_branch'] != null ? $boss_data['premier_branch']:0),
                    "home_branch"=>($boss_data['branch_id']!=null ? $boss_data['branch_id']:10 ),
                    "boss_id"=>$boss_data['custom_client_id'],
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
        $data = [
            "template"=>$template,
            "content_data"=>$content_data,
            "headers"=>$headers,
            "attachments"=>$attachments
        ];
        SendEmailJob::dispatch($data)->delay(now()->addSeconds(2));
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

        $objectData   = json_encode($data);
        $notification = new Notification;
        $notification->notification_type = $type;
        $notification->notification_data = $objectData;
        $notification->user_id = $user_id;
        $notification->save();

        $obj                    = json_decode($objectData);
        $unique_id              = $obj->unique_id;
        $query                  = User::where("id",$user_id)->get()->first();
        $arrayDeviceData        = json_decode($query->device_data,true);
        $array                  = array();

        foreach ($arrayDeviceData as $key => $value) {
            $devicetype         = $value["type"];
            $unique_device_id   = $value["unique_device_id"];
            $this->sendPushNotification($devicetype,$unique_device_id,$unique_id,"appointment",$user_id);
            // break;
        }    

        Curl::to(env('AZURE_WEBHOOKS_URL') . '/refreshNotifications/'. $user_id)->get();

        $user = User::where('id',$user_id)->get()->first();
        if(isset($user['id'])){
            $d = json_decode($user['user_data']);
            if(isset( $d->notifications))
                if(in_array('email', $d->notifications) && $send_mail)
                    if(isset($data['body']) && isset($data['title'])){

                        $headers = array("subject" => env("APP_NAME") .' - ' . $data['title'],
                                            "to" => [["email" => $user['email'], "name" => $user['username']]]);

                        switch($type){
                            case 'appointment':
                                $appointment = Transaction::leftJoin('branches', 'transactions.branch_id', '=', 'branches.id')
                                                ->leftJoin('technicians', 'transactions.technician_id', '=', 'technicians.id')
                                                ->where('transactions.id', $data['unique_id'])
                                                ->select('branch_name', 'technicians.first_name as technician_first_name', 'technicians.last_name as technician_last_name',
                                                    'transactions.*')
                                                ->get()->first();
                                $appointment['items'] = $this->getAppointmentItems($data['unique_id']);

                                if($data['title'] == 'Expired Appointment') {
                                    $template = 'email.appointment_expired_client';
                                }
                                elseif($data['title'] == 'Appointment Complete')
                                    $template = 'email.appointment_completed';

                                $data = ["user"=>$user, "appointment"=> $appointment]; //override data

                                if(isset($template))
                                    $this->sendMail($template, $data, $headers);
                            break;
                            // other types
                        }
                    }
        }
    }

    public function sendPushNotification($devicetype,$device_id,$unique_id,$notification_type,$user_id){
        $queryConfig        = Config::where("config_name","GET_PUSH_NOTIFICATION")->get()->first();
        $objectValue        = json_decode($queryConfig->config_value);
        $full_shared_access = $objectValue->connection_string_full_access;
        $shared_access      = $objectValue->connection_string;
        $hub_name           = $objectValue->hub_name;

        $hub                = new NotificationHub($full_shared_access, $hub_name);
        //android
        if($devicetype == "Android"){
             $message        = '{"data":{"user_id":'.$user_id.',"unique_id":"'.$unique_id.'","notification_type":"appointment"}}';
            $notification   = new Notification_Azure("gcm", $message);
            $hub->sendNotification($notification, $device_id);  
        }
        // //ios
        if($devicetype == "IOS"){
            $alert = '{"aps":{"user_id":'.$user_id.',"unique_id":"'.$unique_id.'","notification_type":"notification"}}';
            $notification = new Notification_Azure("apple", $alert);
            $hub->sendNotification($notification, $device_id);
        }
    }

      public function sendChatNotification($devicetype,$device_id,$thread_id,$notification_type,$user_id){
        
        $queryConfig        = Config::where("config_name","GET_PUSH_NOTIFICATION")->get()->first();
        $objectValue        = json_decode($queryConfig->config_value);
        $full_shared_access = $objectValue->connection_string_full_access;
        $shared_access      = $objectValue->connection_string;
        $hub_name           = $objectValue->hub_name;

        $hub                = new NotificationHub($full_shared_access, $hub_name);
        //android
        if($devicetype == "Android"){
             $message        = '{"data":{"user_id":'.$user_id.',"unique_id":"'.$thread_id.'","notification_type":"chat"}}';
            $notification   = new Notification_Azure("gcm", $message);
            $hub->sendNotification($notification, $device_id);  
        }
        // //ios
        if($devicetype == "IOS"){
            $alert = '{"aps":{"user_id":'.$user_id.',"unique_id":1,"notification_type":"notification"}}';
            $notification = new Notification_Azure("apple", $alert);
            $hub->sendNotification($notification, $device_id);
        }
    }
}