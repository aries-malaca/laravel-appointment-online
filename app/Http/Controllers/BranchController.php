<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Branch;
use App\BranchCluster;
use Validator;
use Storage;

class BranchController extends Controller{
    public function getBranches(Request $request){
        if($request->segment(4)== 'active')
            return response()->json(Branch::where('is_active', 1)
                                            ->select('id','branch_name')
                                            ->get());
        return response()->json(Branch::get());
    }

    public function getClusters(){
        return response()->json(BranchCluster::get());
    }

    public function getBranch(Request $request){
        return response()->json(Branch::leftJoin('regions','branches.region_id','=','regions.id')
                        ->leftJoin('cities','branches.city_id','=','cities.id')
                        ->select('branches.*','region_name','city_name')
                        ->where('branches.id',$request->segment(4))
                        ->get()->first());
    }

    public function addBranch(Request $request){
        $api = $this->authenticateAPI();
        if($api['result'] === 'success'){
            $validator = Validator::make($request->all(), [
                'branch_name' => 'required|unique:branches,branch_name|max:255',
                'branch_code' => 'required|unique:branches,branch_code|max:255',
                'branch_classification' => 'required|in:company-owned,franchised',
                'search_id' => 'required|unique:branches,id',
                'region_id' => 'required|not_in:0',
                'city_id' => 'required|not_in:0',
                'branch_address' => 'required|max:255',
                'rooms_count' => 'required|not_in:0',
                'cluster_id' => 'required|not_in:0',
                'branch_email' => 'required|max:255|email',
                'branch_contact' => 'required|max:255',
                'branch_contact_person' => 'required|max:255'
            ]);

            if ($validator->fails()) {
                return response()->json(['result'=>'failed','error'=>$validator->errors()->all()], 400);
            }

            if ($request->input('search_id') < 1) {
                return response()->json(['result'=>'failed','error'=>"Invalid ID"], 400);
            }

            $branch = new Branch;
            $branch->branch_name = $request->input('branch_name');
            $branch->branch_code = $request->input('branch_code');
            $branch->id = $request->input('search_id');
            $branch->branch_classification = $request->input('branch_classification');
            $branch->region_id = $request->input('region_id');
            $branch->city_id = $request->input('city_id');
            $branch->branch_address = $request->input('branch_address');
            $branch->cluster_id = $request->input('cluster_id');
            $branch->rooms_count = $request->input('rooms_count');
            $branch->payment_methods = $request->input('payment_methods');
            $branch->branch_email = $request->input('branch_email');
            $branch->branch_contact = $request->input('branch_contact');
            $branch->branch_contact_person = $request->input('branch_contact_person');
            $branch->social_media_accounts = json_encode($request->input('social_media_accounts'));
            $branch->map_coordinates = json_encode($request->input('map_coordinates'));
            $branch->branch_pictures = json_encode(array("no photo.jpg"));
            $branch->directions = $request->input('directions');
            $branch->welcome_message = $request->input('welcome_message');
            $branch->opening_date = date('Y-m-d',strtotime($request->input('opening_date')));
            $branch->save();

            return response()->json(["result"=>"success"]);
        }
        return response()->json($api, $api["status_code"]);
    }

    public function updateBranch(Request $request){
        $api = $this->authenticateAPI();
        if($api['result'] === 'success'){
            $validator = Validator::make($request->all(), [
                'branch_name' => 'required|unique:branches,branch_name,'.$request->input('id').'|max:255',
                'branch_code' => 'required|unique:branches,branch_code,'.$request->input('id').'|max:255',
                'branch_classification' => 'required|in:company-owned,franchised',
                'search_id' => 'required|unique:branches,id,'.$request->input('id'),
                'region_id' => 'required|not_in:0',
                'city_id' => 'required|not_in:0',
                'branch_address' => 'required|max:255',
                'rooms_count' => 'required|not_in:0',
                'cluster_id' => 'required|not_in:0',
                'branch_email' => 'required|max:255|email',
                'branch_contact' => 'required|max:255',
                'branch_contact_person' => 'required|max:255'
            ]);

            if ($validator->fails()) {
                return response()->json(['result'=>'failed','error'=>$validator->errors()->all()], 400);
            }

            if ($request->input('search_id') < 1) {
                return response()->json(['result'=>'failed','error'=>"Invalid ID"], 400);
            }

            $branch = Branch::find($request->input('id'));
            $branch->branch_name = $request->input('branch_name');
            $branch->branch_code = $request->input('branch_code');
            $branch->id = $request->input('search_id');
            $branch->branch_classification = $request->input('branch_classification');
            $branch->region_id = $request->input('region_id');
            $branch->city_id = $request->input('city_id');
            $branch->branch_address = $request->input('branch_address');
            $branch->cluster_id = $request->input('cluster_id');
            $branch->rooms_count = $request->input('rooms_count');
            $branch->payment_methods = $request->input('payment_methods');
            $branch->branch_email = $request->input('branch_email');
            $branch->branch_contact = $request->input('branch_contact');
            $branch->branch_contact_person = $request->input('branch_contact_person');
            $branch->social_media_accounts = json_encode($request->input('social_media_accounts'));
            $branch->map_coordinates = json_encode($request->input('map_coordinates'));
            $branch->directions = $request->input('directions');
            $branch->welcome_message = $request->input('welcome_message');
            $branch->opening_date = date('Y-m-d',strtotime($request->input('opening_date')));
            $branch->save();

            return response()->json(["result"=>"success"]);
        }
        return response()->json($api, $api["status_code"]);
    }

    public function addCluster(Request $request){
        $api = $this->authenticateAPI();
        if($api['result'] === 'success'){
            $validator = Validator::make($request->all(), [
                'cluster_name' => 'required|unique:branch_clusters,cluster_name|max:255'
            ]);

            if ($validator->fails()) {
                return response()->json(['result'=>'failed','error'=>$validator->errors()->all()], 400);
            }

            $services = array();
            if($request->input('services') !== null)
                foreach($request->input('services') as $value){
                    $services[] = $value['value'];
                }

            $products = array();
            if($request->input('products') !== null)
                foreach($request->input('products') as $value){
                    $products[] = $value['value'];
                }

            $cluster = new BranchCluster;
            $cluster->cluster_name = $request->input('cluster_name');
            $cluster->cluster_owner = $request->input('cluster_owner');
            $cluster->cluster_email = $request->input('cluster_email');
            $cluster->services = json_encode($services);
            $cluster->products = json_encode($products);
            $cluster->is_active = 1;
            $cluster->cluster_data = '{}';
            $cluster->save();
            return response()->json(["result"=>"success"]);
        }
        return response()->json($api, $api["status_code"]);
    }

    public function updateCluster(Request $request){
        $api = $this->authenticateAPI();
        if($api['result'] === 'success'){
            $validator = Validator::make($request->all(), [
                'cluster_name' => 'required|unique:branch_clusters,cluster_name,'.$request->input('id').'|max:255'
            ]);

            if ($validator->fails()) {
                return response()->json(['result'=>'failed','error'=>$validator->errors()->all()], 400);
            }

            $services = array();
            if($request->input('services') !== null)
                foreach($request->input('services') as $value){
                    $services[] = $value['value'];
                }

            $products = array();
            if($request->input('products') !== null)
                foreach($request->input('products') as $value){
                    $products[] = $value['value'];
                }

            $cluster = BranchCluster::find($request->input('id'));
            $cluster->cluster_name = $request->input('cluster_name');
            $cluster->cluster_owner = $request->input('cluster_owner');
            $cluster->cluster_email = $request->input('cluster_email');
            $cluster->services = json_encode($services);
            $cluster->products = json_encode($products);
            $cluster->save();

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
                    $file->move('images/branches/', $request->input('branch_id') . '_' . $timestamp);
                    $branch = Branch::find($request->input('branch_id'));
                    $pics = json_decode($branch->branch_pictures,true);
                    $pics[$request->input('key')] = $request->input('branch_id') . '_' . $timestamp;
                    $branch->branch_pictures = json_encode($pics);
                    $branch->save();

                    return response()->json(["result"=>"success"],200);
                }
                return response()->json(["result"=>"failed","error"=>"Invalid File Format."],400);
            }
            return response()->json(["result"=>"failed","error"=>"No File to be uploaded."], 400);
        }

        return response()->json($api, $api["status_code"]);
    }

    function removePicture(Request $request){
        $api = $this->authenticateAPI();
        if($api['result'] === 'success') {

            $branch = Branch::find($request->input('branch_id'));
            $pics = json_decode($branch->branch_pictures,true);
            $file_name = $pics[$request->input('key')];
            unset($pics[$request->input('key')]);
            $branch->branch_pictures = json_encode($pics);
            if(file_exists(public_path('/images/branches/'.$file_name)))
                unlink(public_path('/images/branches/'.$file_name));
            $branch->save();
            return response()->json(["result"=>"success"],200);
        }
        return response()->json($api, $api["status_code"]);
    }
}