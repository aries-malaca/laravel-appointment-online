<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Service;
use App\ServiceType;
use App\ServicePackage;

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

    }

    public function updateServiceType(Request $request){

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
