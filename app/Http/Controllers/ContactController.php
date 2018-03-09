<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Contact;
use App\UserLevel;
use Excel;
use DB;

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

    function getContactList(){
        $where = '';
        $api = $this->authenticateAPI();

        if($api['result'] === 'success') {
            if($api['user']['is_client'] == 1)
                $where = " AND level_data LIKE '%CustomerServiceDashboard%'";
            else{
                $level = UserLevel::find($api['user']['level']);

                if(isset($level->id)){
                    $d = json_decode($level->level_data);

                    if($d->dashboard != 'CustomerServiceDashboard')
                        $where = ' AND is_client=0 ';
                }
            }

            $data = DB::select("SELECT username, first_name, last_activity, last_name, a.id as id, user_picture, level_name, is_client,
                                  ( (a.last_activity) > ('" . date('Y-m-d H:i:s', time() - 300) . "') ) as is_online
                                  FROM users AS a 
                                  LEFT JOIN user_levels AS b ON a.level=b.id 
                                  WHERE 1=1 ". $where . " AND a.id <> ". $api['user']['id'] ."
                                  ORDER BY is_online DESC, first_name");

            return response()->json($data);
        }
        return response()->json($api, $api["status_code"]);
    }
}
