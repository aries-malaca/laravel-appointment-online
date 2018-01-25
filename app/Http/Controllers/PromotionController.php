<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Promotion;
use App\Perk;
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
            $data[$key]['promotions_data'] = json_decode($value['promotions_data']);
        }

        return response()->json($data);
    }

    function getPerks(){
        $data = Perk::get()->toArray();

        foreach($data as $key=>$value){
            $data[$key]['perk_data'] = json_decode($value['perk_data']);
        }

        return response()->json($data);
    }

    function getSurveys(){
        return array();
    }

    function addPromotion(Request $request){
        $api = $this->authenticateAPI();

        if($api['result'] === 'success'){
            $validator = Validator::make($request->all(), [
                'title' => 'required|max:255',
                'type' => 'required|in:promo,display',
            ]);

            $promo = new Promotion;
            $promo->title = $request->input('title');
            $promo->type = $request->input('type');
            $promo->description = $request->input('description');
            $promo->promo_picture = 'no photo.jpg';
            $promo->date_start = $request->input('date_start');
            $promo->date_end = $request->input('date_end');
            $promo->branches = '[0]';
            $promo->is_active = 1;
            $promo->promotions_data = '{}';
            $promo->posted_by_id = $api['user']['id'];
            $promo->save();

            if ($validator->fails())
                return response()->json(['result'=>'failed','error'=>$validator->errors()->all()], 400);

            return response()->json(["result"=>"success"]);
        }
        return response()->json($api, $api["status_code"]);
    }

    function updatePromotion(Request $request){
        $api = $this->authenticateAPI();

        if($api['result'] === 'success'){
            $validator = Validator::make($request->all(), [
                'title' => 'required|max:255',
                'type' => 'required|in:promo,display',
            ]);

            $promo = Promotion::find($request->input('id'));
            $promo->title = $request->input('title');
            $promo->type = $request->input('type');
            $promo->description = $request->input('description');
            $promo->date_start = $request->input('date_start');
            $promo->date_end = $request->input('date_end');
            $promo->branches = '[0]';
            $promo->is_active = 1;
            $promo->promotions_data = '{}';
            $promo->posted_by_id = $api['user']['id'];
            $promo->save();

            if ($validator->fails())
                return response()->json(['result'=>'failed','error'=>$validator->errors()->all()], 400);

            return response()->json(["result"=>"success"]);
        }
        return response()->json($api, $api["status_code"]);
    }

    function addPerk(Request $request){
        $api = $this->authenticateAPI();

        if($api['result'] === 'success'){
            $validator = Validator::make($request->all(), [
                'perk_name' => 'required|max:255',
                'perk_data.classname' => 'required',
            ]);

            $perk = new Perk;
            $perk->perk_name = $request->input('perk_name');
            $perk->perk_description = $request->input('perk_description');
            $perk->perk_data = json_encode($request->input('perk_data'));
            $perk->perk_order = 0;
            $perk->perk_picture = 'no photo.jpg';
            $perk->save();

            if ($validator->fails())
                return response()->json(['result'=>'failed','error'=>$validator->errors()->all()], 400);

            return response()->json(["result"=>"success"]);
        }
        return response()->json($api, $api["status_code"]);
    }

    function updatePerk(Request $request){
        $api = $this->authenticateAPI();

        if($api['result'] === 'success'){
            $validator = Validator::make($request->all(), [
                'perk_name' => 'required|max:255',
                'perk_data.classname' => 'required',
            ]);

            $perk = Perk::find($request->input('id'));
            $perk->perk_name = $request->input('perk_name');
            $perk->perk_description = $request->input('perk_description');
            $perk->perk_data = json_encode($request->input('perk_data'));
            $perk->save();

            if ($validator->fails())
                return response()->json(['result'=>'failed','error'=>$validator->errors()->all()], 400);

            return response()->json(["result"=>"success"]);
        }
        return response()->json($api, $api["status_code"]);
    }

    function addSurvey(Request $request){

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


                    if($request->input('promo_id') !== null){
                        $file->move('images/promotions/', $request->input('promo_id') . '_' . $timestamp);
                        $promo = Promotion::find($request->input('promo_id'));

                        if($promo->promo_picture != 'no photo.jpg')
                            if(file_exists(public_path('/images/promotions/'.$promo->promo_picture)))
                                unlink(public_path('/images/promotions/'.$promo->promo_picture));

                        $promo->promo_picture = $request->input('promo_id') . '_' . $timestamp;
                        $promo->save();
                    }
                    else{
                        $file->move('images/perks/', $request->input('perk_id') . '_' . $timestamp);
                        $perk = Perk::find($request->input('perk_id'));

                        if($perk->perk_picture != 'no photo.jpg')
                            if(file_exists(public_path('/images/perks/'.$perk->perk_picture)))
                                unlink(public_path('/images/perks/'.$perk->perk_picture));

                        $perk->perk_picture = $request->input('perk_id') . '_' . $timestamp;
                        $perk->save();
                    }




                    return response()->json(["result"=>"success"],200);
                }
                return response()->json(["result"=>"failed","error"=>"Invalid File Format."],400);
            }
            return response()->json(["result"=>"failed","error"=>"No File to be uploaded."], 400);
        }

        return response()->json($api, $api["status_code"]);
    }
}
