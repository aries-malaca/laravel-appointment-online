<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Requests;
use Illuminate\Support\Facades\Auth;
use App\User;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
class UserController extends Controller
{
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
                return response()->json(Auth::user()->email);
            }
            else
                return response()->json(["result"=>"failed"]);
        }
   }
    public function logout(){
        Auth::logout();
        return redirect('login');
    }
}