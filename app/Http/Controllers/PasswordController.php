<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\User;
use Mail;
use Validator;

class PasswordController extends Controller{
    function index(){
        return view('auth.forgot');
    }

    function requestPassword(Request $request){
        $validator = Validator::make($request->all(), [
            'email' => 'required|max:255',
            'birth_date' => 'required|max:255',
        ]);

        if ($validator->fails())
            return response()->json(['result'=>'failed','error'=>$validator->errors()->all()], 400);

        $user = User::where('email', $request->input('email'))
                            ->where('birth_date', 'LIKE', $request->input('birth_date').'%')
                            ->get()->first();


        if(!isset($user['id'])) {
            if($result = $this->selfMigrateClient($request->input('email'),null, $request->input('birth_date')) )
                $user = User::where('id', $result['id'])->get()->first();
        }

        if(isset($user['id'])) {
            $generated = md5(rand(1,600));
            $user_data = json_decode($user['user_data'],true);
            $user_data['reset_password_key'] = $generated;
            $user_data['prompt_change_password'] = 1;
            $user_data['reset_password_expiration'] = time() + 300;
            User::where('id', $user['id'])
                        ->update(['user_data'=> json_encode($user_data)]);

            $content_data = ["user"=>$user, "generated"=>$generated];
            $headers = array("subject"=>env("APP_NAME"). ' - Password Reset',
                "to"=> [["email"=>$user['email'], "name"=> $user['first_name']]]
            );
            $this->sendMail('email.password_reset', $content_data, $headers);

            return response()->json(["result"=>"success"]);
        }

        return response()->json(["result"=>"failed"]);
    }

    function verifyPassword(Request $request){
        $user = User::where('email', $request->input('email'))
                    ->get()->first();

        $temporary_password = $this->generateNewPassword(6);

        if(isset($user['id'])){
            $user_data = json_decode($user['user_data'], true);
            if($user_data['reset_password_key'] == $request->input('key')){
                $diff = time() - $user_data['reset_password_expiration'];
                if($diff < 300){

                    User::where('id', $user['id'])
                        ->update(['password'=>bcrypt($temporary_password)]);

                    $headers = array("subject"=>env("APP_NAME"). ' - Temporary Password',
                                     "to"=> [["email"=>$user['email'], "name"=> $user['first_name']]]);
                    $this->sendMail('email.password_temporary', ["user"=>$user, "temporary_password"=>$temporary_password], $headers);

                    $user_data['reset_password_expiration'] = 0;
                    User::where('id', $user['id'])->update(['user_data'=>json_encode($user_data)]);

                    $data = array("result"=>"success");
                }
                else
                    $data = array("result"=>"failed", "error"=>"Link Expired.");
            }
            else
                $data = array("result"=>"failed", "error"=>"Link Mismatch.");
        }
        else
            $data = array("result"=>"failed", "error"=>"Invalid Link.");

        return view('auth.forgot_verify', $data);
    }
}