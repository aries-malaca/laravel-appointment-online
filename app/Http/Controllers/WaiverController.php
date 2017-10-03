<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\WaiverQuestion;

class WaiverController extends Controller{
    public function getWaiverQuestions(){
        $data = WaiverQuestion::get()->toArray();
        foreach($data as $key=>$value){
            $data[$key]['question_data'] = json_decode($value['question_data']);
        }

        return response()->json($data);
    }
}
