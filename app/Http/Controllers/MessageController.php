<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Message;
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
                            ->take($request->segment(5))
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

    function getUnreadMessages(Request $request){
        $api = $this->authenticateAPI();

        if($api['result'] === 'success') {
            return response()->json(Message::where('recipient_id', $api['user']['id'])
                                            ->where('is_read', 0)
                                            ->get()->toArray());
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
