<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Transaction;
use App\TransactionItem;
use Validator;
use App\Branch;
use App\User;
use App\Technician;
use App\Service;
use App\ServicePackage;
use App\ServiceType;
use App\Product;
use App\ProductGroup;

class AppointmentController extends Controller{
    public function addAppointment(Request $request){
        $validator = Validator::make($request->all(), [
            'branch' => 'required',
            'client' => 'required',
            'transaction_date' => 'required',
            'transaction_time' => 'required',
            'services' => 'required',
            'products' => 'required_if:services,'.null,
            'platform' => 'required',
            'transaction_type' => 'required',
            'waiver_data.signature' =>'required'
        ]);

        if ($validator->fails())
            return response()->json(['result'=>'failed','error'=>$validator->errors()->all()], 400);

        $api = $this->authenticateAPI();
        if($api['result'] === 'success') {
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
            $appointment->waiver_data = json_encode($request->input('waiver_data'));
            $appointment->transaction_type = $request->input('transaction_type')  ;
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
                $item->book_end_time = date('Y-m-d H:i:s', strtotime($value['end']));
                $item->item_status = 'reserved';
                $item->item_data = '{}';
                $item->save();
            }

            foreach($request->input('products') as $key=>$value){
                $item = new TransactionItem;
                $item->transaction_id = $appointment->id;
                $item->item_id = $value['id'];
                $item->item_type = 'product';
                $item->amount = $value['price'];
                $item->quantity = 1;
                $item->item_status = 'reserved';
                $item->item_data = '{}';
                $item->save();
            }

            return response()->json(["result"=>"success"],200);
        }
        return response()->json($api, $api["status_code"]);
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
        foreach($services as $key=>$value){
            if($key === 0)
                return $value['start'];
        }
        return '00:00:00';
    }

    public function getAppointment(Request $request){
        $appointment = Transaction::where('id', $request->segment(4))->get()->first();
        if(isset($appointment['id'])){
            $branch = Branch::find($appointment['branch_id']);
            $client = User::find($appointment['client_id']);
            $technician = Technician::find($appointment['technician_id']);
            $appointment['branch_name'] = isset($branch)?$branch->branch_name:'N/A';
            $appointment['client_name'] = $client->username;
            $appointment['client_contact'] = $client->user_mobile;
            $appointment['client_gender'] = $client->gender;
            $appointment['technician_name'] = isset($technician)?$technician->first_name .' '. $technician->last_name :'N/A';
            $appointment['items'] = $this->getAppointmentItems($appointment['id']);
            $appointment['transaction_date_formatted'] = date('m/d/Y', strtotime($appointment['transaction_datetime']));
            $appointment['transaction_time_formatted'] = date('h:i A', strtotime($appointment['transaction_datetime']));
            $appointment['transaction_added_formatted'] = date('m/d/Y h:i A', strtotime($appointment['created_at']));
            $appointment['transaction_data'] = json_decode($appointment['transaction_data']);
            $appointment['status_formatted'] = $this->formatStatus($appointment['transaction_status']);
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

        $appointments = $appointments->orderBy('transaction_datetime')
                                    ->get()->toArray();

        foreach($appointments as $key=>$value){
            $branch = Branch::find($value['branch_id']);
            $client = User::find($value['client_id']);
            $technician = Technician::find($value['technician_id']);
            $appointments[$key]['branch_name'] = isset($branch)?$branch->branch_name:'N/A';
            $appointments[$key]['client_name'] = $client->username;
            $appointments[$key]['client_contact'] = $client->user_mobile;
            $appointments[$key]['client_gender'] = $client->gender;
            $appointments[$key]['technician_name'] = isset($technician)?$technician->first_name .' '. $technician->last_name :'N/A';
            $appointments[$key]['items'] = $this->getAppointmentItems($value['id']);
            $appointments[$key]['transaction_date_formatted'] = date('m/d/Y', strtotime($value['transaction_datetime']));
            $appointments[$key]['transaction_time_formatted'] = date('h:i A', strtotime($value['transaction_datetime']));
            $appointments[$key]['transaction_added_formatted'] = date('m/d/Y h:i A', strtotime($value['created_at']));
            $appointments[$key]['transaction_data'] = json_decode($value['transaction_data']);
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

    public function callAppointment(Request $request){
        TransactionItem::where('id', $request->input('item_id'))
                        ->update(['item_data'=>json_encode(['called'=>time()])]);
        return response()->json(["result"=>"success"]);
    }

    public function serveAppointment(Request $request){
        TransactionItem::where('id', $request->input('item_id'))
            ->update(['serve_time' => date('Y-m-d H:i'),
                      'item_data' => json_encode(array())]);
        return response()->json(["result"=>"success"]);
    }

    public function unServeAppointment(Request $request){
        TransactionItem::where('id', $request->input('item_id'))
            ->update(['serve_time'=>null]);
        return response()->json(["result"=>"success"]);
    }

    public function completeAppointment(Request $request){
        $item = TransactionItem::find($request->input('item_id'));
        $item->complete_time = date('Y-m-d H:i');
        $item->item_status = 'completed';
        $item->save();

        $this->refreshStatus($item->transaction_id, 'cancelled');
        return response()->json(["result"=>"success"]);
    }

    public function unCallAppointment(Request $request){
        TransactionItem::where('id', $request->input('item_id'))
            ->update(['item_data'=>json_encode(['called'=>0])]);
        return response()->json(["result"=>"success"]);
    }

    public function cancelAppointment(Request $request){
        $validator = Validator::make($request->all(), [
            'reason' => 'required|not_in:0',
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
                $item = TransactionItem::find($value['id']);

                $item_data = json_decode($item->item_data,true);

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

            $this->arrangeServiceTimes($item->transaction_id, $item->id);
            $this->refreshStatus($item->transaction_id, 'cancelled');

            return response()->json(["result"=>"success"], 200);
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

    function arrangeServiceTimes($transaction_id, $item_id){
        $items = TransactionItem::where('transaction_id', $transaction_id)
                                    ->get()->toArray();
        $index = 0;
        $start = '';
        foreach($items as $key=>$value){
            if($item_id == $value['id']){
                $index = $key;
                $start = $value['book_start_time'];
            }
        }

        $start = strtotime($start);
        foreach($items as $key=>$value){
            if($key > $index){
                $interval = strtotime($value['book_end_time']) - strtotime($value['book_start_time']);

                TransactionItem::where('id', $value['id'])
                                ->update(['book_start_time' => date('Y-m-d H:i',$start) ,
                                          'book_end_time' => date('Y-m-d H:i',$start+=$interval)]);
            }
        }
    }

    function expireAppointments(){
        $items = TransactionItem::where('item_status', 'reserved')
                                    ->where('item_type', 'service')
                                    ->get()->toArray();
        foreach($items as $key=>$value){
            if(strtotime($value['book_start_time']) < strtotime(date('Y-m-d'))){
                TransactionItem::where('id', $value['id'])->update(["item_status" => 'expired']);

                $this->refreshStatus($value['transaction_id'], 'expired');
            }
        }
    }

    function getAppointmentItems($id){
        $items = TransactionItem::where('transaction_id', $id)->get()->toArray();
        foreach($items as $key=>$value){
            $items[$key]['item_data'] = json_decode($value['item_data']);
            if($value['item_type'] === 'service'){
                $service = Service::find($value['item_id']);
                $service_name = $service->service_type_id !== 0 ? ServiceType::find($service->service_type_id)->service_name:ServicePackage::find($service->service_package_id)->package_name;
                $items[$key]['item_name'] = $service_name;
                $items[$key]['item_info']['gender'] = $service->service_gender;
            }
            else{
                $product = Product::find($value['item_id']);
                $product_name = ProductGroup::find($product->product_group_id)->product_group_name;
                $items[$key]['item_name'] = $product_name;
                $items[$key]['item_info']['size'] = $product->product_size;
                $items[$key]['item_info']['variant'] = $product->product_variant;
            }
        }
        return $items;
    }

    function sendNotification(Request $request){

    }
}