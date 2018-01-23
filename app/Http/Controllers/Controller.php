<?php
namespace App\Http\Controllers;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\User;
use App\Client;
use App\Config;
use App\Menu;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;

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
        if(sizeof($tokens) == 0){
            return ["result"=>"failed","error"=>"no_token_registered","status_code"=>401];
        }
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
    public function registerToken($user_id, $token, $type='WEB', $device_info=null){
        if($type == 'WEB'){
            $device_info = $_SERVER['HTTP_USER_AGENT'];
        }

        $user = User::find($user_id);
        $tokens = json_decode($user->device_data, true);
        $data = array("token" => $token,
                      "type" => $type,
                      "device_info" => $device_info,
                      "registered" => date('Y-m-d H:i'),
                      "last_activity" => date('Y-m-d H:i')
                    );
        array_unshift($tokens, $data);
        $user->last_login = date('Y-m-d H:i');
        $user->device_data = json_encode($tokens);
        $user->save();
    }

    public function selfMigrateClient($email, $password=null, $birth_date=null){

        if($password !== null){
            $client = Client::where('cusemail', $email)
                ->where('password', md5($password))
                ->get()->first();
        }
        elseif($password === null && $birth_date === null){
            $client = Client::where('cusemail', $email)
                ->get()->first();
        }
        else{
            $client = Client::where('cusemail', $email)
                ->where('cusbday', 'LIKE', $birth_date.'%')
                ->get()->first();
        }

        if(isset($client['cusid'])){
            $boss_data = $this->getBossClient($email);

            $user = new User;
            $user->email = $email;
            $user->password = bcrypt($password);
            $user->first_name = ($client['cusfname'] != '') ? $client['cusfname'] : $boss_data['firstname'];
            $user->middle_name = ($client['cusmname'] != '') ? $client['cusmname'] : $boss_data['middlename'];
            $user->last_name = ($client['cuslname'] != '') ? $client['cuslname'] : $boss_data['lastname'];
            $user->username = $user->first_name .' ' . $user->last_name;
            $user->birth_date = date('Y-m-d',strtotime($client['cusbday']));
            $user->user_mobile = $client['cusmob'];
            $user->gender = ($boss_data['gender']=='m') ? 'male':'female';
            $user->level = 0;
            $user->user_data = json_encode(array("premier_status"=>($boss_data['premier'] != null ? $boss_data['premier']:0),
                "premier_branch"=>($boss_data['premier_branch'] != null ? $boss_data['premier_branch']:0),
                "home_branch"=>($boss_data['branch_id']!=null ? $boss_data['branch_id']:0 ) ));
            $user->device_data = '[]';
            $user->last_activity = date('Y-m-d H:i');
            $user->last_login = date('Y-m-d H:i');
            $user->is_confirmed = ($client['confirmed'] == 'Confirmed') ? 1:0;
            $user->is_active = 1;
            $user->is_client = 1;
            $user->is_agreed = ($client['confirmed']=='Confirmed'?1:0);
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
        $boss_data = file_get_contents(Config::where('config_name', 'SEARCH_BOSS_CLIENT')->get()->first()->config_value . $email);

        if($boss_data === false){
            return false;
        }
        //start self migration

        return json_decode($boss_data,true);
    }

    function getBossID($email){
        $boss_data = file_get_contents(Config::where('config_name', 'GET_BOSS_ID')->get()->first()->config_value . $email);

        if($boss_data === false)
            return false;

        //start self migration
        return json_decode($boss_data,true);
    }

    function generateUserPermission($level_data){
        $system_permissions = config('app.permissions');
        foreach($system_permissions as $key=>$value){
            foreach($value['actions'] as $k=>$v){
                $system_permissions[$key]['actions'][$k] =
                    array("label"=>$v,
                          "value"=>isset($level_data->permissions)?
                                        $this->getPermissionValue($value['name'], $v, $level_data->permissions):false
                        );
            }
        }
        return $system_permissions;
    }

    function getPermissionValue($name, $action, $data){
        if(isset($data))
        foreach($data as $key=>$value){
            if($name == $value->name){
                foreach($value->actions as $k=>$v){
                    if($v->label == $action)
                        return $v->value;
                }
            }
        }
        return false;
    }
}