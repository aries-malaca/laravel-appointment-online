<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use DB;

class AuditController extends Controller{
    function getAudits(Request $request){
        $data = DB::select(DB::raw("SELECT * FROM audits WHERE new_values <> '[]' AND (user_id=" .$request->segment(4)  . " 
                            OR (auditable_id=" . $request->segment(4) . " AND ISNULL(user_id))) 
                            ORDER BY created_at DESC 
                            LIMIT 20"));
        $logs = array();
        foreach($data as $key=>$value){
            $old = json_decode($value->old_values);
            $new = json_decode($value->new_values);
            $action = ucfirst($value->event) . ' ' . str_replace("App\\", "", $value->auditable_type);
            if($value->new_values !== '[]'){
                $body = [];
                if($value->event==='updated')
                    foreach($old as $k=>$v)
                        $body[] = ["field"=>$k, "new_value"=>$v, "old_value"=> $new->$k ];

                $category = strtolower(str_replace("App\\", "", $value->auditable_type));
                if($value->user_id === $value->auditable_id) {
                    $action = "Updated Profile";
                    $category = 'user';
                }
                if(isset($old->last_login)){
                    $action = "Logged in the system";
                    $body = [];
                }
                $logs[] = array(
                    "action" => $action,
                    "body" => $body,
                    "category"=>$category,
                    "ip_address"=>$value->ip_address,
                    "created_at"=>$value->created_at,
                    "reference_id"=>$value->auditable_id,
                    "event"=>$value->event
                );
            }
        }
        return response()->json($logs);
    }
}
