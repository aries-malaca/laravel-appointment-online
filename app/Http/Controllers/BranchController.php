<?php

namespace App\Http\Controllers;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Branch;
use App\BranchCluster;
use App\BranchSchedule;
use Validator;
use Storage;

class BranchController extends Controller{
    public function getBranches(Request $request){
        if($request->segment(4)== 'active') {
            $data = Branch::leftJoin('branch_clusters','branches.cluster_id','=','branch_clusters.id')
                ->where('branches.is_active', 1)
                ->select('branches.id as id','branch_name','rooms_count','cluster_data')
                ->orderBy('branch_name', 'asc')
                ->get()->toArray();

            foreach($data as $key=>$value){
                $data[$key]['cluster_data'] = json_decode($value['cluster_data']);
                $data[$key]['schedules'] = BranchSchedule::where('branch_id', $value['id'])
                                                            ->orderBy('schedule_type')
                                                            ->get()->toArray();

                foreach($data[$key]['schedules'] as $k=>$v){
                    $data[$key]['schedules'][$k]['schedule_data'] = json_decode($v['schedule_data']);
                }
            }

            return response()->json($data);
        }

        return response()->json(Branch::get());
    }

    public function getClusters(){
        $data = BranchCluster::get()->toArray();

        foreach($data as $key=>$value)
            $data[$key]['cluster_data'] = json_decode($value['cluster_data']);

        return response()->json($data);
    }

    public function getBranch(Request $request){
        $data = Branch::leftJoin('regions','branches.region_id','=','regions.id')
                        ->leftJoin('cities','branches.city_id','=','cities.id')
                        ->leftJoin('branch_clusters','branches.cluster_id','=','branch_clusters.id')
                        ->select('branches.*','region_name','city_name','cluster_data')
                        ->where('branches.id',$request->segment(4))
                        ->get()->first()->toArray();

        $data['branch_pictures'] = json_decode($data['branch_pictures']);
        $data['branch_data'] = json_decode($data['branch_data']);
        $data['map_coordinates'] = json_decode($data['map_coordinates']);
        $data['social_media_accounts'] = json_decode($data['social_media_accounts']);
        $data['cluster_data'] = json_decode($data['cluster_data']);
        $data['schedules'] = BranchSchedule::where('branch_id', $request->segment(4))
                                            ->orderBy('schedule_type')
                                            ->get()->toArray();
        foreach($data['schedules'] as $key=>$value){
            $data['schedules'][$key]['schedule_data'] = json_decode($value['schedule_data']);
        }

        return response()->json($data);
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

            if ($validator->fails())
                return response()->json(['result'=>'failed','error'=>$validator->errors()->all()], 400);

            if ($request->input('search_id') < 1)
                return response()->json(['result'=>'failed','error'=>"Invalid ID"], 400);

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
            $branch->branch_pictures = json_encode(array());
            $branch->directions = $request->input('directions');
            $branch->welcome_message = $request->input('welcome_message');
            $branch->branch_data = json_encode($request->input('branch_data'));
            $branch->opening_date = date('Y-m-d',strtotime($request->input('opening_date')));
            $branch->is_active = 1;
            $branch->save();

            $schedule = new BranchSchedule;
            $schedule->branch_id = $branch->id;
            $schedule->date_start = date('Y-m-d');
            $schedule->date_end = date('Y-m-d');
            $schedule->schedule_data = json_encode(array(
                                                ["start"=>"09:00","end"=>"20:00"],
                                                ["start"=>"09:00","end"=>"20:00"],
                                                ["start"=>"09:00","end"=>"20:00"],
                                                ["start"=>"09:00","end"=>"20:00"],
                                                ["start"=>"09:00","end"=>"20:00"],
                                                ["start"=>"09:00","end"=>"20:00"],
                                                ["start"=>"09:00","end"=>"20:00"]
                                            ));
            $schedule->schedule_type= 'regular';
            $schedule->save();

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

            if ($validator->fails())
                return response()->json(['result'=>'failed','error'=>$validator->errors()->all()], 400);

            if ($request->input('search_id') < 1)
                return response()->json(['result'=>'failed','error'=>"Invalid ID"], 400);

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
            $branch->branch_data = json_encode($request->input('branch_data'));
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

            if ($validator->fails())
                return response()->json(['result'=>'failed','error'=>$validator->errors()->all()], 400);

            $services = array();
            if($request->input('services') !== null)
                foreach($request->input('services') as $value)
                    $services[] = $value['value'];

            $products = array();
            if($request->input('products') !== null)
                foreach($request->input('products') as $value)
                    $products[] = $value['value'];

            $cluster = new BranchCluster;
            $cluster->cluster_name = $request->input('cluster_name');
            $cluster->cluster_owner = $request->input('cluster_owner');
            $cluster->cluster_email = $request->input('cluster_email');
            $cluster->services = json_encode($services);
            $cluster->products = json_encode($products);
            $cluster->is_active = 1;
            $cluster->cluster_data = json_encode($request->input('cluster_data'));
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

            if ($validator->fails())
                return response()->json(['result'=>'failed','error'=>$validator->errors()->all()], 400);

            $services = array();
            if($request->input('services') !== null)
                foreach($request->input('services') as $value)
                    $services[] = $value['value'];

            $products = array();
            if($request->input('products') !== null)
                foreach($request->input('products') as $value)
                    $products[] = $value['value'];

            $cluster = BranchCluster::find($request->input('id'));
            $cluster->cluster_name = $request->input('cluster_name');
            $cluster->cluster_owner = $request->input('cluster_owner');
            $cluster->cluster_email = $request->input('cluster_email');
            $cluster->services = json_encode($services);
            $cluster->products = json_encode($products);
            $cluster->cluster_data = json_encode($request->input('cluster_data'));

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

    function updateBranchSchedule(Request $request){
        $validator = Validator::make($request->all(), [
            'date_start'=>'required',
            'date_end'=>'required'
        ]);

        if ($validator->fails())
            return response()->json(['result'=>'failed','error'=>$validator->errors()->all()], 400);

        $api = $this->authenticateAPI();
        if($api['result'] === 'success') {
            $schedule = BranchSchedule::find($request->input('id'));
            $schedule->date_start = $request->input('date_start');
            $schedule->date_end = $request->input('date_end');
            $schedule->schedule_data = json_encode($request->input('schedule_data'));
            $schedule->schedule_type = $request->input('schedule_type');
            $schedule->save();
            return response()->json(["result"=>"success"],200);
        }
        return response()->json($api, $api["status_code"]);
    }

    function addBranchSchedule(Request $request){
        $validator = Validator::make($request->all(), [
            'date_start'=>'required',
            'date_end'=>'required'
        ]);
        if ($validator->fails())
            return response()->json(['result'=>'failed','error'=>$validator->errors()->all()], 400);

        $api = $this->authenticateAPI();
        if($api['result'] === 'success') {
            $schedule = new BranchSchedule;
            $schedule->branch_id = $request->input('branch_id');
            $schedule->date_start = $request->input('date_start');
            $schedule->date_end = $request->input('date_end');
            $schedule->schedule_data = json_encode($request->input('schedule_data'));
            $schedule->schedule_type = $request->input('schedule_type');
            $schedule->save();
            return response()->json(["result"=>"success"],200);
        }
        return response()->json($api, $api["status_code"]);
    }

    function deleteBranchSchedule(Request $request){
        $api = $this->authenticateAPI();
        if($api['result'] === 'success') {
            BranchSchedule::destroy($request->input('id'));
            return response()->json(["result"=>"success"],200);
        }
        return response()->json($api, $api["status_code"]);
    }
}