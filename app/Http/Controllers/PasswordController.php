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
        if ($validator->fails()) {
            return response()->json(['result'=>'failed','error'=>$validator->errors()->all()], 400);
        }

        $user = User::where('email', $request->input('email'))
                            ->where('birth_date', 'LIKE', $request->input('birth_date').'%')
                            ->get()->first();


        if(!isset($user['id'])) {
            if($result = $this->selfMigrateClient($request->input('email'),null, $request->input('birth_date')) ){
                $user = User::where('id', $result['id'])->get()->first();
            }
        }

        if(isset($user['id'])) {
            $generated = md5(rand(1,600));
            $user_data = json_decode($user['user_data'],true);
            $user_data['reset_password_key'] = $generated;
            $user_data['reset_password_expiration'] = time() + 300;
            User::where('id', $user['id'])
                        ->update(['user_data'=> json_encode($user_data)]);

            Mail::send('email.reset_password', ["user"=>$user, "generated"=>$generated], function ($message) use($user) {
                $message->from('notification@system.lay-bare.com', 'LBO');
                $message->subject('Password Reset');
                $message->to($user['email'], $user['first_name']);
            });

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
                    Mail::send('email.temporary_password', ["user"=>$user, "temporary_password"=>$temporary_password], function ($message) use($user) {
                        $message->from('notification@system.lay-bare.com', 'LBO');
                        $message->subject('Temporary Password');
                        $message->to($user['email'], $user['first_name']);
                    });
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
