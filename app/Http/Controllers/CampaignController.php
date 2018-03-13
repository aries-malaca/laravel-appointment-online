<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\TextMessage;
use App\TextTemplate;
use Curl;
use Validator;
use Illuminate\Support\Facades\Storage;

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

            if(isset($request->input('message')['body']))
                $message = $this->parseMessage($request->input('message')['body'], $recipient, $request->input('message')['parameters']);
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

            if($response = $this->sendSMS($message, $mobile, $title, $api, $this->getGlobeShortCode($mobile) )){
                return response()->json([
                    "result"=>"success",
                    "recipient"=>$recipient,
                    "title"=>$title,
                    "sent_message"=> $message,
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
            $message = str_replace('{'. $field .'}', $recipient[$field], $message);

        return $message;
    }

    function makeHyperlink($string){
        $url = '@(http)?(s)?(://)?(([a-zA-Z])([-\w]+\.)+([^\s\.]+[^\s]*)+[^,.\s])@';
        $string = preg_replace($url, '<a href="http$2://$4" target="_blank" title="$0">$0</a>', $string);
        return $string;
    }

    function getGlobeShortCode($number){
        if(!in_array($number, config('app.home_network_prefixes') ))
            //return env('GLOBE_API_SHORT_CODE_CROSS_TELCO');

        return env('GLOBE_API_SHORT_CODE');
    }

    function getAttachments(){
        $files = Storage::disk('public_root')->files('images/blast');
        return response()->json($files);
    }
}