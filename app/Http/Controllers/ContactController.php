<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Contact;
use App\UserLevel;
use Excel;
use Validator;
use DB;
use App\Message;

class ContactController extends Controller{
    function getContacts(){
        $data = Contact::orderBy('first_name')
                        ->get()->toArray();

        foreach($data as $key=>$value){
            $data[$key]['email_addresses'] = json_decode($value['email_addresses']);
            $data[$key]['mobiles'] = json_decode($value['mobiles']);
        }

        return response()->json($data);
    }

    function importContacts(Request $request){
        $rows = Excel::selectSheetsByIndex(1)->load(public_path('files/csv/'. $request->segment(4) .'.' . $request->segment(5)))->get()->toArray();
        foreach ($rows as $key=>$value){
            if($value['first_name']){
                $contact = new Contact;
                $contact->first_name = strlen($value['first_name'])>2?ucfirst($value['first_name']):$value['first_name'];
                $contact->last_name = ucfirst($value['last_name']);
                $contact->email_addresses = json_encode([$value['email']]);
                $contact->mobiles = json_encode([$value['mobile']]);
                $contact->gender = $value['gender'];
                $contact->remarks = 'Staff';
                $contact->save();
            }
        }
    }

    function addContact(Request $request){
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|max:255',
            'last_name' => 'required|max:255',
            'email' => 'required|max:255',
            'mobile' => 'required|max:255',
            'gender' => 'required|in:female,male',
            'remarks' => 'required',
            'new_group' => 'required_if:remarks,new',
        ]);

        if ($validator->fails())
            return response()->json(['result'=>'failed','error'=>$validator->errors()->all()], 400);

        $api = $this->authenticateAPI();

        if($api['result'] === 'success') {
            $contact = new Contact;
            $contact->first_name = $request->input('first_name');
            $contact->last_name = $request->input('last_name');
            $contact->gender = $request->input('gender');
            $contact->email_addresses = json_encode([$request->input('email')]);
            $contact->mobiles = json_encode([$request->input('mobile')]);
            $contact->remarks = $request->input('remarks') !== 'new'?$request->input('remarks'):$request->input('new_group');
            $contact->save();
            return response()->json(['result'=>'success']);
        }

        return response()->json($api, $api["status_code"]);
    }

    function updateContact(Request $request){
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|max:255',
            'last_name' => 'required|max:255',
            'email' => 'required|max:255',
            'mobile' => 'required|max:255',
            'gender' => 'required|in:female,male',
            'remarks' => 'required',
            'new_group' => 'required_if:remarks,new',
        ]);

        if ($validator->fails())
            return response()->json(['result'=>'failed','error'=>$validator->errors()->all()], 400);

        $api = $this->authenticateAPI();

        if($api['result'] === 'success') {
            $contact = Contact::find($request->input('id'));
            $contact->first_name = $request->input('first_name');
            $contact->last_name = $request->input('last_name');
            $contact->gender = $request->input('gender');
            $contact->email_addresses = json_encode([$request->input('email')]);
            $contact->mobiles = json_encode([$request->input('mobile')]);
            $contact->remarks = $request->input('remarks') !== 'new'?$request->input('remarks'):$request->input('new_group');
            $contact->save();
            return response()->json(['result'=>'success']);
        }

        return response()->json($api, $api["status_code"]);
    }

    function deleteContact(Request $request){
        $api = $this->authenticateAPI();

        if($api['result'] === 'success') {
            Contact::destroy($request->input('id'));
            return response()->json(['result'=>'success']);
        }

        return response()->json($api, $api["status_code"]);
    }

    function getContactList(){
        $where = '';
        $api = $this->authenticateAPI();

        if($api['result'] === 'success') {
            if($api['user']['is_client'] == 1)
                $where = " AND (level_data LIKE '%CustomerServiceDashboard%' OR level_data LIKE '%BranchSupervisorDashboard%')";
            else{
                $level = UserLevel::find($api['user']['level']);

                if(isset($level->id)){
                    $d = json_decode($level->level_data);

                    if($d->dashboard != 'CustomerServiceDashboard')
                        $where = ' AND is_client=0';
                    else{
                        $ids = Message::where('recipient_id', $api['user']['id'])
                                        ->where('is_closed', '0')
                                        ->get()->pluck('sender_id')->toArray();

                        if(sizeof($ids) > 0)
                            $where = "a.id IN (". implode(",", $ids) .") OR";

                        $data = DB::select("SELECT username, level_data, user_data, first_name, last_activity, last_name, a.id as id, user_picture, level_name, is_client, ( (a.last_activity) > ('" . date('Y-m-d H:i:s', time() - 600) . "') ) as is_online FROM users AS a  LEFT JOIN user_levels AS b ON a.level=b.id  WHERE " . $where . " is_client = 0 ORDER BY is_online DESC, first_name");

                        return response()->json($data);
                    }
                }
            }

            $data = DB::select("SELECT username, level_data, user_data, first_name, last_activity, last_name, a.id as id, user_picture, level_name, is_client,
                                  ( (a.last_activity) > ('" . date('Y-m-d H:i:s', time() - 600) . "') ) as is_online
                                  FROM users AS a 
                                  LEFT JOIN user_levels AS b ON a.level=b.id 
                                  WHERE 1=1 ". $where . " AND a.id <> ". $api['user']['id'] ."
                                  ORDER BY is_online DESC, first_name");

            foreach($data as $key=>$v) {
                $data[$key]->level_data = json_decode($v->level_data);
                $data[$key]->user_data = json_decode($v->user_data);
            }

            return response()->json($data);
        }
        return response()->json($api, $api["status_code"]);
    }
}