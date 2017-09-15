<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\User;
use App\Client;
use App\UserLevel;
use App\Branch;
use App\Config;
use JWTAuth;
use Validator;
use Hash;
use ImageOptimizer;
use Facebook\Facebook;

class UserController extends Controller{
    public function login(Request $request){
        //attempt to login the system
        $u = User::where('email', $request['email'])->get()->first();
        if(isset($u['id'])){
            if(Hash::check($request['password'], $u['password'])){
                $token = JWTAuth::fromUser(User::find($u['id']));
                return response()->json($token);
            }
            return response()->json(["result"=>"failed"]);
        }

        if($token = $this->selfMigrateClient($request->input('email'), $request->input('password'))){
            return response()->json($token);
        }

        return response()->json(["result"=>"failed"]);
    }

    public function selfMigrateClient($email, $password){
        $client = Client::where('cusemail', $email)
                            ->where('password', md5($password))
                            ->get()->first();
        if(isset($client['cusid'])){
            //start self migration

            //end self migration
            return JWTAuth::fromUser(User::find(5));
        }

        return false;
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
            return response()->json(["user"=>$api["user"],
                                     "menus"=>$this->getUserMenus($api["user"]),
                                     "configs"=> Config::get()->toArray()
                                    ],
                            $api["status_code"]);
        }
           
        return response()->json($api, $api["status_code"]);
    }

    public function getUsers(){
        return response()->json(User::leftJoin('user_levels','user_levels.id','=','users.level')
                                ->where('is_client', 0)
                                ->select('users.*', 'level_name')
                                ->get());
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

    public function uploadPicture(Request $request){
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
                    $timestamp = time().'.'.$ext ;
                    $file->move('images/users/', $request->input('user_id') . '_' . $timestamp);
                    $user = User::find($request->input('user_id'));
                    $user->user_picture = $request->input('user_id') . '_' . $timestamp;
                    $user->save();
                    return response()->json(["result"=>"success"],200);
                }
                return response()->json(["result"=>"failed","error"=>"Invalid File Format."],400);
            }
            return response()->json(["result"=>"failed","error"=>"No File to be uploaded."], 400);
        }

        return response()->json($api, $api["status_code"]);
    }

    public function fbLogin(Facebook $fb, Request $request){
        // call api to retrieve person's public_profile details
        $fields = "id,name,email,first_name,last_name,middle_name,gender,locale,picture,verified";
        $fb->setDefaultAccessToken($request->input('accessToken'));
        $fb_user = $fb->get('/me?fields='.$fields)->getGraphUser()->asArray();

        $user = User::where('user_data','LIKE', '%"facebook_id":"'.$request->input('userID').'"%')->get()->first();
        if(isset($user['id'])){
            $token = JWTAuth::fromUser($user);
            return response()->json($token);
        }

        $user = User::where('email', $fb_user['email'])->get()->first();
        if(isset($user['id'])){
            $token = JWTAuth::fromUser($user);
            return response()->json($token);
        }

        return response()->json(['result'=>'failed', "user"=>$fb_user],300);
    }

    public function addUser(Request $request){
        $api = $this->authenticateAPI();
        if($api['result'] === 'success'){
            $validator = Validator::make($request->all(), [
                'first_name' => 'required|max:255',
                'last_name' => 'required|max:255',
                'user_mobile' => 'required|max:255',
                'user_address' => 'required|max:255',
                'email' => 'required|email|unique:users,email',
                'gender' => 'required|in:male,female',
                'level' => 'required|not_in:0'
            ]);

            if ($validator->fails()) {
                return response()->json(['result'=>'failed','error'=>$validator->errors()->all()], 400);
            }

            if(sizeof($request->input('user_data')['branches']) == 0){
                return response()->json(['result'=>'failed','error'=>'Select at least 1 branch.'], 400);
            }

            $branches = array();
            foreach($request->input('user_data')['branches'] as $value){
                $branches[] = $value['value'];
            }

            $user = new User;
            $user->first_name = $request->input('first_name');
            $user->middle_name = $request->input('middle_name');
            $user->last_name = $request->input('last_name');
            $user->user_mobile = $request->input('user_mobile');
            $user->email = $request->input('email');
            $user->password = bcrypt(12345);
            $user->gender = $request->input('gender');
            $user->user_address = $request->input('user_address');
            $user->is_confirmed = 0;
            $user->is_active = 1;
            $user->device_data = '{}';
            $user->birth_date = '2000-01-01';
            $user->user_picture = 'no photo ' . $request->input('gender').'.jpg';
            $user->level = $request->input('level');
            $user->is_client = 0;
            $user->user_data = json_encode(array("branches"=>$branches));
            $user->save();

            return response()->json(["result"=>"success"]);
        }

        return response()->json($api, $api["status_code"]);
    }

    public function updateUser(Request $request){
        $api = $this->authenticateAPI();
        if($api['result'] === 'success'){
            $validator = Validator::make($request->all(), [
                'first_name' => 'required|max:255',
                'last_name' => 'required|max:255',
                'user_mobile' => 'required|max:255',
                'user_address' => 'required|max:255',
                'email' => 'required|email|unique:users,email,'.$request->input('id'),
                'gender' => 'required|in:male,female',
                'level' => 'required|not_in:0'
            ]);

            if ($validator->fails()) {
                return response()->json(['result'=>'failed','error'=>$validator->errors()->all()], 400);
            }

            if(sizeof($request->input('user_data')['branches']) == 0){
                return response()->json(['result'=>'failed','error'=>'Select at least 1 branch.'], 400);
            }

            $branches = array();
            foreach($request->input('user_data')['branches'] as $value){
                $branches[] = $value['value'];
            }

            $user = User::find($request->input('id'));
            $user->first_name = $request->input('first_name');
            $user->middle_name = $request->input('middle_name');
            $user->last_name = $request->input('last_name');
            $user->user_mobile = $request->input('user_mobile');
            $user->email = $request->input('email');
            $user->gender = $request->input('gender');
            $user->user_address = $request->input('user_address');
            $user->level = $request->input('level');
            $user->user_data = json_encode(array("branches"=>$branches));
            $user->save();

            return response()->json(["result"=>"success"]);
        }

        return response()->json($api, $api["status_code"]);
    }
}