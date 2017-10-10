<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Technician;
use App\TechnicianSchedule;
use App\Config;
use App\Branch;

class TechnicianController extends Controller{
    function getTechnicians(){
        $data = Technician::get()->toArray();
        foreach($data as $key=>$value){
            $data[$key]['schedules'] = TechnicianSchedule::where('technician_id', $value['id'])->get()->toArray();
        }

        return response()->json($data);
    }

    function fetchEMSTechnicians(){
        $api = Config::where('config_name', 'FETCH_TECHNICIANS')->get()->first()['config_value'];

        $data = file_get_contents($api);
        $data = json_decode($data,true);

        foreach($data as $key=>$value) {
            $find = Technician::where('employee_id', $value['employee_no'])->get()->first();

            if (isset($find['id']))
                $technician = Technician::find($find['id']);
            else
                $technician = new Technician;

            $technician->first_name = $value['first_name'];
            $technician->middle_name = $value['middle_name'];
            $technician->last_name = $value['last_name'];
            $technician->technician_status = '';
            $technician->technician_picture = '';
            $technician->cluster_id = 0;
            $technician->is_active = 1;
            $technician->technician_data = json_encode(array(
                "mobile" => $value['mobile'],
                "email" => $value['email'],
                "civil_status" => $value['civil_status'],
                "position_name" => $value['position_name'],
                "birth_date" => $value['birth_date'],
                "hired_date" => $value['hired_date'],
            ));
            $technician->employee_id = $value['employee_no'];
            $technician->save();

            $this->fillSchedules($value['schedules'], $technician->id);
        }

        return response()->json(["result"=>"success"]);
    }

    function getScheduledTechnicians(Request $request){
        $technicians = $this->getBranchTechnicians($request->segment(4), $request->segment(5)); //branch, date
        return response()->json($technicians);
    }

    function getBranchTechnicians($branch, $date){
        $technicians = array();

        $find = TechnicianSchedule::where('branch_id', $branch)
                                    ->where('date_start','<=', $date)
                                    ->where('date_end','>=', $date .' 23:59:59')
                                    ->get()->toArray();

        foreach($find as $key=>$value){
            if($e = $this->compareExtract($technicians, $value, idate('w', strtotime($date)))){

                $tech = Technician::find($value['technician_id']);
                $name = $tech->first_name .' ' . $tech->last_name;
                if($e['schedule'] != '00:00'){
                    $technicians[] = array("id"=>$value['technician_id'],
                                            "schedule"=>$e['schedule'],
                                            "name" => $name,
                                             "type"=>$e['type']
                                             );

                }
            }
        }

        return $technicians;
    }

    function compareExtract($list, $data, $i){

        foreach($list as $key=>$value ){
            if($value['id'] == $data['technician_id'] ){
                if($value['type'] == 'SINGLE'){
                    return false;
                }
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
}
