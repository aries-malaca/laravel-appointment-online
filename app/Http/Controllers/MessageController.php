<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Message;
use App\MessageThread;
use App\User;
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

            $is_client = $api["user"]["is_client"];
            $thread = Message::whereIn('sender_id', [$api['user']['id'], $request->input('recipient_id')])
                            ->whereIn('recipient_id', [$api['user']['id'], $request->input('recipient_id')])
                            ->get()->first();
            if(isset($thread['id'])){
                $thread_id = $thread['message_thread_id'];
                $updateThread                 = MessageThread::find($thread_id);
                $updateThread->updated_at     = date("Y-m-d H:i:s");
                $updateThread->save();
            }
            else{
                $thread = new MessageThread;
                $thread->created_by_id = $api['user']['id'];
                $thread->participant_ids = json_encode([$request->input('recipient_id')]);
                $thread->save();
                $thread_id = $thread->id;
            }

            $message                    = new Message;
            $message->body              = $request->input('body');
            $message->title             = $request->input('title');
            $message->sender_id         = $api['user']['id'];
            $message->recipient_id      = $request->input('recipient_id');
            $message->message_data      = '{}';
            $message->message_thread_id = $thread_id;
            $message->save();
            // $arrayDeviceData            = array();

            if($is_client != 1){
                $client_id              = $request->input('recipient_id');
                $query                  = User::where("id",$client_id)->get()->first();
                $arrayDeviceData        = json_decode($query->device_data,true);

                foreach ($arrayDeviceData as $key => $value) {

                    if(isset($value["unique_device_id"])){
                        $devicetype         = $value["type"];
                        $unique_device_id   = $value["unique_device_id"];
                        $this->sendChatNotification($devicetype,$unique_device_id,$thread_id,"chat",$client_id);
                    }
                    // break;
                }
            }    
            return response()->json(["result"=>"success", "thread_id"=> $message->message_thread_id]);
        }
        return response()->json($api, $api["status_code"]);
    }

    function getConversation(Request $request){
        $api = $this->authenticateAPI();
        if($api['result'] === 'success') {
            $data = Message::whereIn('recipient_id', [$api['user']['id'], $request->segment(4)])
                            ->whereIn('sender_id', [$api['user']['id'], $request->segment(4)])
                            ->where(function($query) use($request){
                                $query->orWhereNull('deleted_to_id');
                                $query->orWhere('deleted_to_id', '=', $request->segment(4));
                            })
                            ->orderBy('created_at','DESC')
                            ->take($request->segment(5))
                            ->get()->toArray();

            $last_id = Message::whereIn('recipient_id', [$api['user']['id'], $request->segment(4)])
                            ->whereIn('sender_id', [$api['user']['id'], $request->segment(4)])
                            ->where(function($query) use($request){
                                $query->orWhereNull('deleted_to_id');
                                $query->orWhere('deleted_to_id', '=', $request->segment(4));
                            })
                            ->get()->first();

            if(isset($last_id['id']))
                $last_id = $last_id['id'];

            return response()->json(['messages'=>$data, 'last_id'=>$last_id]);
        }
        return response()->json($api, $api["status_code"]);
    }

    function deleteConversation(Request $request){
        $api = $this->authenticateAPI();
        if($api['result'] === 'success') {
            $messages = Message::whereIn('recipient_id', [$api['user']['id'], $request->input('recipient_id')])
                ->whereIn('sender_id', [$api['user']['id'], $request->input('recipient_id')])
                ->get();

            foreach($messages as $key=>$value){
                if($value['deleted_to_id'] == 0)
                    Message::find($value['id'])->update(['deleted_to_id'=>$api['user']['id']]);
                elseif($value['deleted_to_id'] == $request->input('recipient_id'))
                    Message::find($value['id'])->update(['deleted_to_id'=>-1]);
            }

            return response()->json(["result"=>"success"]);
        }
        return response()->json($api, $api["status_code"]);
    }

    function seenMessages(Request $request){
        $api = $this->authenticateAPI();
        $sender_id  = $request->input('sender_id');
        if($api['result'] === 'success') {
            
             Message::where('sender_id',$sender_id)
                            ->where('recipient_id',  $api['user']['id'])
                            ->update(['read_at'=>date('Y-m-d H:i:s')]);


            return response()->json(["result"=>"success"]);
        }
        return response()->json($api, $api["status_code"]);
    }

    function getUnreadMessages(){
        $api = $this->authenticateAPI();

        if($api['result'] === 'success') {
            return response()->json(Message::where('recipient_id', $api['user']['id'])
                                            ->whereNull('read_at')
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
                ->whereNotNull('read_at')
                ->select( 'body as message','first_name', 'last_name', 'users.id', 'user_picture', 'level_name','is_client')
                ->orderBy('messages.created_at', 'DESC')
                ->get()->first());
        }
        return response()->json($api, $api["status_code"]);
    }
}
