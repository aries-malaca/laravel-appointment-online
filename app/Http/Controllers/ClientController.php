<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\User;
use App\Branch;
use Validator;
use DB;

class ClientController extends Controller{

    function searchAdvancedClients(Request $request){
        $find = DB::connection('old_mysql')->select("SELECT * FROM clients WHERE 1 = 1 " . ($request->input('email') !== null? "AND cusemail LIKE '%". $request->input('email') ."%'":"") . ($request->input('first_name') !== null ? " AND cusfname LIKE '%". $request->input('first_name') ."%'":"") .($request->input('last_name') !== null ? " AND cuslname LIKE '%". $request->input('last_name') ."%'":"") ."LIMIT 10");

        $final = array();
        foreach($find as $key=>$value){
            $f = User::where('email', $value->cusemail)->get()->first();

            if(!isset($f))
                $final[] = $value;
        }
        return response()->json($final);
    }

    function migrateClient(Request $request){
        $validator = Validator::make($request->all(), [
            'cusfname' => 'required|max:255',
            'cuslname' => 'required|max:255',
            'cusemail' => 'required|email|unique:users,email',
            'cusbday' => 'required|date_format:Y-m-d|before_or_equal:'. date('Y-m-d', strtotime("-13 years")),
        ],[
            'cusemail.unique' => 'Client already in the database',
            'cusbday.date_format'    => 'Birth Date must be mm/dd/yyyy format',
            'cusbday.before_or_equal' => 'Must be at least 13 years old to register'
        ]);

        if ($validator->fails())
            return response()->json(['result'=>'failed','error'=>$validator->errors()->all()], 400);


        $password = $this->generateNewPassword();
        $boss_data = $this->getBossClient($request->input('cusemail'));

        $user = new User;
        $user->email = $request->input('cusemail');
        $user->password = bcrypt($password);
        $user->first_name = ($request->input('cusfname') != '') ? $request->input('cusfname') : $boss_data['firstname'];
        $user->middle_name = ($request->input('cusmname') != '') ? $request->input('cusmname') : $boss_data['middlename'];
        $user->last_name = ($request->input('cuslname')!= '') ? $request->input('cuslname') : $boss_data['lastname'];
        $user->username = $user->first_name .' ' . $user->last_name;
        $user->birth_date = date('Y-m-d',strtotime($request->input('cusbday')));
        $user->user_mobile = $request->input('cusmob');
        $user->gender = ($boss_data['gender']=='m') ? 'male':'female';
        $user->level = 0;
        if($boss_data === false)
            $user->user_data = json_encode(array("premier_status"=>0,
                "premier_branch"=>0,
                "home_branch"=>10,
                "notifications"=>["email"]
            ));
        else{
            $bbb  = Branch::find($boss_data['branch_id']);
            if(!isset($bbb->id))
                $boss_data['branch_id'] = null;

        }
        $user->user_data = json_encode(array("premier_status"=>($boss_data['premier'] != null ? $boss_data['premier']:0),
            "premier_branch"=>($boss_data['premier_branch'] != null ? $boss_data['premier_branch']:0),
            "home_branch"=>($boss_data['branch_id']!=null ? $boss_data['branch_id']:10 ),
            "boss_id"=>$boss_data['custom_client_id'],
            "notifications"=>["email"]
        ));
        $user->device_data = '[]';
        $user->last_activity = date('Y-m-d H:i');
        $user->last_login = date('Y-m-d H:i');
        $user->is_confirmed = ($request->input('confirmed') == 'Confirmed') ? 1:0;
        $user->is_active = 1;
        $user->is_client = 1;
        $user->transaction_data = '[]';
        $user->notifications_read = '[]';
        $user->is_agreed = ($request->input('confirmed')=='Confirmed'?1:0);
        $user->user_picture = 'no photo '. ($boss_data['gender']=='m' ? 'male':'female') .'.jpg';
        $user->save();

        $user = User::where('id', $user->id)->get()->first();
        $this->dispatchVerification($user, $password);

        return response()->json(["result"=>"success"]);
    }

    public function searchClients(Request $request){
        $imploded = explode(" ", $request->input('keyword'));

        if($request->input('keyword') == '')
            return response()->json(["result"=>"failed","error"=>"Please Enter Keyword."], 400);

        $clients = User::leftJoin('user_levels', 'users.level', 'user_levels.id')
                ->where('is_client', 1);
        foreach($imploded as $keyword){
            $clients = $clients->where(function($query) use ($keyword){
                $query->where('first_name', 'LIKE', '%' . $keyword . '%')
                    ->orWhere('middle_name', 'LIKE', '%' . $keyword . '%')
                    ->orWhere('last_name', 'LIKE', '%' . $keyword . '%')
                    ->orWhere('email', 'LIKE', '%' . $keyword . '%')
                    ->orWhere('user_address', 'LIKE', '%' . $keyword . '%')
                    ->orWhere('user_mobile', 'LIKE', '%' . $keyword . '%');
            });
        }

        $clients = $clients->select('users.*','level_name','level_data')
                        ->orderBy('first_name')
                        ->take(30)
                        ->get()->toArray();
        foreach($clients as $key=>$value){
            $clients[$key]['user_data'] = json_decode($value['user_data']);
            $clients[$key]['picture_html_big'] = '<img class="img img-thumbnail" style="width:100px" src="images/users/'. $value['user_picture'] .'" />';
            $clients[$key]['picture_html'] = '<img class="img-circle" style="height:35px" src="images/users/'. $value['user_picture'] .'" />';
            $clients[$key]['gender_html'] = '<span class="badge badge-'. ($value['gender']=='male'?'success':'warning').'">'.  $value['gender'] .'</span>';
            $clients[$key]['name'] = $value['first_name'] .' '. $value['last_name'];
        }
        return response()->json($clients);
    }

    public function filterClients(Request $request){
        $search = DB::table('users')
                    ->select(DB::raw('floor(DATEDIFF(CURDATE(),birth_date) /365.25) as age'),'first_name', 'gender',
                            'last_name','user_address', 'user_mobile AS mobile', 'email', 'user_data')
                    ->where('is_client', 1);
        //this is for gender and address
        $search = $search->where(function($query) use ($request){
            if($request->input('gender') !== null)
                $query = $query->where('gender', $request->input('gender'));

            $addresses = $request->input('address');
            if(sizeof($addresses)>0)
                $query->where('user_address', '<>', '')
                    ->where(function($q) use ($addresses){
                        foreach($addresses as $address)
                            $q = $q->orWhere('user_address', 'LIKE', '%'. $address .'%');
                    });
        });

        $search = $search->get()
            ->map(function($value){
                $value->user_data = json_decode($value->user_data);
                return $value;
             })
            ->filter(function($value) use ($request){
                if(sizeof($request->input('home_branch')) >0 ){
                    $home_branches = [];
                    foreach($request->input('home_branch') as $k=>$v)
                        $home_branches[] = (int)$v['value'];

                    if(!in_array($value->user_data->home_branch, $home_branches))
                        return false;
                }
                if($request->input('premier_status') !== null){
                    if($value->user_data->premier_status === 0 && $request->input('premier_status') || $value->user_data->premier_status === 1 && !$request->input('premier_status'))
                        return false;
                }
                if($request->input('age') !== null){
                    if(! ($value->age >= $request->input('age')[0] && $value->age <= $request->input('age')[1]) )
                        return false;
                }
                return true;
            })->all();

        $search = $this->unserialize($search);
        return response()->json($search);
    }

    function unserialize($object){
        $array = [];
        foreach($object as $o)
            $array[] = $o;

        return $array;
    }

    public function getClient(Request $request){
        $client = User::where('is_client', 1)
                        ->where('id', $request->segment(4))->get()->first();
        if(isset($client['id'])){
            $client['user_data']= json_decode($client['user_data']);
            $client['device_data']= json_decode($client['device_data']);
            $client['home_branch_id'] = $client['user_data']->home_branch;
            $find = Branch::find($client['home_branch_id']);
            $client['home_branch_name'] = isset($find->id)? $find->branch_name:'N/A';
            $client['transaction_data'] = json_decode($client['transaction_data']);
            $client['home_branch'] = array("label"=>$client['home_branch_name'], "value"=> $client['home_branch_id']);
        }
        return response()->json($client);
    }

    public function updateTransactionData(Request $request){
        $api = $this->authenticateAPI();
        if($api['result'] === 'success'){
            $user = User::find($request->input('id'));
            if(isset($user->id)){
                $boss_client = $this->getBossClient($user['email']);
                if($boss_client !== false){
                    $ud = json_decode($user->user_data);
                    $ud->boss_id = $boss_client['custom_client_id'];
                    $user->user_data = json_encode($ud);
                }

                $user->transaction_data = json_encode($request->input('data'));
                $user->save();
            }

            return response()->json(["result"=>"success"]);
        }
        return response()->json($api, $api["status_code"]);
    }

    public function updateInfo(Request $request){
        $api = $this->authenticateAPI();
        if($api['result'] === 'success'){
            $validator = Validator::make($request->all(), [
                'first_name' => 'required|max:255',
                'middle_name' => 'required|max:255',
                'last_name' => 'required|max:255',
                'user_address' => 'required|max:255',
                'user_mobile' => 'required|max:255',
                'home_branch' => 'required'
            ]);

            if ($validator->fails())
                return response()->json(['result'=>'failed','error'=>$validator->errors()->all()], 400);

            //check boss ID unique
            if(isset($request->input('user_data')['boss_id'])){
                $checker = User::where('id', '<>', $request->input('id'))
                                ->where("user_data", "LIKE", '%"boss_id":"'. $request->input('user_data')['boss_id'] .'"%')->count();
                if ($checker > 0)
                    return response()->json(['result'=>'failed','error'=>["BOSS ID (Transaction account) already been taken."]], 400);


            }

            $client = User::find($request->input('id'));
            $client->first_name = $request->input('first_name');
            $client->middle_name = $request->input('middle_name');
            $client->last_name = $request->input('last_name');
            $client->username = $request->input('first_name') .' '.$request->input('last_name');
            $client->user_address = $request->input('user_address');
            $client->user_mobile = $request->input('user_mobile');
            $client->birth_date = $request->input('birth_date');
            $data = json_decode($client->user_data);
            $data->home_branch = (int)$request->input('home_branch')['value'];
            $data->notifications = $request->input('user_data')['notifications'] === null? [] :$request->input('user_data')['notifications'];
            $data->boss_id = !isset($request->input('user_data')['boss_id'])? null :$request->input('user_data')['boss_id'];
            $client->user_data = json_encode($data);
            $client->save();

            return response()->json(["result"=>"success"]);
        }
        return response()->json($api, $api["status_code"]);
    }

    public function changePassword(Request $request){
        $api = $this->authenticateAPI();
        if($api['result'] === 'success'){
            $validator = Validator::make($request->all(), [
                'password'=>'required|regex:/^.*(?=.*[a-zA-Z])(?=.*[0-9]).*$/'
            ]);

            if ($validator->fails()) {
                return response()->json(['result'=>'failed','error'=>$validator->errors()->all()], 400);
            }

            $client = User::find($request->input('id'));
            $client->password = bcrypt($request->input('password'));
            $client->save();

            return response()->json(["result"=>"success"]);
        }
        return response()->json($api, $api["status_code"]);
    }
}