<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Message;
use App\User;
use App\UserLevel;
use DB;
use Validator;

class MessageController extends Controller{
    function sendMessage(Request $request){
        $validator = Validator::make($request->all(), [
            'body' => 'required',
        ]);

        if ($validator->fails())
            return response()->json(['result'=>'failed','error'=>$validator->errors()->all()], 400);

        $api = $this->authenticateAPI();
        if($api['result'] === 'success') {
            $message = new Message;
            $message->body = $request->input('body');
            $message->title = $request->input('title');
            $message->sender_id = $api['user']['id'];
            $message->recipient_id = $request->input('recipient_id');
            $message->message_data = '{}';
            $message->is_read = 0;
            $message->save();
            return response()->json(["result"=>"success"]);
        }
        return response()->json($api, $api["status_code"]);
    }

    function getConversation(Request $request){
        $api = $this->authenticateAPI();
        if($api['result'] === 'success') {
            $data = Message::whereIn('recipient_id', [$api['user']['id'], $request->segment(4)])
                            ->whereIn('sender_id', [$api['user']['id'], $request->segment(4)])
                            ->orderBy('created_at')
                            ->get()->toArray();

            return response()->json($data);
        }
        return response()->json($api, $api["status_code"]);
    }

    function deleteConversation(Request $request){
        $api = $this->authenticateAPI();
        if($api['result'] === 'success') {
            Message::whereIn('recipient_id', [$api['user']['id'], $request->input('recipient_id')])
                ->whereIn('sender_id', [$api['user']['id'], $request->input('recipient_id')])
                ->delete();

            return response()->json(["result"=>"success"]);
        }
        return response()->json($api, $api["status_code"]);
    }

    function seenMessages(Request $request){
        $api = $this->authenticateAPI();

        if($api['result'] === 'success') {
             Message::where('recipient_id', $api['user']['id'])
                        ->where('sender_id', $request->input('sender_id'))
                        ->update(['is_read'=>1]);

            return response()->json(["result"=>"success"]);
        }
        return response()->json($api, $api["status_code"]);
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

                    if($d->dashboard != 'CustomerServiceDashboard'){
                        $where = ' AND is_client=0 ';
                    }
                }
            }

            $data = DB::select("SELECT first_name, last_name, a.id as id, user_picture, level_name, is_client,(SELECT COUNT(id) as unread FROM messages 
                                                WHERE is_read=0 AND
                                                    sender_id=a.id
                                                    AND recipient_id=" . $api['user']['id'] . " 
                                                ) as unread 
                                              FROM users AS a 
                                              LEFT JOIN user_levels AS b ON a.level=b.id 
                                              WHERE 1=1 ". $where . " AND a.id <> ". $api['user']['id'] ." 
                                              ORDER BY unread DESC, first_name");

            return response()->json($data);
        }
        return response()->json($api, $api["status_code"]);
    }

    function countUnseenMessages(Request $request){
        $api = $this->authenticateAPI();

        if($api['result'] === 'success') {
            return response()->json(["count"=>Message::where('sender_id', $request->segment(4))
                        ->where('recipient_id', $api['user']['id'])
                        ->where('is_read', 0)
                        ->count()]);
        }
        return response()->json($api, $api["status_code"]);
    }

    function getLastMessage(Request $request){
        $api = $this->authenticateAPI();

        if($api['result'] === 'success') {
            return response()->json(Message::leftJoin('users', 'messages.sender_id','=','users.id')
                ->leftJoin('user_levels', 'users.level', '=','user_levels.id')
                ->where('sender_id', $request->segment(4))
                ->where('recipient_id', $api['user']['id'])
                ->where('is_read', 0)
                ->select( 'body as message','first_name', 'last_name', 'users.id', 'user_picture', 'level_name','is_client')
                ->orderBy('messages.created_at', 'DESC')
                ->get()->first());
        }
        return response()->json($api, $api["status_code"]);

    }
}
