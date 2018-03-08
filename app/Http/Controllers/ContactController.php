<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Contact;
use Excel;

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
}
