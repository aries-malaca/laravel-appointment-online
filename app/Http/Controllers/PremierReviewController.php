<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\PlcReviewRequest;
use App\User;
use Validator;

class PremierReviewController extends Controller{
    function sendReviewRequest(Request $request){
        $validator = Validator::make($request->all(), [
            'message' => 'required',
        ]);

        if ($validator->fails())
            return response()->json(['result'=>'failed','error'=>$validator->errors()->all()], 400);

        $api = $this->authenticateAPI();
        if($api['result'] === 'success'){

            if(PlcReviewRequest::where('client_id', $api['user']['id'])
                    ->where('status', 'pending')
                    ->count() > 0)
                return response()->json(['result'=>'failed', 'error'=>'Already have pending request.'],400);

            if($request->input('valid_id_url') === null)
                return response()->json(["result"=>"failed","error"=>"No ID to be uploaded."], 400);

            $data = $request->input('valid_id_url');
            list($type, $data) = explode(';',$data);
            list(,$data) = explode(',', $data);

            if($type == 'data:image/jpeg')
                $ext = 'jpg';
            elseif($type == 'data:image/png')
                $ext = 'png';
            else
                return response()->json(["result"=>"failed","error"=>"Invalid File Format."],400);

            $filename = $api['user']['id'] . '_' . time().'.'.$ext ;
            $data = base64_decode($data);
            file_put_contents(public_path('images/ids/'). $filename, $data );

            $review = new PlcReviewRequest;
            $review->client_id = $api['user']['id'];
            $review->status = 'pending';
            $review->plc_review_request_data = '{}';
            $review->message = $request->input('message');
            $review->valid_id_url = $filename;
            $review->save();

            $this->sendMail('email.transaction_review_request',
                ["user"=>$api['user']],
                ["subject"=> env("APP_NAME"). " - Transaction Review",
                    "to"=>[["email"=>$api['user']['email'],"name"=> $api['user']['first_name'] . ' ' . $api['user']['last_name']]]
                ]
            );

            return response()->json(["result"=>"success"]);
        }
        return response()->json($api, $api["status_code"]);
    }

    function getRequests(){
        $api = $this->authenticateAPI();
        if($api['result'] === 'success'){
            $data = PlcReviewRequest::where('client_id', $api['user']['id'])
                                    ->get()->toArray();

            foreach($data as $key=>$value){
                $client = User::find($value['client_id']);
                $data[$key]['name'] = ($client->id?$client->username:'');
                $data[$key]['status_html'] = '<span class="badge '.($value['status']=='pending'?'badge-info':'badge-success').'">'. $value['status'] .'</span>';
                $user = User::find($value['updated_by_id']);
                $data[$key]['updated_by'] =  (isset($user->id)?$user->username:'');
                $data[$key]['processed_date_formatted'] =  isset($value['processed_date'])?date('m/d/Y',strtotime($value['processed_date'])):'';
            }
            return response()->json($data);
        }
        return response()->json($api, $api["status_code"]);
    }

    function getAllRequests(){
        $data = PlcReviewRequest::get()->toArray();

        foreach($data as $key=>$value){
            $client = User::find($value['client_id']);
            $data[$key]['name'] = ($client->id?$client->username:'');
            $data[$key]['status_html'] = '<span class="badge '.($value['status']=='pending'?'badge-info':'badge-success').'">'. $value['status'] .'</span>';
            $user = User::find($value['updated_by_id']);
            $data[$key]['updated_by'] =  (isset($user->id)?$user->username:'');
            $data[$key]['processed_date_formatted'] =  isset($value['processed_date'])?date('m/d/Y',strtotime($value['processed_date'])):'';
        }

        return response()->json($data);
    }

    function completeRequest(Request $request){
        $validator = Validator::make($request->all(), [
            'remarks' => 'required',
        ]);

        if ($validator->fails())
            return response()->json(['result'=>'failed','error'=>$validator->errors()->all()], 400);


        $api = $this->authenticateAPI();
        if($api['result'] === 'success'){
            $review = PlcReviewRequest::find($request->input('id'));
            $review->remarks = $request->input('remarks');
            $review->processed_date = date('Y-m-d');
            $review->status = 'completed';
            $review->updated_by_id = $api['user']['id'];
            $review->save();

            return response()->json(['result'=>'success']);
        }
        return response()->json($api, $api["status_code"]);
    }

    function deleteRequest(Request $request){
        $api = $this->authenticateAPI();
        if($api['result'] === 'success'){
            PlcReviewRequest::destroy($request->input('id'));
            return response()->json(['result'=>'success']);
        }
        return response()->json($api, $api["status_code"]);
    }
}
