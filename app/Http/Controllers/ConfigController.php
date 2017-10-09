<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Config;
class ConfigController extends Controller{
    public function getTerms(){
        return response()->json(Config::find(3)->config_value);
    }

    public function getConfigs(){
        return response()->json(Config::get()->toArray());
    }
}
