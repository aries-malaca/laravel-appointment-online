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

    //1st
    public function login(Request $request){
        //attempt to login the system
        $u = User::where('email', $request['email'])->get()->first();
        if(isset($u['id'])){
            if(Hash::check($request['password'], $u['password'])){
                $token = JWTAuth::fromUser(User::find($u['id']));

                if($request->input('device') === null)
                    $this->registerToken($u['id'], $token);
                else
                    $this->registerToken($u['id'], $token, $request->input('device'), $request->input('device_info'));

                return response()->json(["token"=>$token, "result"=>'success']);
            }
            return response()->json(["result"=>"failed","error"=>"Incorrect Password"],400);
        }
        if($token = $this->selfMigrateClient($request->input('email'), $request->input('password'))){
            return response()->json(["token"=>$token, "result"=>'success']);
        }
        return response()->json(["result"=>"failed","error"=>"User not found."],400);
    }

    public function selfMigrateClient($email, $password){

        $client = Client::where('cusemail', $email)
                        ->where('password', '827ccb0eea8a706c4c34a16891f84e7b')
                        ->get()->first();

        if(isset($client['cusid'])){

            $boss_data = file_get_contents('http://boss.lay-bare.com/laybare-online/API/search_client.php?email=' . $email);

            if($boss_data === false){
                return false;
            }
            //start self migration

            $boss_data = json_decode($boss_data,true);

            $user = new User;
            $user->email = $email;
            $user->password = bcrypt($password);
            $user->username = '';
            $user->first_name = ($client['cusfname'] != '') ? $client['cusfname'] : $boss_data['firstname'];
            $user->middle_name = ($client['cusmname'] != '') ? $client['cusmname'] : $boss_data['middlename'];
            $user->last_name = ($client['cuslname'] != '') ? $client['cuslname'] : $boss_data['lastname'];
            $user->birth_date = date('Y-m-d',strtotime($client['cusbday']));
            $user->user_mobile = $client['cusmob'];
            $user->gender = ($boss_data['gender']=='m') ? 'male':'female';
            $user->level = 0;
            $user->user_data = json_encode(array("premier_status"=>$boss_data['premier'],
                                                 "premier_branch"=>$boss_data['premier_branch'],
                                                 "home_branch"=>$boss_data['branch_id']));
            $user->device_data = '{}';
            $user->last_activity = date('Y-m-d H:i');
            $user->last_login = date('Y-m-d H:i');
            $user->is_confirmed = ($client['confirmed'] == 'Confirmed') ? 1:0;
            $user->is_active = 1;
            $user->is_client = 1;
            $user->save();
            //end self migration
            return JWTAuth::fromUser(User::find($user->id));
        }
        return false;
    }

    public function getUser(){
        $api = $this->authenticateAPI();
        if($api['result'] === 'success'){
            $user_data = json_decode($api['user']['user_data'],true);
            if($api['user']['is_client'] == 1){
                $branch = Branch::find($user_data['home_branch']);
                if(isset($branch->id)){
                    $branch = $branch->branch_name;
                }
                else{
                    $branch = 'N/A';
                }
                $api['user']['branch'] = ["value"=>$user_data['home_branch'], "label"=> $branch];
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
                'verify_password' => 'required|same:new_password'
                // required and has to match the password field
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
            if($request->input('image') === null){
                return response()->json(["result"=>"failed","error"=>"No File to be uploaded."], 400);
            }

            $data = $request->input('image');
            list($type, $data) = explode(';',$data);
            list(,$data) = explode(',', $data);

            if($type == 'data:image/jpeg')
               $ext = 'jpg';
            elseif($type == 'data:image/png')
                $ext = 'png';
            else
                return response()->json(["result"=>"failed","error"=>"Invalid File Format."],400);

            $filename = $request->input('user_id') . '_' . time().'.'.$ext ;
            $data = base64_decode($data);
            file_put_contents(public_path('images/users/'). $filename, $data );
            $user = User::find($request->input('user_id'));

            if($user->user_picture != 'no photo ' . $user->gender .'.jpg')
                if(file_exists(public_path('/images/users/'.$user->user_picture)))
                    unlink(public_path('/images/users/'.$user->user_picture));

            $user->user_picture = $filename;
            $user->save();

            return response()->json(["result"=>"success"],200);
        }

        return response()->json($api, $api["status_code"]);
    }

    public function fbLogin(Facebook $fb, Request $request){
        // call api to retrieve person's public_profile details
        $fields = "id,name,email,birthday,first_name,last_name,middle_name,gender,locale,picture,verified";
        $fb->setDefaultAccessToken($request->input('accessToken'));
        $fb_user = $fb->get('/me?fields='.$fields)->getGraphUser()->asArray();

        $user = User::where('user_data','LIKE', '%"facebook_id":"'.$request->input('userID').'"%')->get()->first();
        if(!isset($user['id'])){
            $user = User::where('email', $fb_user['email'])->get()->first();
        }

        if(isset($user['id'])){
            $token = JWTAuth::fromUser($user);

            User::where($user['id'])->update(['is_confirmed'=>1]);
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

    public function destroyToken(Request $request){
        $user = User::find($request->input('user_id'));
        $tokens = json_decode($user->device_data, true);

        if(sizeof($tokens) == 0){
            $user->device_data = json_encode(array());
        }
        else{
            foreach ($tokens as $t=>$v){
                if($v['token'] == $request->input('token')){
                    unset($tokens[$t]);
                }
            }
            $user->device_data = json_encode($tokens);
        }
        $user->save();

        return response()->json(["result"=>"success"]);
    }
}