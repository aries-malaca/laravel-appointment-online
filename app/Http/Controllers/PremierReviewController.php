<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\PlcReviewRequest;
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

            $review = new PlcReviewRequest;
            $review->client_id = $api['user']['id'];
            $review->status = 'pending';
            $review->plc_review_request_data = '{}';
            $review->message = $request->input('message');
            $review->valid_id_image = $request->input('valid_id_image');
            $review->save();

            return response()->json(["result"=>"success"]);
        }
        return response()->json($api, $api["status_code"]);
    }

    function getRequests(){
        $api = $this->authenticateAPI();
        if($api['result'] === 'success'){
            $data = PlcReviewRequest::where('client_id', $api['user']['id'])
                                    ->get()->toArray();
            return response()->json($data);
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
