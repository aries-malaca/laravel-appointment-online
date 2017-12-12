<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Faq;
use Validator;

class FAQController extends Controller{
    function addFAQ(Request $request){
        $api = $this->authenticateAPI();
        if($api['result'] === 'success'){
            $validator = Validator::make($request->all(), [
                'question' => 'required|max:255',
                'answer' => 'required',
            ]);

            if ($validator->fails())
                return response()->json(['result'=>'failed','error'=>$validator->errors()->all()], 400);

            $faq = new Faq;
            $faq->question = $request->input('question');
            $faq->answer = $request->input('answer');
            $faq->order = Faq::count()+1;
            $faq->save();

            return response()->json(["result"=>"success"]);
        }
        return response()->json($api, $api["status_code"]);
    }

    function updateFAQ(Request $request){
        $api = $this->authenticateAPI();
        if($api['result'] === 'success'){
            $validator = Validator::make($request->all(), [
                'question' => 'required|max:255',
                'answer' => 'required'
            ]);

            if ($validator->fails())
                return response()->json(['result'=>'failed','error'=>$validator->errors()->all()], 400);

            $faq = Faq::find($request->input('id'));
            $faq->question = $request->input('question');
            $faq->answer = $request->input('answer');
            $faq->save();

            return response()->json(["result"=>"success"]);
        }
        return response()->json($api, $api["status_code"]);
    }

    function getFAQs(){
        $data = Faq::orderBy('order')->get()->toArray();

        foreach($data as $key=>$value){
            $data[$key]['answer']   = 'Answer: '. nl2br( $value['answer']);
        }
        $response['questions'] = $data;
        $response['category']  = $this->getFAQCategory();
        return response()->json($response);
    }

    function moveFAQ(Request $request){
        $api = $this->authenticateAPI();

        if($api['result'] === 'success'){
            return response()->json(["result"=>"success"]);
        }
        return response()->json($api, $api["status_code"]);
    }

    function deleteFAQ(Request $request){
        $api = $this->authenticateAPI();

        if($api['result'] === 'success'){
            Faq::destroy($request->input('id'));
            return response()->json(["result"=>"success"]);
        }
        return response()->json($api, $api["status_code"]);
    }

    function getFAQCategory(){
        $array_res = array(
            array(
                "image"         => "images/a_plc.png",
                "title"         => "Premiere Loyalty Card",
                "category_id"   => 1
            ),
            array(
                "image" => "images/a_booking_faq.png",
                "title" => "Appointment Booking",
                "category_id"   => 2
            ),
            array(
                "image" => "images/a_wax.png",
                "title" => "About Waxing",
                "category_id"   => 3
            ),
            array(
                "image" => "images/a_transaction_faq.png",
                "title" => "Transaction History",
                "category_id"   => 4
            )
        );
     
        return $array_res;
    }


}
