<?php

namespace App\Http\Controllers;

use Validator;
use Illuminate\Http\Request;
use App\UserLevel;

class UserLevelController extends Controller{

    public function getUserLevels(){
        return response()->json(UserLevel::get()->toArray());
    }

    public function addUserLevel(Request $request){
        $api = $this->authenticateAPI();
        if($api['result'] === 'success'){
            $validator = Validator::make($request->all(), [
                'level_name' => 'required|max:255|unique:user_levels,level_name',
                'description' => 'max:255',
            ]);
            if ($validator->fails()) {
                return response()->json(['result'=>'failed','error'=>$validator->errors()->all()], 400);
            }

            $level = new UserLevel;
            $level->level_name = $request->input('level_name');
            $level->description = $request->input('description');
            $level->level_data = '{}';
            $level->is_active = 1;
            $level->save();

            return response()->json(["result"=>"success"]);
        }

        return response()->json($api, $api["status_code"]);
    }

    public function updateUserLevel(Request $request){
        $api = $this->authenticateAPI();
        if($api['result'] === 'success'){
            $validator = Validator::make($request->all(), [
                'level_name' => 'required|max:255|unique:user_levels,level_name,' . $request->input('id'),
                'description' => 'max:255',
            ]);
            if ($validator->fails()) {
                return response()->json(['result'=>'failed','error'=>$validator->errors()->all()], 400);
            }

            $level = UserLevel::find($request->input('id'));
            $level->level_name = $request->input('level_name');
            $level->description = $request->input('description');
            $level->save();

            return response()->json(["result"=>"success"]);
        }

        return response()->json($api, $api["status_code"]);
    }
}