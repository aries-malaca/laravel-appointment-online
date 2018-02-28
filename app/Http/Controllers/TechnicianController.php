<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Technician;
use App\TechnicianSchedule;
use App\Config;
use App\Branch;
use App\BranchCluster;
use DB;
use DateTime;
use Validator;

class TechnicianController extends Controller{
    function getTechnicians(){
        $data = Technician::leftJoin('branch_clusters', 'technicians.cluster_id', '=', 'branch_clusters.id')
                            ->select('cluster_name', 'technicians.*')
                            ->orderBy('first_name')
                            ->get()->toArray();
        foreach($data as $key=>$value){
            $data[$key]['technician_data'] = json_decode($value['technician_data']);
        }
        return response()->json($data);
    }

    function getTechnician(Request $request){
        $data = Technician::leftJoin('branch_clusters', 'technicians.cluster_id', '=', 'branch_clusters.id')
                            ->where('technicians.id', $request->segment(4))
                            ->select('cluster_name', 'technicians.*', 'cluster_data')
                            ->orderBy('first_name')
                            ->get()->first();
        if(isset($data['technician_data'] )) {
            $data['technician_data'] = json_decode($data['technician_data']);
            $data['cluster_data'] = json_decode($data['cluster_data']);
        }
        return response()->json($data);
    }

    function fetchEMSTechnicians(){
        $api = Config::where('config_name', 'FETCH_TECHNICIANS')->get()->first()['config_value'];
        $picture_path = Config::where('config_name', 'EMS_TECHNICIAN_PICTURES_PATH')->get()->first()['config_value'];


        $data = file_get_contents($api);
        $data = json_decode($data,true);

        foreach($data as $key=>$value) {
            $find = Technician::where('employee_id', $value['employee_no'])->get()->first();
            $cluster = BranchCluster::where('cluster_data','LIKE','%"ems_supported":true%')->get()->first();

            if (isset($find['id']))
                $technician = Technician::find($find['id']);
            else
                $technician = new Technician;


            $picture = file_get_contents($picture_path. $value['picture']);
            file_put_contents(public_path('images/technicians/'. $value['employee_no'].'.jpg'), $picture);

            $technician->first_name = $value['first_name'];
            $technician->middle_name = $value['middle_name'];
            $technician->last_name = $value['last_name'];
            $technician->technician_status = '';
            $technician->technician_picture = $value['employee_no'].'.jpg';
            $technician->cluster_id = isset($cluster['id'])?$cluster['id']:0;
            $technician->is_active = 1;
            $technician->technician_data = json_encode(array(
                "mobile" => $value['mobile'],
                "gender" => $value['gender'],
                "email" => $value['email'],
                "civil_status" => $value['civil_status'],
                "position_name" => $value['position_name'],
                "birth_date" => $value['birth_date'],
                "hired_date" => $value['hired_date'],
                "address" => $value['address'],
            ));
            $technician->employee_id = $value['employee_no'];
            $technician->save();

            $this->fillSchedules($value['schedules'], $technician->id);
        }

        return response()->json(["result"=>"success"]);
    }

    function getBranchTechnicians(Request $request){
        $technicians = $this->getScheduledTechnicians($request->segment(4), $request->segment(5)); //branch, date
        return response()->json($technicians);
    }

    function getScheduledTechnicians($branch, $date){
        $technicians = array();

        $find = TechnicianSchedule::where('branch_id', $branch)
                                    ->where('date_start','<=', $date)
                                    ->where('date_end','>=', $date .' 23:59:59')
                                    ->get()->toArray();

        foreach($find as $key=>$value){
            if($e = $this->compareExtract($technicians, $value, idate('w', strtotime($date)))){
                $tech               = Technician::find($value['technician_id']);
                $name               = $tech->first_name .' ' . $tech->last_name;
                $tech_id            = $value['technician_id'];
                $transaction_status = "reserved";
                $array_reserved_sched     = array();

                $getAppointment = DB::table("transaction_items as a")
                                        ->leftJoin("transactions as b","a.transaction_id","=","b.id")
                                        ->where("b.technician_id","=",$tech_id)
                                        ->where("b.transaction_datetime",'<=',$date.' 23:59:59')
                                        ->where("b.transaction_status","=",$transaction_status)
                                        ->where("a.item_status","=",$transaction_status)
                                        ->where("a.item_type","=","service")
                                        ->select("a.book_start_time","a.book_end_time")
                                        ->get();

               foreach ($getAppointment as $key) {
                    $converted_start = new DateTime($key->book_start_time);
                    $converted_end   = new DateTime($key->book_end_time);
                    
                    $array_reserved_sched[] = array(
                                            "sched_appointment_start"    => $converted_start->format("H:i"),
                                            "sched_appointment_end"      => $converted_end->format("H:i")
                                                );
               }                         
                                        
                if($e['schedule'] != '00:00') {
                    $object = array(
                        "employee_id" => $tech['employee_id'],
                        "id" => $tech_id,
                        "schedule" =>
                            array("start" => $e['schedule'],
                                "end" => date("H:i", strtotime(date('Y-m-d ') . ' ' . $e['schedule']) + 32400),
                            ),
                        "name" => $name,
                        "type" => $e['type'],
                        "appointment" => $array_reserved_sched
                    );

                    $found_key = $this->findRangeSchedule($technicians, $tech_id, $e['type']);

                    if ($found_key === false)
                        $technicians[] = $object;
                    else
                        $technicians[$found_key] = $object;
                }

            }
        }

        return $technicians;
    }

    function findRangeSchedule($array, $tech_id, $type){
        foreach($array as $key=>$value){
            if($tech_id === $value['id']){
                if($value['type']==='RANGE' && $type === 'SINGLE')
                    return $key;
                elseif($value['type']==='RANGE')
                    return $key;
            }
        }
        return false;
    }

    function compareExtract($list, $data, $i){
        foreach($list as $key=>$value ) {
            if ($value['id'] == $data['technician_id']) {
                if ($value['type'] == 'SINGLE')
                    return false;
                else
                    if ($data['schedule_type'] == 'RANGE')
                        return false;
            }
        }

        $schedule = json_decode($data['schedule_data']);

        return is_array($schedule)?array("schedule"=>$schedule[$i],"type"=>"RANGE"): array("schedule"=>$schedule,"type"=>"SINGLE");
    }

    function fillSchedules($schedules, $id){
        if(!empty($schedules)){
            TechnicianSchedule::where('technician_id', $id)->delete();

            foreach($schedules as $k=>$v){

                $branch = Branch::where('branch_data','LIKE', '%"ems_id":"'. $v['branch_id'] .'"%')
                    ->get()->first();

                if(isset($branch['id'])){
                    $schedule = new TechnicianSchedule;
                    $schedule->date_start = $v['schedule_start'];
                    $schedule->date_end = $v['schedule_end'];
                    $schedule->schedule_data = json_encode($v['schedule_data']);
                    $schedule->schedule_type = $v['schedule_type'];
                    $schedule->branch_id = $branch['id'];
                    $schedule->technician_id = $id;
                    $schedule->save();
                }
            }
        }
    }

    function addTechnician(Request $request){
        $api = $this->authenticateAPI();
        if($api['result'] === 'success') {
            $validator = Validator::make($request->all(), [
                'first_name'=>'required|max:255',
                'last_name'=>'required|max:255',
                'cluster_id'=>'required|not_in:0|numeric',
                'employee_id'=>'required|max:255|unique:technicians,employee_id',
                'technician_data.gender'=>'required|in:male,female',
                'technician_data.birth_date'=>'required',
                'technician_data.civil_status'=>'required|in:single,married',
            ]);
            if ($validator->fails())
                return response()->json(['result'=>'failed','error'=>$validator->errors()->all()], 400);

            $technician = new Technician;
            $technician->first_name = $request->input('first_name');
            $technician->last_name = $request->input('last_name');
            $technician->middle_name = $request->input('middle_name');
            $technician->cluster_id = $request->input('cluster_id');
            $technician->employee_id = $request->input('employee_id');
            $technician->technician_status = $request->input('technician_status');
            $technician->is_active = 1;
            $technician->technician_picture = 'no photo ' . $request->input('technician_data')['gender'] . '.jpg';
            $technician->technician_data = json_encode($request->input('technician_data'));
            $technician->save();

            return response()->json(['result'=>'success']);
        }

        return response()->json($api, $api["status_code"]);
    }

    function updateTechnician(Request $request){
        $api = $this->authenticateAPI();
        if($api['result'] === 'success') {
            $validator = Validator::make($request->all(), [
                'first_name'=>'required|max:255',
                'last_name'=>'required|max:255',
                'cluster_id'=>'required|not_in:0|numeric',
                'employee_id'=>'required|max:255|unique:technicians,employee_id,' . $request->input('id'),
                'technician_data.gender'=>'required|in:male,female',
                'technician_data.birth_date'=>'required',
                'technician_data.civil_status'=>'required|in:single,married',
            ]);
            if ($validator->fails())
                return response()->json(['result'=>'failed','error'=>$validator->errors()->all()], 400);

            $technician = Technician::find($request->input('id'));
            $technician->first_name = $request->input('first_name');
            $technician->last_name = $request->input('last_name');
            $technician->middle_name = $request->input('middle_name');
            $technician->cluster_id = $request->input('cluster_id');
            $technician->employee_id = $request->input('employee_id');
            $technician->technician_status = $request->input('technician_status');
            $technician->is_active = 1;
            $technician->technician_picture = 'no photo ' . $request->input('technician_data')['gender'] . '.jpg';
            $technician->technician_data = json_encode($request->input('technician_data'));
            $technician->save();

            return response()->json(['result'=>'success']);
        }

        return response()->json($api, $api["status_code"]);
    }

}