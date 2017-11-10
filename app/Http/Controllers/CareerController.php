<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Career;
use Validator;

class CareerController extends Controller{
    function addCareer(Request $request){
        $api = $this->authenticateAPI();
        if($api['result'] === 'success'){
            $validator = Validator::make($request->all(), [
                'title' => 'required|max:255',
                'career_data.requirements' => 'required',
            ]);

            if ($validator->fails())
                return response()->json(['result'=>'failed','error'=>$validator->errors()->all()], 400);

            $career = new Career;
            $career->title = $request->input('title');
            $career->description = $request->input('description');
            $career->career_data = json_encode($request->input('career_data'));
            $career->order = Career::count()+1;
            $career->save();

            return response()->json(["result"=>"success"]);
        }
        return response()->json($api, $api["status_code"]);
    }

    function updateCareer(Request $request){
        $api = $this->authenticateAPI();
        if($api['result'] === 'success'){
            $validator = Validator::make($request->all(), [
                'title' => 'required|max:255',
                'career_data.requirements' => 'required',
            ]);

            if ($validator->fails())
                return response()->json(['result'=>'failed','error'=>$validator->errors()->all()], 400);

            $career = Career::find($request->input('id'));
            $career->title = $request->input('title');
            $career->description = $request->input('description');
            $career->career_data = json_encode($request->input('career_data'));
            $career->order = Career::count()+1;
            $career->save();

            return response()->json(["result"=>"success"]);
        }
        return response()->json($api, $api["status_code"]);
    }

    function getCareers(){
        $data = Career::orderBy('order')->get()->toArray();

        foreach($data as $key=>$value){
            $data[$key]['career_data'] = json_decode($value['career_data']);
            $data[$key]['date_posted'] = date('m/d/Y',strtotime($value['created_at']));
        }

        return response()->json($data);
    }

    function moveCareer(Request $request){
        $api = $this->authenticateAPI();

        if($api['result'] === 'success'){
            return response()->json(["result"=>"success"]);
        }
        return response()->json($api, $api["status_code"]);
    }

    function deleteCareer(Request $request){
        $api = $this->authenticateAPI();

        if($api['result'] === 'success'){
            Career::destroy($request->input('id'));
            return response()->json(["result"=>"success"]);
        }
        return response()->json($api, $api["status_code"]);
    }
}
