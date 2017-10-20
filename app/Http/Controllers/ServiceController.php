<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Service;
use App\ServiceType;
use App\ServicePackage;
use Validator;
use Storage;

class ServiceController extends Controller{

    public function getServices(Request $request){
        $services = Service::leftJoin('service_types','services.service_type_id','=','service_types.id')
                        ->leftJoin('service_packages','services.service_package_id','=','service_packages.id')
                        ->select('services.*','service_name','package_name','service_description','service_picture');

        if($request->segment(4)=='active')
            $services = $services->where('services.is_active', 1);

        $services=$services->get()->toArray();

        foreach($services as $key=>$value){
            if($value['service_type_id'] === 0){
                $package_services = ServicePackage::find($value['service_package_id'])['package_services'];
                $services[$key]['service_picture'] = ServiceType::whereIn('id', json_decode($package_services))->pluck('service_picture')->toArray();
                $services[$key]['service_description'] = ServiceType::whereIn('id', json_decode($package_services))->pluck('service_name')->toArray();
            }
        }

        return response()->json($services);
    }

    public function addService(Request $request){
        $api = $this->authenticateAPI();
        if($api['result'] === 'success'){
            $validator = Validator::make($request->all(), [
                'search_id' => 'required|unique:services,id',
                'service_code' => 'required|max:255',
                'service_gender' => 'required|in:male,female,both',
                'service_price' => 'required|numeric',
                'service_minutes' => 'required|numeric'
            ]);
            if ($validator->fails()) {
                return response()->json(['result'=>'failed','error'=>$validator->errors()->all()], 400);
            }

            if($request->input('service_type_id') == 0 && $request->input('service_package_id') == 0){
                return response()->json(['result'=>'failed','error'=>"Type and package should not be empty at the same time"], 400);
            }

            if($request->input('service_type_id') != 0 && $request->input('service_package_id') != 0){
                return response()->json(['result'=>'failed','error'=>"Cannot set package if the service type is selected."], 400);
            }

            if ($request->input('search_id') < 1) {
                return response()->json(['result'=>'failed','error'=>"Invalid ID"], 400);
            }

            $service = new Service;
            $service->id = $request->input('search_id');
            $service->service_code = $request->input('service_code');
            $service->service_price = $request->input('service_price');
            $service->service_gender = $request->input('service_gender');
            $service->service_data = '{}';
            $service->service_minutes = $request->input('service_minutes');
            $service->service_type_id = $request->input('service_type_id');
            $service->service_package_id = $request->input('service_package_id');
            $service->is_active = 1;
            $service->save();

            return response()->json(["result"=>"success"]);
        }
        return response()->json($api, $api["status_code"]);
    }

    public function updateService(Request $request){
        $api = $this->authenticateAPI();
        if($api['result'] === 'success'){
            $validator = Validator::make($request->all(), [
                'search_id' => 'required|unique:services,id,'. $request->input('id'),
                'service_code' => 'required|max:255',
                'service_gender' => 'required|in:male,female,both',
                'service_price' => 'required|numeric',
                'service_minutes' => 'required|numeric'
            ]);
            if ($validator->fails()) {
                return response()->json(['result'=>'failed','error'=>$validator->errors()->all()], 400);
            }

            if($request->input('service_type_id') == 0 && $request->input('service_package_id') == 0){
                return response()->json(['result'=>'failed','error'=>"Type and package should not be empty at the same time"], 400);
            }

            if($request->input('service_type_id') != 0 && $request->input('service_package_id') != 0){
                return response()->json(['result'=>'failed','error'=>"Cannot set package if the service type is selected."], 400);
            }

            if ($request->input('search_id') < 1) {
                return response()->json(['result'=>'failed','error'=>"Invalid ID"], 400);
            }

            $service = Service::find($request->input('id'));
            $service->service_code = $request->input('service_code');
            $service->id = $request->input('search_id');
            $service->service_price = $request->input('service_price');
            $service->service_gender = $request->input('service_gender');
            $service->service_minutes = $request->input('service_minutes');
            $service->service_type_id = $request->input('service_type_id');
            $service->service_package_id = $request->input('service_package_id');
            $service->save();

            return response()->json(["result"=>"success"]);
        }
        return response()->json($api, $api["status_code"]);
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
                    $timestamp = time().'.'.$ext ;
                    $file->move('images/services/', $request->input('service_id') . '_' . $timestamp);
                    $service = ServiceType::find($request->input('service_id'));

                    if($service->service_picture != 'no photo.jpg')
                        if(file_exists(public_path('/images/services/'.$service->service_picture)))
                            unlink(public_path('/images/services/'.$service->service_picture));

                    $service->service_picture = $request->input('service_id') . '_' . $timestamp;
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
            $data = ServicePackage::where('is_active', 1)->get()->toArray();
        else{
            $data = ServicePackage::get()->toArray();
        }

        foreach ($data as $key=>$value){
            $data[$key]['service_list'] = implode(', ',ServiceType::whereIn('id', json_decode($value['package_services']))->pluck('service_name')->toArray());
            $query          = Service::where('service_package_id','=',$data[$key]['id'])
                            ->select('service_minutes')
                            ->get();
            $data[$key]['service_duration'] = $query;
        }

        return response()->json($data);
    }

    public function addServicePackage(Request $request){
        $api = $this->authenticateAPI();
        if($api['result'] === 'success'){
            $validator = Validator::make($request->all(), [
                'package_name' => 'required|max:255|unique:service_packages,package_name'
            ]);
            if ($validator->fails()) {
                return response()->json(['result'=>'failed','error'=>$validator->errors()->all()], 400);
            }

            if(sizeof($request->input('package_services')) < 2){
                return response()->json(['result'=>'failed','error'=>'Select at least 2 services.'], 400);
            }

            $services = array();
            foreach($request->input('package_services') as $value){
                $services[] = $value['value'];
            }

            $service = new ServicePackage;
            $service->package_name = $request->input('package_name');
            $service->package_services = json_encode($services);
            $service->is_active = 1;
            $service->save();

            return response()->json(["result"=>"success"]);
        }
        return response()->json($api, $api["status_code"]);
    }

    public function updateServicePackage(Request $request){
        $api = $this->authenticateAPI();
        if($api['result'] === 'success'){
            $validator = Validator::make($request->all(), [
                'package_name' => 'required|max:255|unique:service_packages,package_name,'. $request->input('id')
            ]);
            if ($validator->fails()) {
                return response()->json(['result'=>'failed','error'=>$validator->errors()->all()], 400);
            }

            if(sizeof($request->input('package_services')) < 2){
                return response()->json(['result'=>'failed','error'=>'Select at least 2 services.'], 400);
            }

            $services = array();
            foreach($request->input('package_services') as $value){
                $services[] = $value['value'];
            }

            $service = ServicePackage::find($request->input('id'));
            $service->package_name = $request->input('package_name');
            $service->package_services = json_encode($services);
            $service->save();

            return response()->json(["result"=>"success"]);
        }
        return response()->json($api, $api["status_code"]);
    }
}
