<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Requests;
use Illuminate\Support\Facades\Auth;
use App\User;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;

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
        try{
            if (! $user = JWTAuth::parseToken()->authenticate()) {
                return response()->json(['user_not_found'], 404);
            }
        }
        catch(TokenExpiredException $e){
            return response()->json(['token_expired'], $e->getStatusCode());
        }
        catch(TokenInvalidException $e){
            return response()->json(['token_invalid'], $e->getStatusCode());
        }
        catch(JWTException $e){
            return response()->json(['token_absent'], $e->getStatusCode());
        }

        // the token is valid and we have found the user via the sub claim
        return response()->json(compact('user'));
    }
}