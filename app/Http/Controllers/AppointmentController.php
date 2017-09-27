<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Transaction;
use App\TransactionItem;
use Validator;

class AppointmentController extends Controller{
    public function addAppointment(Request $request){
        $api = $this->authenticateAPI();
        if($api['result'] === 'success') {
            $client_id = $api['user']['is_client'] === 1 ?  $client_id = $api['user']['id'] : $request->input('client_id');

            $validator = Validator::make($request->all(), [
                'branch' => 'required',
                'transaction_date' => 'required',
                'transaction_time' => 'required',
                'services' => 'required',
                'products' => 'required_if:services,'.null,
                'platform' => 'required'
            ]);

            $appointment = new Transaction;
            $appointment->reference_no = "";
            $appointment->branch_id = $request->input('branch')['value'];
            $appointment->client_id = $client_id;
            $appointment->transaction_datetime = date('Y-m-d H:i:s', strtotime($request->input('transaction_date').' '. $this->getFirstServiceTime($request->input('services'))));
            $appointment->transaction_status = 'reserved';
            $appointment->platform = $request->input('platform');
            $appointment->booked_by_name = $api['user']['username'];
            $appointment->booked_by_id = $api['user']['id'];
            $appointment->booked_by_type = $api['user']['is_client'] === 1 ? 'client':'admin';
            $appointment->transaction_data = '{}';
            $appointment->waiver_data = '{}';
            $appointment->technician_id = $request->input('technician') !== null ? $request->input('technician')['value']:0;
            $appointment->save();

            foreach($request->input('services') as $key=>$value){
                $item = new TransactionItem;
                $item->transaction_id = $appointment->id;
                $item->item_id = $value['id'];
                $item->item_type = 'service';
                $item->amount = $value['price'];
                $item->quantity = 1;
                $item->book_start_time = date('Y-m-d H:i:s', strtotime($value['start']));
                $item->book_end_time = date('Y-m-d H:i:s', strtotime($value['start']));
                $item->serve_start_time = null;
                $item->serve_end_time = null;
                $item->complete_time = null;
                $item->item_status = 'reserved';
                $item->item_data = '{}';
                $item->save();
            }

            if ($validator->fails()) {
                return response()->json(['result'=>'failed','error'=>$validator->errors()->all()], 400);
            }

            return response()->json(["result"=>"success"],200);
        }
        return response()->json($api, $api["status_code"]);
    }

    function getFirstServiceTime($services){
        foreach($services as $key=>$value){
            if($key === 0){
                return $value['start'];
            }
        }
        return '00:00:00';
    }

    public function getAppointments(Request $request){

    }
}
