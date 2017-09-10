<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Service;
use App\ServiceType;
use App\ServicePackage;
use Validator;

class ServiceController extends Controller{

    public function getServices(Request $request){
        if($request->segment(4)=='active')
            return response()->json(Service::where('is_active', 1)->get());

        return response()->json(Service::get());
    }

    public function addService(Request $request){

    }

    public function updateService(Request $request){

    }



    public function getServiceTypes(Request $request){
        if($request->segment(4)=='active')
            return response()->json(ServiceType::where('is_active', 1)->get());

        return response()->json(ServiceType::get());
    }

    public function addServiceType(Request $request){
        $api = $this->authenticateAPI();
        if($api['result'] === 'success'){
            $validator = Validator::make($request->all(), [
                'service_name' => 'required|max:255',
                'service_description' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json(['result'=>'failed','error'=>$validator->errors()->all()], 400);
            }

            $service = new ServiceType;
            $service->service_name = $request->input('service_name');
            $service->service_description = $request->input('service_description');
            $service->is_active = 1;
            $service->service_picture = 'no photo.jpg';
            $service->save();

            return response()->json(["result"=>"success"]);
        }
        return response()->json($api, $api["status_code"]);
    }

    public function updateServiceType(Request $request){
        $api = $this->authenticateAPI();
        if($api['result'] === 'success'){
            $validator = Validator::make($request->all(), [
                'service_name' => 'required|max:255',
                'service_description' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json(['result'=>'failed','error'=>$validator->errors()->all()], 400);
            }

            $service = ServiceType::find($request->input('id'));
            $service->service_name = $request->input('service_name');
            $service->service_description = $request->input('service_description');
            $service->save();

            return response()->json(["result"=>"success"]);
        }
        return response()->json($api, $api["status_code"]);
    }

    public function uploadPicture(Request $request){
        $api = $this->authenticateAPI();
        if($api['result'] === 'success') {
            //valid extensions
            $valid_ext = array('jpeg', 'gif', 'png', 'jpg');
            //check if the file is submitted
            if ($request->hasFile('file')) {
                $file = $request->file('file');
                $ext = $file->getClientOriginalExtension();

                //check if extension is valid
                if (in_array($ext, $valid_ext)) {
                    $file->move('images/services/', $request->input('service_id') . '_' . $file->getClientOriginalName());
                    $service = ServiceType::find($request->input('service_id'));
                    $service->service_picture = $request->input('service_id') . '_' . $file->getClientOriginalName();
                    $service->save();
                    return response()->json(["result"=>"success"],200);
                }
                return response()->json(["result"=>"failed","error"=>"Invalid File Format."],400);
            }
            return response()->json(["result"=>"failed","error"=>"No File to be uploaded."], 400);
        }

        return response()->json($api, $api["status_code"]);
    }




    public function getServicePackages(Request $request){
        if($request->segment(4)=='active')
            return response()->json(ServicePackage::where('is_active', 1)->get());

        return response()->json(ServicePackage::get());
    }

    public function addServicePackage(Request $request){

    }

    public function updateServicePackage(Request $request){

    }
}
