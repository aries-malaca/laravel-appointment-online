<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Requests;
use Illuminate\Support\Facades\Auth;
use App\User;
use App\UserLevel;
use App\Branch;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Validator;
use Hash;
use ImageOptimizer;

class UserController extends Controller{

    public function login(Request $request){
        //attempt to login the system
        if(Auth::attempt( [ 'email' => $request['email'], 'password' => $request['password'] ], $request['remember'])){
            $user = Auth::user();
            $token = JWTAuth::fromUser($user);
            return response()->json($token);
        }
        else
            return response()->json(["result"=>"failed"]);

   }
    public function logout(){
        Auth::logout();
        return redirect('login');
    }

    public function getUser(){
        $api = $this->authenticateAPI();
        if($api['result'] === 'success'){
            $user_data = json_decode($api['user']['user_data'],true);
            if($api['user']['is_client'] == 1){
                $api['user']['branch'] = ["value"=>$user_data['home_branch'], "label"=> Branch::find($user_data['home_branch'])->branch_name];
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
            ImageOptimizer::optimize(public_path('images/users/' . $api['user']['user_picture']));

            $validator = Validator::make($request->all(), [
                'first_name' => 'required|max:255',
                'middle_name' => 'required|max:255',
                'last_name' => 'required|max:255',
                'user_mobile' => 'required|max:255',
                'user_address' => 'required|max:255',
                'branch' => 'required_if:is_client,1',
            ]);
            if ($validator->fails()) {
                return response()->json(['result'=>'failed','error'=>$validator->errors()->all()], 400);
            }
            $user_data = json_decode( User::find($api['user']['id'])->user_data,true);
            $user_data['home_branch'] = $request->input('branch')['value'];

            User::where('id',$api['user']['id'])
                ->update([  'first_name'=>$request->input('first_name'),
                            'middle_name'=>$request->input('middle_name'),
                            'last_name'=>$request->input('last_name'),
                            'user_address'=>$request->input('user_address'),
                            'user_mobile'=>$request->input('user_mobile'),
                            'user_data'=>json_encode($user_data)
                ]);
            return response()->json(["result"=>"success"]);
        }
           
        return response()->json($api, $api["status_code"]);
    }

    public function getUserLevels(){
        return response()->json(UserLevel::get()->toArray());
    }

    public function resendConfirmation(Request $request){
        $api = $this->authenticateAPI();
        if($api['result'] === 'success'){
            return response()->json(["result"=>"success"]);
        }
           
        return response()->json($api, $api["status_code"]);
    }

    public function changePassword(Request $request){
        $api = $this->authenticateAPI();
        if($api['result'] === 'success'){
            if(!Hash::check($request->input('old_password'), $api['user']['password'] )){
                return response()->json(["result"=>"failed","error"=>"Old password incorrect"], 400);
            }

            $validator = Validator::make($request->all(),[
                'new_password'     => 'required|regex:/^.*(?=.*[a-zA-Z])(?=.*[0-9]).*$/',
                'verify_password' => 'required|same:new_password'           // required and has to match the password field
            ]);
            if ($validator->fails()) {
                return response()->json(['result'=>'failed','error'=>$validator->errors()->all()], 400);
            }

            User::where('id', $api['user']['id'])->update(['password'=>bcrypt($request->input('new_password'))]);
            return response()->json(["result"=>"success"]);
        }

        return response()->json($api, $api["status_code"]);
    }

    public function updatePicture(Request $request){
        $api = $this->authenticateAPI();
        if($api['result'] === 'success') {
            //valid extensions
            $valid_ext = array('jpeg', 'gif', 'png', 'jpg');
            //check if the file is submitted
            if ($request->hasFile('file')) {
                $file = $request->file('file');
                $ext = $file->getClientOriginalExtension();

                //check if extension is valid
                if (in_array($ext, $valid_ext)) {
                    $file->move('images/users', $api['user']['id'] . '_' . $file->getClientOriginalName());
                    $user = User::find($api['user']['id']);
                    $user->user_picture = $api['user']['id'] . '_' . $file->getClientOriginalName();
                    $user->save();
                    return response()->json(["result"=>"success"], $api["status_code"]);
                }
                return response()->json(["result"=>"failed","error"=>"Invalid File Format."],400);
            }
            return response()->json(["result"=>"failed","error"=>"No File to be uploaded."], 400);
        }

        return response()->json($api, $api["status_code"]);
    }
}