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
        return response()->json(Branch::find($request->segment(4)));
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
            Storage::disk('public')->delete('images/branches/'.$file_name);

            $branch->save();
            return response()->json(["result"=>"success"],200);
        }
        return response()->json($api, $api["status_code"]);
    }
}