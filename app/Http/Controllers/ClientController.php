<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\User;
use App\Branch;
use Validator;

class ClientController extends Controller{
    public function searchClients(Request $request){

        $keyword = $request->input('keyword');
        if($keyword == ''){
            return response()->json(["result"=>"failed","error"=>"Please Enter Keyword."], 400);
        }
        $clients = User::where('is_client', 1);
        $clients = $clients->where(function($query) use ($keyword){
            $query->where('first_name', 'LIKE', '%' . $keyword . '%')
                    ->orWhere('middle_name', 'LIKE', '%' . $keyword . '%')
                    ->orWhere('last_name', 'LIKE', '%' . $keyword . '%')
                    ->orWhere('email', 'LIKE', '%' . $keyword . '%')
                    ->orWhere('user_address', 'LIKE', '%' . $keyword . '%')
                    ->orWhere('user_mobile', 'LIKE', '%' . $keyword . '%');
        });
        return response()->json($clients->get());
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
            $client['home_branch'] = array("label"=>$client['home_branch_name'], "value"=> $client['home_branch_id']);
        }
        return response()->json($client);
    }

    public function updateInfo(Request $request){
        $api = $this->authenticateAPI();
        if($api['result'] === 'success'){
            $validator = Validator::make($request->all(), [
                'first_name' => 'required|max:255',
                'middle_name' => 'max:255',
                'last_name' => 'required|max:255',
                'user_address' => 'required|max:255',
                'user_mobile' => 'required|max:255',
                'home_branch' => 'required'
            ]);

            if ($validator->fails()) {
                return response()->json(['result'=>'failed','error'=>$validator->errors()->all()], 400);
            }

            $client = User::find($request->input('id'));
            $client->first_name = $request->input('first_name');
            $client->middle_name = $request->input('middle_name');
            $client->last_name = $request->input('last_name');
            $client->user_address = $request->input('user_address');
            $client->user_mobile = $request->input('user_mobile');
            $client->birth_date = $request->input('birth_date');
            $data = json_decode($client->user_data);
            $data->home_branch = $request->input('home_branch')['value'];
            $client->user_data = json_encode($data);
            $client->save();

            return response()->json(["result"=>"success"]);
        }
        return response()->json($api, $api["status_code"]);
    }

    public function updatePassword(Request $request){
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

    public function updateSettings(){

    }
}