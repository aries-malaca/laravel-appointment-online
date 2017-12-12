<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\City;
use Validator;

class CityController extends Controller{
    public function getCities(){
        return response()->json(City::leftJoin('regions','cities.region_id', '=','regions.id')
                                            ->select('cities.*','region_name')
                                            ->get());
    }

    public function addCity(Request $request){
        $api = $this->authenticateAPI();
        if($api['result'] === 'success'){
            $validator = Validator::make($request->all(), [
                'city_name' => 'required|unique:cities,city_name|max:255',
                'region_id' => 'required|not_in:0'
            ]);

            if ($validator->fails()) {
                return response()->json(['result'=>'failed','error'=>$validator->errors()->all()], 400);
            }

            $city = new City;
            $city->city_name = $request->input('city_name');
            $city->region_id = $request->input('region_id');
            $city->save();

            return response()->json(["result"=>"success"]);
        }
        return response()->json($api, $api["status_code"]);
    }

    public function updateCity(Request $request){
        $api = $this->authenticateAPI();
        if($api['result'] === 'success'){
            $validator = Validator::make($request->all(), [
                'city_name' => 'required|unique:cities,city_name,'.$request->input('id').'|max:255',
                'region_id' => 'required|not_in:0'
            ]);

            if ($validator->fails()) {
                return response()->json(['result'=>'failed','error'=>$validator->errors()->all()], 400);
            }

            $city = City::find($request->input('id'));
            $city->city_name = $request->input('city_name');
            $city->region_id = $request->input('region_id');
            $city->save();

            return response()->json(["result"=>"success"]);
        }
        return response()->json($api, $api["status_code"]);
    }
}
