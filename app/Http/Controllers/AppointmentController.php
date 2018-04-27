<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Transaction;
use App\TransactionItem;
use Validator;
use App\Branch;
use App\Service;
use App\User;
use App\Technician;
use Curl;


class AppointmentController extends Controller{
    public function addAppointment(Request $request){
        $validator = Validator::make($request->all(), [
            'branch' => 'required',
            'client' => 'required',
            'transaction_date' => 'required|date_format:Y-m-d',
            'products' => 'required_if:services,'.null,
            'platform' => 'required',
            'transaction_type' => 'required',
        ],[
            'transaction_date.required'=>'Appointment Date is required',
            'transaction_date.date_format'    => 'Appointment Date must be mm/dd/yyyy format'
        ]);

        if ($validator->fails())
            return response()->json(['result'=>'failed','error'=>$validator->errors()->all()], 400);

        $api = $this->authenticateAPI();
        if($api['result'] === 'success') {

            if($this->hasPendingAppointment($request->input('client')['value']))
                return response()->json(['result'=>'failed','error'=> $api['user']['is_client'] === 1? 'You':'Client'.
                                                    ' already have pending appointment.'], 400);

            $client_id = $api['user']['is_client'] === 1 ?  $client_id = $api['user']['id'] : $request->input('client')['value'];

            $appointment = new Transaction;
            $appointment->reference_no = $this->generateReferenceNo($request->input('branch')['value']);
            $appointment->branch_id = $request->input('branch')['value'];
            $appointment->client_id = $client_id;
            $appointment->transaction_datetime = date('Y-m-d H:i:s', strtotime( $this->getFirstServiceTime($request->input('services'))));
            $appointment->transaction_status = 'reserved';
            $appointment->platform = $request->input('platform');
            $appointment->booked_by_name = $api['user']['username'];
            $appointment->booked_by_id = $api['user']['id'];
            $appointment->booked_by_type = $api['user']['is_client'] === 1 ? 'client':'admin';
            $appointment->transaction_data = '{}';
            $appointment->acknowledgement_data = json_encode(array("signature"=>null));
            $appointment->waiver_data = $api['user']['is_client'] == 1 ? json_encode($request->input('waiver_data')): '{"signature":null}';
            $appointment->transaction_type = $request->input('transaction_type')  ;
            $appointment->technician_id = $request->input('technician') !== null ? $request->input('technician')['value']:0;
            $appointment->save();

            foreach($request->input('services') as $key => $value){
                $item                   = new TransactionItem;
                $item->transaction_id   = $appointment->id;
                $item->item_id          = $value['id'];
                $item->item_type        = 'service';
                $item->amount           = $value['price'];
                $item->quantity         = 1;
                $item->book_start_time  = date('Y-m-d H:i:s', strtotime($value['start']));
                $item->book_end_time    = date('Y-m-d H:i:s', strtotime($value['end']));
                $item->item_status      = 'reserved';
                $item->item_data        = '{}';
                $item->save();
            }
            foreach($request->input('products') as $key=>$value){
                $item                   = new TransactionItem;
                $item->transaction_id   = $appointment->id;
                $item->item_id          = $value['id'];
                $item->item_type        = 'product';
                $item->amount           = $value['price'];
                $item->quantity         = $value['quantity'];
                $item->item_status      = 'reserved';
                $item->item_data        = '{}';
                $item->save();
            }

            $this->sendAppointmentNotification($appointment->id, 'Appointment Confirmation', 'email.appointment_confirmation');

            return response()->json(["result"=>"success","appointment_id"=>$appointment->id, "transaction_datetime"=>$appointment->transaction_datetime],200);
        }
        return response()->json($api, $api["status_code"]);
    }

    function sendAppointmentNotification($appointment_id, $title, $template){
        $transaction = Transaction::leftJoin('branches', 'transactions.branch_id', '=', 'branches.id')
                                    ->leftJoin('technicians', 'transactions.technician_id', '=', 'technicians.id')
                                    ->where('transactions.id', $appointment_id)
                                    ->select('branch_name', 'technicians.first_name as technician_first_name', 'technicians.last_name as technician_last_name',
                                        'transactions.*')
                                    ->get()->first();

        $transaction['items'] = $this->getAppointmentItems($appointment_id);
        $user = User::where('id', $transaction->client_id)->get()->first();
        $data = ["user"=>$user, "appointment"=> $transaction]; //override data
        $headers = array("subject" => env("APP_NAME") .' - '. $title,
                        "to" => [["email" => $user['email'], "name" => $user['username']]]);
        $this->sendMail($template, $data, $headers);
    }

    function hasPendingAppointment($client_id){
        return Transaction::where('client_id', $client_id)
                            ->where('transaction_status', 'reserved')
                            ->count() > 2;
    }

    function generateReferenceNo($branch_id){
        //branch code year series
        $year = date('Y');
        $branch_code = Branch::find($branch_id)->branch_code;

        $last = Transaction::where('reference_no', 'LIKE', $branch_code.'-'.$year.'-%')
                                ->orderBy('id','DESC')
                                ->get()->first();

        if(isset($last['id'])){
            $reference_no = $last['reference_no'];
            $str = explode("-", $reference_no);
            return $branch_code . '-' . $year . '-' . str_pad(($str[2]+1), 8,"0",STR_PAD_LEFT);
        }

        return $branch_code . '-' . $year . '-' . str_pad(1, 8,"0",STR_PAD_LEFT);
    }

    function getFirstServiceTime($services){
        foreach($services as $key => $value){
            if($key === 0)
                return $value['start'];
        }
        return '00:00:00';
    }

    public function getAppointment(Request $request){
        $appointment = Transaction::leftJoin('reviews', 'reviews.transaction_id','=','transactions.id')
                                ->where('transactions.id', $request->segment(4))
                                ->select('rating', 'feedback', 'transactions.*', 'reviews.created_at as review_date', 'review_status')
                                ->get()->first();

        if(isset($appointment['id'])){
            $branch = Branch::find($appointment['branch_id']);
            $client = User::find($appointment['client_id']);
            $technician = Technician::find($appointment['technician_id']);
            $appointment['branch_name'] = isset($branch)?$branch->branch_name:'N/A';
            $appointment['client_name'] = $client->username;
            $appointment['client_contact'] = $client->user_mobile;
            $appointment['client_gender'] = $client->gender;
            $appointment['client_picture'] = $client->user_picture;
            $appointment['technician_name'] = isset($technician)?$technician->first_name .' '. $technician->last_name :'N/A';
            $appointment['items'] = $this->getAppointmentItems($appointment['id']);
            $appointment['transaction_date_formatted'] = date('m/d/Y', strtotime($appointment['transaction_datetime']));
            $appointment['transaction_time_formatted'] = date('h:i A', strtotime($appointment['transaction_datetime']));
            $appointment['transaction_added_formatted'] = date('m/d/Y h:i A', strtotime($appointment['created_at']));
            $appointment['transaction_data'] = json_decode($appointment['transaction_data']);
            $appointment['acknowledgement_data'] = json_decode($appointment['acknowledgement_data']);
            $appointment['status_formatted'] = $this->formatStatus($appointment['transaction_status']);
            $appointment['waiver_data'] = json_decode($appointment['waiver_data']);

            if($appointment['rating'] === null)
                $appointment['rating'] = 0;

            return response()->json($appointment);
        }
        return response()->json(false);
    }

    public function getAppointments(Request $request){
        switch($request->segment(4)){
            case 'client':
                $appointments = Transaction::where('client_id', $request->segment(5));
                break;
            case 'branch':
            case 'queue':
                $appointments = Transaction::where('branch_id', $request->segment(5));
                break;
            default:
                $appointments = Transaction::where('id','<>', 0);
        }

        if($request->segment(6) === 'active')
            $appointments = $appointments->where('transaction_status', 'reserved');
        elseif($request->segment(6) === 'inactive')
            $appointments = $appointments->where('transaction_status', '<>','reserved');
        elseif($request->segment(4) === 'queue'){
            if($request->segment(6) === 'queue')
                $appointments = $appointments->where('transaction_datetime', 'LIKE', date('Y-m-d').'%');
            else
                $appointments = $appointments->where('transaction_datetime', 'LIKE',$request->segment(6) .'%');
        }


        if($request->input('branches') !== null)
            if(!in_array(0, explode( ",",$request->input('branches'))))
                $appointments = $appointments->whereIn('branch_id', explode( ",",$request->input('branches')));

        $appointments = $appointments->orderBy('transaction_datetime','desc')
                                    ->get()->toArray();

        foreach($appointments as $key=>$value){
            $branch = Branch::find($value['branch_id']);
            $client = User::find($value['client_id']);
            $technician = Technician::find($value['technician_id']);
            $appointments[$key]['branch_name'] = isset($branch)?$branch->branch_name:'N/A';

            $appointments[$key]['client_name'] = $client->username;
            $appointments[$key]['client_shortname'] = explode(" ",$client->first_name)[0];
            $appointments[$key]['client_contact'] = $client->user_mobile;
            $appointments[$key]['client_gender'] = $client->gender;
            $appointments[$key]['technician_name'] = isset($technician)?$technician->first_name .' '. $technician->last_name :'N/A';
            $appointments[$key]['technician_shortname'] = isset($technician)?$technician->first_name:'N/A';
            $appointments[$key]['items'] = $this->getAppointmentItems($value['id']);
            $appointments[$key]['transaction_date_formatted'] = date('m/d/Y', strtotime($value['transaction_datetime']));
            $appointments[$key]['transaction_time_formatted'] = date('h:i A', strtotime($value['transaction_datetime']));
            $appointments[$key]['transaction_added_formatted'] = date('m/d/Y h:i A', strtotime($value['created_at']));
            $appointments[$key]['transaction_data'] = json_decode($value['transaction_data']);
            $appointments[$key]['acknowledgement_data'] = json_decode($value['acknowledgement_data']);
            $appointments[$key]['serve_time'] = $value['serve_time'];
            $appointments[$key]['status_formatted'] = $this->formatStatus($value['transaction_status']);
            $appointments[$key]['waiver_data'] = null;
        }

        return response()->json($appointments);
    }

    function formatStatus($status){
        if($status === 'reserved')
            return '<span class="badge badge-info">Reserved</span>';
        else if($status === 'completed')
            return '<span class="badge badge-success">Completed</span>';
        else
            return '<span class="badge badge-danger">'.$status.'</span>';
    }

    public function completeAppointment(Request $request){
        TransactionItem::where('transaction_id', $request->input('id'))
                        ->where('item_status', 'reserved')
                        ->update(['item_status'=>'completed']);

        Transaction::where('id', $request->input('id'))
                    ->update(['transaction_status'=>'completed',
                                'acknowledgement_data'=>json_encode( $request->input('acknowledgement_data')),
                                'complete_time'=> date('Y-m-d H:i:s')
                            ]);

        $this->createNotification('appointment', $request->input('client_id'), ["title"=>"Appointment Complete",
                    "body"=>"Your appointment has been completed.",
                    "unique_id"=>(int)$request->input('id'),
                    "images"=>[],
        ] , true  );
       
        return response()->json(["result"=>"success"]);
    }

    public function cancelAppointment(Request $request){
        $validator = Validator::make($request->all(), [
            'reason'    => 'required|not_in:0',
            'reason_text' => 'required_if:reason,"other"',
        ],[
            'reason.not_in' =>'Please select reason',
            'reason_text.required_if' => 'Please provide text field value for reason.'
        ]);

        if ($validator->fails())
            return response()->json(['result'=>'failed','error'=>$validator->errors()->all()], 400);

        $api = $this->authenticateAPI();
        if($api['result'] === 'success') {
            $items = TransactionItem::where('transaction_id',$request->input('id'))
                                        ->where('item_status', 'reserved')
                                        ->get();

            foreach($items as $i=>$value){
                $item       = TransactionItem::find($value['id']);

                $item_data  = json_decode($item->item_data,true);

                if($item->item_type === 'service'){
                    $item_data['cancel_reason'] = $request->input('reason')!=='other' ? $request->input('reason'): $request->input('reason_text');
                    $item_data['cancel_datetime'] = date('Y-m-d H:i');
                    $item_data['cancel_by_name'] = $api['user']['username'];
                    $item_data['cancel_by_id'] = $api['user']['id'];
                    $item_data['cancel_by_type'] = $api['user']['is_client']===1?'client':'admin';
                }

                $item->item_data = json_encode($item_data);
                $item->item_status = 'cancelled';
                $item->save();
            }

            Transaction::where('id', $request->input('id'))
                            ->update(['transaction_status'=>'cancelled']);

            $this->sendAppointmentNotification($request->input('id'), 'Appointment Cancelled', 'email.appointment_cancelled');

            return response()->json(["result"=>"success"], 200);
        }
        return response()->json($api, $api["status_code"]);
    }

    public function cancelItem(Request $request){
        if($request->input('type') === 'service'){
            $validator = Validator::make($request->all(), [
                'reason' => 'required|not_in:0',
                'reason_text' => 'required_if:reason,"other"',
            ],[
                'reason.not_in' =>'Please select reason',
                'reason_text.required_if' => 'Please provide text field value for reason.'
            ]);

            if ($validator->fails())
                return response()->json(['result'=>'failed','error'=>$validator->errors()->all()], 400);
        }

        $api = $this->authenticateAPI();
        if($api['result'] === 'success') {
            $item = TransactionItem::find($request->input('id'));

            if($item->item_status === 'cancelled')
                return response()->json(['result'=>'failed','error'=>""], 400);

            $item->item_status = 'cancelled';
            $item_data = json_decode($item->item_data,true);

            if($item->item_type === 'service'){
                $item_data['cancel_reason'] = $request->input('reason')!=='other' ? $request->input('reason'): $request->input('reason_text');
                $item_data['cancel_datetime'] = date('Y-m-d H:i');
                $item_data['cancel_by_name'] = $api['user']['username'];
                $item_data['cancel_by_id'] = $api['user']['id'];
                $item_data['cancel_by_type'] = $api['user']['is_client']===1?'client':'admin';
            }

            $item->item_data = json_encode($item_data);
            $item->save();

            $this->arrangeServiceTimes($item->transaction_id);
            $this->refreshStatus($item->transaction_id, 'cancelled');

            return response()->json(["result"=>"success","items_length"=>TransactionItem::where('transaction_id', $item->transaction_id)
                                                                                        ->where('item_status','reserved')
                                                                                        ->where('item_type','service')
                                                                                        ->count()], 200);
        }
        return response()->json($api, $api["status_code"]);
    }

    function refreshStatus($id, $status){
        $items = TransactionItem::where('transaction_id', $id)
                                ->pluck('item_status')->toArray();
        $has_reserved = false;
        $all_reserved = true;
        foreach($items as $item){
            if($item === 'reserved')
                $has_reserved = true;
            else
                $all_reserved = false;
        }

        if($all_reserved && $status ==='expired')
            Transaction::where('id', $id)->update(["transaction_status" => 'expired']);

        if(!$has_reserved)
            Transaction::where('id', $id)->update(["transaction_status" => $status]);
    }

    function arrangeServiceTimes($transaction_id){
        $items = TransactionItem::where('transaction_id', $transaction_id)
                                    ->where('item_type', 'service')
                                    ->get()->toArray();
        $start = false;
        foreach($items as $key=>$value){
            if($value['item_status'] == 'reserved'){
                if(!$start){
                    $start = strtotime($value['book_start_time']);
                    Transaction::where('id', $value['transaction_id'])
                                ->update(['transaction_datetime'=> date('Y-m-d H:i',$start)]);
                }

                $service = Service::find($value['item_id']);
                $interval = isset($service->id)?($service->service_minutes * 60):0;

                if($start){
                    TransactionItem::where('id', $value['id'])
                        ->update(['book_start_time' => date('Y-m-d H:i',$start) ,
                            'book_end_time' => date('Y-m-d H:i',$start+=$interval)]);
                }
            }
        }
    }

    function expireAppointments(){
        $items = TransactionItem::leftJoin('transactions', 'transaction_items.transaction_id', '=', 'transactions.id')
                                    ->where('item_status', 'reserved')
                                    ->select('transaction_items.*','client_id', 'transaction_datetime')
                                    ->get()->toArray();
        $ids = [];

        foreach($items as $key=>$value){
            if((strtotime($value['book_start_time']) < strtotime(date('Y-m-d')) && $value['book_start_time'] != null) ||
                (strtotime($value['transaction_datetime']) < strtotime(date('Y-m-d'))) ){
                TransactionItem::where('id', $value['id'])->update(["item_status" => 'expired']);
                if(!in_array($value['transaction_id'], $ids))
                    $ids[] = $value['transaction_id'];
            }
        }

        $transactions = Transaction::whereIn('id',$ids)->get()->toArray();
        $branches = [];
        foreach($transactions as $transaction){
            $this->refreshStatus($transaction['id'], 'expired');
            $this->createNotification('appointment', $transaction['client_id'],
                            ["title"=>"Expired Appointment",
                                "body"=>"Your appointment has been expired.",
                                "unique_id"=>(int)$transaction['id'],
                                "images"=>[],
                            ] , true );
            if(!in_array($transaction['branch_id'], $branches))
                $branches[] = $transaction['branch_id'];
        }
        $this->expireAppointmentBranches($branches, $ids);
    }

    function expireAppointmentBranches($branches, $ids){
        foreach($branches as $key=>$value){
            $transactions = Transaction::leftJoin('branches', 'transactions.branch_id', '=', 'branches.id')
                            ->leftJoin('technicians', 'transactions.technician_id', '=', 'technicians.id')
                            ->leftJoin('users', 'transactions.client_id', '=', 'users.id')
                            ->whereIn('transactions.id', $ids)
                            ->where('transactions.branch_id', $value)
                            ->select('branch_name', 'technicians.first_name as technician_first_name', 'technicians.last_name as technician_last_name',
                                        'users.first_name as client_first_name', 'users.last_name as client_last_name', 'transactions.*')
                            ->get()->toArray();
            if(!empty($transactions)){
                foreach($transactions as $k=>$v)
                    $transactions[$key]['items'] = $this->getAppointmentItems($v['id']);

                $branch = Branch::where('id', $value)->get()->first();
                $data = ["branch"=>$branch, "appointments"=> $transactions]; //override data
                $headers = array("subject" => env("APP_NAME") .' - '. 'Expired Branch Appointments',
                    "to" => [["email" => $branch['branch_email'], "name" => $branch['branch_name']]]);

                $this->sendMail('email.appointment_expired_branch', $data, $headers);
            }
        }
    }

    function acknowledgeAppointment(Request $request){
        $api = $this->authenticateAPI();
        if($api['result'] === 'success') {
            $appointment = Transaction::find($request->input('id'));
            $waiver_data = json_decode($appointment->waiver_data);
            $acknowledgement_data = json_decode($appointment->acknowledgement_data);
            $acknowledgement_data->signature = isset($waiver_data->signature)?$waiver_data->signature:$this->getLastSignature($api['user']['id']);
            $appointment->acknowledgement_data = json_encode($acknowledgement_data);
            $appointment->save();

            return response()->json(['result'=>'success']);
        }

        return response()->json($api, $api["status_code"]);
    }

    function saveItem(Request $request){
        $validator = Validator::make($request->all(), [
            'service' => 'required',
        ],[
            'service.required'=>'Please select service.'
        ]);

        if ($validator->fails())
            return response()->json(['result'=>'failed','error'=>$validator->errors()->all()], 400);

        $api = $this->authenticateAPI();
        if($api['result'] === 'success') {
            $item = new TransactionItem;
            $item->transaction_id = $request->input('id');
            $item->item_id = $request->input('service')['value'];
            $item->item_type = 'service';
            $item->amount = $request->input('service')['price'];
            $item->quantity = 1;
            $item->item_data = '{}';
            $item->book_start_time = date('Y-m-d H:i:s');
            $item->book_end_time = date('Y-m-d H:i:s');
            $item->item_status = 'reserved';
            $item->save();
            $this->arrangeServiceTimes($item->transaction_id);
            return response()->json(['result'=>'success']);
        }

        return response()->json($api, $api["status_code"]);
    }
}