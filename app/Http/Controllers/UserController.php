<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Requests;
use Illuminate\Support\Facades\Auth;
use App\User;
use App\UserLevel;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Validator;

class UserController extends Controller{
    public function login(Request $request){
        header('Access-Control-Allow-Origin: *');
        if($request->input('api')){
            // grab credentials from the request
            $credentials = $request->only('email', 'password');
            try {
                // attempt to verify the credentials and create a token for the user
                if (! $token = JWTAuth::attempt($credentials)) {
                    return response()->json(['error' => 'invalid_credentials'], 401);
                }
            } 
            catch (JWTException $e) {
                // something went wrong whilst attempting to encode the token
                return response()->json(['error' => 'could_not_create_token'], 500);
            }
    
            // all good so return the token
            return response()->json(compact('token'));
        }
        else{
            //attempt to login the system
            if(Auth::attempt( [ 'email' => $request['email'], 'password' => $request['password'] ], $request['remember'])){
                $user = Auth::user();
                $token = JWTAuth::fromUser($user);
                User::where('id',Auth::user()->id)->update(['access_token'=>$token]);
                return response()->json($token);
            }
            else
                return response()->json(["result"=>"failed"]);
        }
   }
    public function logout(){
        Auth::logout();
        return redirect('login');
    }

    public function getUser(){
        $api = $this->authenticateAPI();
        if($api['result'] === 'success'){
            if($api['user']['is_client'] == 1){
                $api['user']['branch'] = ["branch_id"=>1, "branch_name"=>"Orlando Suites Manila"]; 
            }
            else{
                $api['user']['level_name'] = UserLevel::find($api['user']['level'])->level_name; 
            }
            return response()->json(["user"=>$api["user"], "menus"=>$this->getUserMenus($api["user"])], $api["status_code"]);
        }
           
        return response()->json($api, $api["status_code"]);
    }

    public function updateProfile(Request $request){
        $api = $this->authenticateAPI();
        if($api['result'] === 'success'){
            $validator = Validator::make($request->all(), [
                'first_name' => 'required|max:255',
                'last_name' => 'required|max:255',
            ]);
            if ($validator->fails()) {
                return response()->json(['result'=>'failed','error'=>$validator->errors()->all()], 400);
            }
            $user = User::find($api['user']['id']);
            
            return response()->json(["result"=>"success"]);
        }
           
        return response()->json($api, $api["status_code"]);
    }

    public function resendConfirmation(Request $request){
        $api = $this->authenticateAPI();
        if($api['result'] === 'success'){
            return response()->json(["result"=>"success"]);
        }
           
        return response()->json($api, $api["status_code"]);
    }
}