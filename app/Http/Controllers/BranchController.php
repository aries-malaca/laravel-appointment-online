<?php

namespace App\Http\Controllers;
use App\BranchShift;
use Illuminate\Http\Request;
use App\Branch;
use App\User;
use App\BranchCluster;
use App\BranchSchedule;
use Validator;
use DateTime;
use Storage;

class BranchController extends Controller{
    public function getBranches(Request $request){
        $today = new DateTime();
        $date_today = $today->format("Y-m-d H:i:s");

        $data = Branch::leftJoin('branch_clusters','branches.cluster_id','=','branch_clusters.id')
                        ->leftJoin('regions','branches.region_id','=','regions.id')
                        ->leftJoin('cities','branches.city_id','=','cities.id')
                        ->select('branches.id as id','cluster_data','branches.*',
                                        'services','products','cluster_name','city_name','region_name')
                        ->orderBy('branch_name', 'asc')
                        ->get()->toArray();

        foreach($data as $key=>$value){
            $data[$key]['cluster_data']             = json_decode($value['cluster_data']);
            $data[$key]['branch_pictures']          = json_decode($value['branch_pictures']);
            $data[$key]['services']                 = json_decode($value['services']);
            $data[$key]['products']                 = json_decode($value['products']);
            $data[$key]['branch_data']              = json_decode($value['branch_data']);
            $data[$key]['kiosk_data']               = json_decode($value['kiosk_data']);
            $data[$key]['social_media_accounts']    = json_decode($value['social_media_accounts']);
            $data[$key]['map_coordinates']          = json_decode($value['map_coordinates']);
            $query_schedule                         = BranchSchedule::where('branch_id', $value['id'])
                                                                    ->select('date_start','date_end','schedule_data','schedule_type')
                                                                    ->orderBy('schedule_type')
                                                                    ->get()->toArray();
            $array_sched = array();
            foreach($query_schedule as $k=>$v){
                $schedule_type  = $query_schedule[$k]['schedule_type'];
                $date_start     = $query_schedule[$k]['date_start'];
                $date_end       = $query_schedule[$k]['date_end'];
                if($schedule_type == "regular"){
                    $query_schedule[$k]['schedule_data'] = json_decode($v['schedule_data']);
                    $array_sched[] = $query_schedule[$k];
                }
                else{
                    if(date($date_today) >= date($date_start) && date($date_today) <= date($date_end)){
                        $query_schedule[$k]['schedule_data'] = json_decode($v['schedule_data']);
                        $array_sched[] = $query_schedule[$k];
                    }
                }
            }
            $data[$key]['schedules']  = $array_sched;
            $data[$key]['schedules_original']  = $query_schedule;
        }
        return response()->json($data);
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
                        ->select('branches.*','region_name','city_name','cluster_data','cluster_name')
                        ->where('branches.id',$request->segment(4))
                        ->get()->first()->toArray();

        $data['branch_pictures'] = json_decode($data['branch_pictures']);
        $data['branch_data'] = json_decode($data['branch_data']);
        $data['map_coordinates'] = json_decode($data['map_coordinates']);
        $data['social_media_accounts'] = json_decode($data['social_media_accounts']);
        $data['cluster_data'] = json_decode($data['cluster_data']);
        $data['kiosk_data'] = json_decode($data['kiosk_data']);
        $data['schedules'] = BranchSchedule::where('branch_id', $request->segment(4))
                                            ->orderBy('schedule_type')
                                            ->get()->toArray();
        foreach($data['schedules'] as $key=>$value){
            $data['schedules'][$key]['schedule_data'] = json_decode($value['schedule_data']);
        }

        return response()->json($data);
    }

    function getBranchSupervisor(Request $request){
        $users = User::leftJoin('user_levels', 'users.level', '=', 'user_levels.id')
                        ->where('user_levels.level_data', 'LIKE', '%"dashboard":"BranchSupervisorDashboard"%')
                        ->select('user_data', 'users.id', 'username')
                        ->get()->toArray();

        foreach($users as $user){
            $d = json_decode($user['user_data'])->branches;
            if(in_array($request->segment(4), $d) OR in_array(0, $d) )
                return response()->json($user);
        }
        return response()->json(false);
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
            $branch->is_active = $request->input('is_active');
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
            $branch->kiosk_data = json_encode(array());
            $branch->directions = $request->input('directions');
            $branch->welcome_message = $request->input('welcome_message');
            $branch->branch_data = json_encode($request->input('branch_data'));
            $branch->opening_date = date('Y-m-d',strtotime($request->input('opening_date')));
            $branch->is_active = 1;
            $branch->save();

            $this->incrementConfigVersion('APP_BRANCH_VERSION');

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
            $branch->is_active = $request->input('is_active');
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
            $this->incrementConfigVersion('APP_BRANCH_VERSION');

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
            $schedule->date_start = $request->input('schedule_type')==='closed'?$request->input('date_start').' '.$request->input('time_start') :$request->input('date_start');
            $schedule->date_end = $request->input('schedule_type')==='closed'?$request->input('date_end').' '.$request->input('time_end') :$request->input('date_end');
            $schedule->schedule_type = $request->input('schedule_type');
            $schedule->schedule_data = json_encode($request->input('schedule_data'));
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
            $schedule->date_start = $request->input('schedule_type')==='closed'?$request->input('date_start').' '.$request->input('time_start') :$request->input('date_start');
            $schedule->date_end = $request->input('schedule_type')==='closed'?$request->input('date_end').' '.$request->input('time_end') :$request->input('date_end');
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

    function getTechnicianShifts(Request $request){
        $data = BranchShift::where('branch_id', $request->segment(4))->get()->toArray();
        foreach($data as $key=>$value)
            $data[$key]['shift_data'] = json_decode($value['shift_data']);

        return response()->json($data);
    }

    function addTechnicianShift(Request $request){
        $api = $this->authenticateAPI();

        $validator = Validator::make($request->all(), [
            'shift_name'=>'required',
            'shift_color'=>'required',
            'shift_data'=>'required',
            'branch_id'=>'required'
        ]);
        if ($validator->fails())
            return response()->json(['result'=>'failed','error'=>$validator->errors()->all()], 400);

        if($api['result'] === 'success') {
            $shift = new BranchShift;
            $shift->shift_name = $request->input('shift_name');
            $shift->shift_color = $request->input('shift_color');
            $shift->shift_data = json_encode($request->input('shift_data'));
            $shift->branch_id = $request->input('branch_id');
            $shift->save();
            return response()->json(["result"=>"success"],200);
        }
        return response()->json($api, $api["status_code"]);
    }

    function updateTechnicianShift(Request $request){
        $api = $this->authenticateAPI();

        $validator = Validator::make($request->all(), [
            'shift_name'=>'required',
            'shift_color'=>'required',
            'shift_data'=>'required',
            'branch_id'=>'required',
            'id'=>'required',
        ]);
        if ($validator->fails())
            return response()->json(['result'=>'failed','error'=>$validator->errors()->all()], 400);

        if($api['result'] === 'success') {
            $shift = BranchShift::find($request->input('id'));
            $shift->shift_name = $request->input('shift_name');
            $shift->shift_color = $request->input('shift_color');
            $shift->shift_data = json_encode($request->input('shift_data'));
            $shift->branch_id = $request->input('branch_id');
            $shift->save();
            return response()->json(["result"=>"success"],200);
        }
        return response()->json($api, $api["status_code"]);
    }

    function deleteTechnicianShift(Request $request){
        $api = $this->authenticateAPI();
        if($api['result'] === 'success') {
            BranchShift::destroy($request->input('id'));
            return response()->json(["result"=>"success"],200);
        }
        return response()->json($api, $api["status_code"]);
    }

    function registerKiosk(Request $request){
        $api = $this->authenticateAPI();
        if($api['result'] === 'success') {

            $find = Branch::where('kiosk_data', 'LIKE', '%'. $request->input('serial_no') .'%')
                ->count();

            if($find > 0)
                return response()->json(["result"=>"failed", "error"=>"Serial already taken."], 400);

            $branch = Branch::find($request->input('branch_id'));

            $kiosk_data = json_decode($branch->kiosk_data,true);
            $kiosk_data[] = array("alias"=> $request->input('alias'),
                                  "serial_no"=>$request->input('serial_no'),
                                  "registered"=>true);

            Branch::where('id',$request->input('branch_id'))
                    ->update(['kiosk_data'=> json_encode($kiosk_data)]);

            return response()->json(["result"=>"success"],200);
        }
        return response()->json($api, $api["status_code"]);
    }

    function unregisterKiosk(Request $request){
        $api = $this->authenticateAPI();
        if($api['result'] === 'success') {

            $branch = Branch::find($request->input('branch_id'));
            $kiosks = json_decode($branch->kiosk_data, true);

            $new_kiosks = array();
            if(sizeof($kiosks) == 0)
                $branch->kiosk_data = json_encode(array());
            else{
                foreach ($kiosks as $t=>$v){
                    if($v['serial_no'] != $request->input('serial_no'))
                        $new_kiosks[] = $v;
                }
                $branch->kiosk_data = json_encode($new_kiosks);
            }
            $branch->save();

            return response()->json(["result"=>"success"],200);
        }
        return response()->json($api, $api["status_code"]);
    }

    function importBranches(){
        $branches = json_decode(Storage::get('imports/branches.json'));

        foreach($branches as $key => $value){
            $branch = Branch::find($value->boss_id);
            if(!isset($branch->id)){
                $branch = new Branch;
                $branch->branch_name = $value->name;
                $branch->branch_code = $value->branch_code;
                $branch->id = $value->boss_id;
                $branch->branch_classification = $value->ownership == 1 ? 'company-owned':'franchised';
                $branch->region_id = $value->region_id;
                $branch->city_id = $value->city_id;
                $branch->branch_address = $value->address;
                $branch->cluster_id = $value->ownership == 1 ? 1: 0;
                $branch->rooms_count = $value->rooms;
                $branch->payment_methods = $value->payment_method;
                $branch->branch_email = $value->email;
                $branch->branch_contact = $value->phone_fax;
                $branch->branch_contact_person = $value->contact_person;
                $branch->social_media_accounts = $value->fb == ''? '[null,null]': json_encode([$value->fb,'']) ;
                $x = explode(",", $value->map_string);
                $branch->map_coordinates = $value->map_string == ""? '{"lat":14,"long":5}': json_encode(["lat"=>(float)$x[0],"long"=>(float)$x[1]]);
                $branch->branch_pictures = json_encode(array());
                $branch->kiosk_data = json_encode(array());
                $branch->directions = $value->address;
                $branch->welcome_message = $value->address;
                $branch->branch_data = json_encode(["ems_id"=>$value->ems_id, "type"=>null, "extension_minutes"=>0]);
                $branch->opening_date = $value->opening_date == ''? date('Y-m-d'):date('Y-m-d',strtotime($value->opening_date));
                $branch->is_active = $value->is_active=='Y'?0:1;
                $branch->save();
            }
        }
    }
}