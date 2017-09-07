<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Region;

class RegionController extends Controller{
    public function getRegions(){
        return response()->json(Region::get());
    }

    public function addRegion(Request $request){

    }

    public function updateRegion(Request $request){

    }

    public function deleteRegion(Request $request){

    }
}