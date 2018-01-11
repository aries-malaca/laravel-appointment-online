<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Region;
use Validator;

class RegionController extends Controller{
    public function getRegions(){
        return response()->json(Region::orderBy('region_order')->get());
    }

    public function addRegion(Request $request){
        $api = $this->authenticateAPI();
        if($api['result'] === 'success'){
            $validator = Validator::make($request->all(), [
                'region_name' => 'required|unique:regions,region_name|max:255',
            ]);

            if ($validator->fails()) {
                return response()->json(['result'=>'failed','error'=>$validator->errors()->all()], 400);
            }

            $region = new Region;
            $region->region_name = $request->input('region_name');
            $region->save();

            return response()->json(["result"=>"success"]);
        }
        return response()->json($api, $api["status_code"]);
    }

    public function updateRegion(Request $request){
        $api = $this->authenticateAPI();
        if($api['result'] === 'success'){
            $validator = Validator::make($request->all(), [
                'region_name' => 'required|unique:regions,region_name,'.$request->input('id').'|max:255',
            ]);

            if ($validator->fails()) {
                return response()->json(['result'=>'failed','error'=>$validator->errors()->all()], 400);
            }

            $region = Region::find($request->input('id'));
            $region->region_name = $request->input('region_name');
            $region->save();

            return response()->json(["result"=>"success"]);
        }
        return response()->json($api, $api["status_code"]);
    }
}