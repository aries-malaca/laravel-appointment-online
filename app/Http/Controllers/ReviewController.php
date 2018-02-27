<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Review;

class ReviewController extends Controller{
    function getReviews(Request $request){
        $data = Review::leftJoin('transactions', 'reviews.transaction_id', '=', 'transactions.id');

        if($request->segment(4)=='branch')
            $data = $data->where('branch_id', $request->segment(5));
        elseif($request->segment(4) == 'technician')
            $data = $data->where('branch_id', $request->segment(5));

        $data = $data->select('branch_id', 'technician_id', 'reviews.*')
                        ->orderBy('rating', 'DESC')
                        ->get()->toArray();

        return response()->json($data);
    }

    function submitReview(Request $request){
        $api = $this->authenticateAPI();
        if($api['result'] === 'success') {

            $find = Review::where('transaction_id', $request->input('appointment_id'))
                ->get()->toArray();
            if(sizeof($find) > 0){
                Review::where('transaction_id', $request->input('appointment_id'))
                    ->update(['rating'=>$request->input('rating'),
                        'feedback'=>$request->input('feedback'),
                    ]);
            }
            else{
                $review = new Review;
                $review->rating = $request->input('rating');
                $review->feedback = $request->input('feedback');
                $review->transaction_id = $request->input('appointment_id');
                $review->review_status = 'pending';
                $review->save();
            }

            return response()->json(['result'=>'success']);
        }

        return response()->json($api, $api["status_code"]);
    }
}