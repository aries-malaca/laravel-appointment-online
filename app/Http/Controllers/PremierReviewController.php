<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\PlcReviewRequest;

class PremierReviewController extends Controller{
    function sendReviewRequest(Request $request){
        $api = $this->authenticateAPI();
        if($api['result'] === 'success'){

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
}
