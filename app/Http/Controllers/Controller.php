<?php
namespace App\Http\Controllers;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\User;
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
                return ["result"=>"failed","error"=>"token_user_not_found","status_code"=>404];
        }
        catch(TokenExpiredException $e){
            return ["result"=>"failed","error"=>"token_expired", "status_code"=>$e->getStatusCode()];
        }
        catch(TokenInvalidException $e){
            return ["result"=>"failed","error"=>"token_invalid", "status_code"=>$e->getStatusCode()];
        }
        catch(JWTException $e){
            return ["result"=>"failed","error"=>"token_absent","status_code"=>$e->getStatusCode()];
        }

        $parsed = JWTAuth::getToken();
        $tokens = json_decode($user['device_data'],true);
        if(sizeof($tokens) == 0){
            return ["result"=>"failed","error"=>"no_token_registered","status_code"=>300];
        }
        else{
            foreach($tokens as $key=>$value){
                if($parsed == $value['token']){
                    // token is valid and update the last activity
                    User::where('id', $user['id'])->update(['last_activity'=>date('Y-m-d H:i')]);
                    return ["result"=>"success", "user"=>$user, "status_code"=>200];
                }
            }
        }

        return ["result"=>"failed","error"=>"token_not_found" ,"status_code"=>300];
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
}