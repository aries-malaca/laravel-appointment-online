<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use DB;
class MobileApiController extends Controller
{

    //with data
	public function SapnuPuas(Request $request){
		return response()->json();
	}
	//get only
	public function SapnuPuas1(){
		return response()->json($user = User::take(100)->get());
	}



}
