<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Menu;

class MenuController extends Controller{
    function getAllMenus(){
        return response()->json(Menu::get()->toArray());
    }
}
