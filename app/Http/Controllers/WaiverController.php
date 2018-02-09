<?php

namespace App\Http\Controllers;
use App\Technician;
use Illuminate\Http\Request;
use App\WaiverQuestion;
use App\User;
use App\Branch;
use App\Transaction;

class WaiverController extends Controller{
    public function getWaiverQuestions(){
        $data = WaiverQuestion::get()->toArray();
        foreach($data as $key=>$value){
            $data[$key]['question_data'] = json_decode($value['question_data']);
        }

        return response()->json($data);
    }

    function viewWaiver(Request $request){
        $api = $this->authenticateAPI();

        if($api['result'] === 'success'){

            if($appointment = Transaction::find($request->segment(2))){
                $technician = Technician::find($appointment->technician_id);

                if($client = User::find($appointment->client_id) ){
                    if($api['user']['is_client'] == 1 && $appointment->client_id !== $api['user']['id'])
                        return view('errors.403');

                    $data = array(
                        'client_type'=> (time()-strtotime($client->created_at))<7776000?'New Client':'Old Client',
                        'client'=>$client,
                        'branch'=>Branch::find($appointment->branch_id),
                        'data' => json_decode($appointment->waiver_data),
                        'appointment_date'=>$appointment->transaction_datetime,
                        'appointment'=>$appointment,
                        'technician'=> (isset($technician->id)?($technician->first_name .' '.$technician->last_name):'N/A')
                    );
                    return view('waiver', $data);
                }
            }
        }
        return view('errors.404');
    }
}
