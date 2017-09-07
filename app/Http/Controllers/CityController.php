<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\City;

class CityController extends Controller
{
    public function getCities(){
        return response()->json(City::get());
    }

    public function addCity(Request $request){

    }

    public function updateCity(Request $request){

    }

    public function deleteCity(Request $request){

    }
}
