<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\TextMessage;
use App\TextTemplate;
use Validator;

class CampaignController extends Controller{

    function getTemplates(){
        $data = TextTemplate::get()->toArray();
        foreach($data as $key=>$value)
            $data[$key]['parameters'] = json_decode($value['parameters']);

        return response()->json($data);
    }

    function sendCampaign(Request $request){
        $validator = Validator::make($request->all(), [
            'message' => 'required',
            'title' => 'required_if:send_via,email|required_if:send_via,sms+email',
            'recipient.mobile' => 'required',
            'recipient' => 'required',
        ]);

        if ($validator->fails())
            return response()->json(['result'=>'failed','error'=>$validator->errors()->all()], 400);

        $api = $this->authenticateAPI();
        if($api['result'] === 'success') {
            $recipient = $request->input('recipient');
            $mobile = $this->convertMobile($recipient['mobile']);
            $title = $request->input('title');

            if(isset($request->input('message')['body'])) {
                $message = $this->parseMessage($request->input('message')['body'], $recipient, $request->input('message')['parameters']);
                if($message ===false)
                    return response()->json(['result'=>'failed','error'=>"Cannot parse message, make sure parameters are correct."], 400);
            }
            else
                $message = $request->input('message');

            if($request->input('send_via')=='email' && $request->input('flag')!='preview'){
                $content_data = ["recipient"=>$recipient, "content"=>$this->makeHyperlink($message), "subject"=>$title];
                $headers = array("subject"=>$title,
                                 "to"=> [["email"=>$recipient['email'], "name"=> $recipient['first_name']]]);

                if($request->input('disable_content') !== false)
                    $template = 'email.blank';
                else
                    $template = 'email.common';

                $this->sendMail($template, $content_data, $headers, $request->input('attachments'));

                return response()->json([
                    "result"=>"success",
                    "recipient"=>$recipient,
                    "title"=>$title,
                    "sent_message"=> $message,
                    "message"=>"Email Successfully sent to " . $recipient['first_name'] .' '. $recipient['last_name'],
                ]);
            }

            if(!$this->isValidMobile($mobile))
                return response()->json(['result'=>'failed','error'=>'Invalid mobile number.'], 400);

            if($this->messageSentAlready($mobile, $message) && $request->input('force_sending')!==true)
                return response()->json(['result'=>'failed','error'=>'Already sent.'], 400);

            if($request->input('flag') == 'preview')
                return response()->json(['message'=>$message]);

            if($response = $this->sendSMS(strip_tags($message), $mobile, $title, $api, $this->getGlobeShortCode($mobile) )){
                return response()->json([
                    "result"=>"success",
                    "recipient"=>$recipient,
                    "title"=>$title,
                    "sent_message"=> strip_tags($message),
                    "message"=>"SMS Successfully sent to " . $recipient['first_name'] .' '. $recipient['last_name'],
                    "request_send_mail"=> $request->input('send_via') == 'sms+email',
                ]);
            }

            return response()->json(['result'=>'failed', 'error'=>'No campaign were sent.'], 400);
        }
        return response()->json($api, $api["status_code"]);
    }

    function isValidMobile($mobile){
        $prefix = substr($mobile, 0, 4);
        return (preg_match('/^[0-9]{11}+$/', $mobile) && in_array($prefix, config('app.valid_prefixes')));
    }

    function convertMobile($mobile){
        return str_replace('+63', '0', $mobile);
    }

    function messageSentAlready($mobile, $message){
        return TextMessage::where('recipient', $mobile)
                        ->where('message', $message)
                        ->count() >0;
    }

    function parseMessage($message, $recipient, $fields){
        foreach($fields as $field)
            if(isset($recipient[$field]))
                $message = str_replace('{'. $field .'}', $recipient[$field], $message);

        return $message;
    }

    function makeHyperlink($string){
        $url = '@(http)?(s)?(://)?(([a-zA-Z])([-\w]+\.)+([^\s\.]+[^\s]*)+[^,.\s])@';
        $string = preg_replace($url, '<a href="http$2://$4" target="_blank" title="$0">$0</a>', $string);
        return $string;
    }

    function getGlobeShortCode($number){
        //if(!in_array($number, config('app.home_network_prefixes') ))
            //return env('GLOBE_API_SHORT_CODE_CROSS_TELCO');

        return env('GLOBE_API_SHORT_CODE');
    }

    function addTemplate(Request $request){
        $api = $this->authenticateAPI();
        if($api['result'] === 'success') {
            $validator = Validator::make($request->all(), [
                'name' => 'required|max:255',
                'body' => 'required',
            ]);

            if ($validator->fails())
                return response()->json(['result'=>'failed','error'=>$validator->errors()->all()], 400);

            $template = new TextTemplate;
            $template->name = $request->input('name');
            $template->body = $request->input('body');
            $template->parameters = json_encode($request->input('parameters'));
            $template->save();

            return response()->json(["result"=>"success"]);
        }

        return response()->json($api, $api["status_code"]);
    }

    function updateTemplate(Request $request){
        $api = $this->authenticateAPI();
        if($api['result'] === 'success') {
            $validator = Validator::make($request->all(), [
                'name' => 'required|max:255',
                'body' => 'required',
            ]);

            if ($validator->fails())
                return response()->json(['result'=>'failed','error'=>$validator->errors()->all()], 400);

            $template = TextTemplate::find($request->input('id'));
            $template->name = $request->input('name');
            $template->body = $request->input('body');
            $template->parameters = json_encode($request->input('parameters'));
            $template->save();

            return response()->json(["result"=>"success"]);
        }

        return response()->json($api, $api["status_code"]);
    }

    function deleteTemplate(Request $request){
        $api = $this->authenticateAPI();
        if($api['result'] === 'success') {
            TextTemplate::destroy($request->input('id'));
            return response()->json(["result"=>"success"]);
        }

        return response()->json($api, $api["status_code"]);
    }

    function uploadFile(Request $request){
        $api = $this->authenticateAPI();
        if($api['result'] === 'success') {
            //check if the file is submitted
            if ($request->hasFile('file')) {
                $file = $request->file('file');
                $name = $file->getClientOriginalName() ;
                if(!in_array($file->getClientOriginalExtension(),['docx','doc','pdf','jpg','jpeg','gif','png','xls','xlsx']))
                    return response()->json(["result"=>"failed","error"=>"File not supported."], 400);
                if($file->getClientSize() > 5120000)
                    return response()->json(["result"=>"failed","error"=>"File size exceeds the maximum of 5MB."], 400);

                $file->move('files/blast/', $name);

                return response()->json(["result"=>"success", "filename"=>$name, "size"=>$file->getClientSize()],200);
            }
            return response()->json(["result"=>"failed","error"=>"No File to be uploaded."], 400);
        }
        return response()->json($api, $api["status_code"]);
    }

    function removeFile(Request $request){
        $api = $this->authenticateAPI();
        if($api['result'] === 'success') {
            if(file_exists(public_path('/files/blast/'.$request->input('filename'))))
                unlink(public_path('/files/blast/'.$request->input('filename')));

            return response()->json(["result"=>"success"],200);
        }
        return response()->json($api, $api["status_code"]);
    }
}