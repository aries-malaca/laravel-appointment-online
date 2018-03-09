<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Notification;

class NotificationController extends Controller{
    function getUserNotifications(){
        $api = $this->authenticateAPI();
        if($api['result'] === 'success'){
            $data = Notification::where('user_id', $api['user']['id'])
                                    ->orderBy('created_at', 'DESC')
                                    ->get()->toArray();
            return response()->json($data);
        }
        return response()->json($api, $api["status_code"]);
    }

    function seenNotifications(){
        $api = $this->authenticateAPI();
        if($api['result'] === 'success'){
            Notification::where('user_id', $api['user']['id'])
                        ->update(["is_read"=>1]);

            return response()->json(["result"=>"success"]);
        }
        return response()->json($api, $api["status_code"]);
    }
}
