<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Promotion;
use App\User;
use Validator;

class PromotionController extends Controller{
    function getPromotions(){
        $data = Promotion::get()->toArray();

        foreach($data as $key=>$value){
            $user = User::find($value['posted_by_id']);
            $username = isset($user->id)?$user->first_name .' ' . $user->last_name:'';
            $data[$key]['branches'] = json_decode($value['branches']);
            $data[$key]['posted_by_name'] = $username;
        }

        return response()->json($data);
    }
}
