<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Branch;
use App\BranchCluster;
use Validator;

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
}