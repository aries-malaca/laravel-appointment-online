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

            if ($validator->fails()) {
                return response()->json(['result'=>'failed','error'=>$validator->errors()->all()], 400);
            }

            $appointment = new Transaction;
            $appointment->reference_no = "";
            $appointment->branch_id = $request->input('branch')['value'];
            $appointment->client_id = $client_id;
            $appointment->transaction_datetime = date('Y-m-d H:i:s', strtotime( $this->getFirstServiceTime($request->input('services'))));
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
        switch($request->segment(4)){
            case 'client':
                $appointments = Transaction::where('client_id', $request->segment(5));
                break;
            case 'branch':
                $appointments = Transaction::where('branch_id', $request->segment(5));
                break;
            default:
                $appointments = Transaction;
        }

        if($request->segment(6) === 'active')
            $appointments = $appointments->where('transaction_status', 'reserved');
        elseif($request->segment(6) === 'inactive')
            $appointments = $appointments->where('transaction_status', '<>','reserved');


        $appointments = $appointments->orderBy('transaction_datetime')->get()->toArray();

        foreach($appointments as $key=>$value){
            $branch = Branch::find($value['branch_id']);
            $client = User::find($value['client_id']);
            $technician = Technician::find($value['technician_id']);
            $appointments[$key]['branch_name'] = isset($branch)?$branch->branch_name:'N/A';
            $appointments[$key]['client_name'] = isset($client)?$client->username:'N/A';
            $appointments[$key]['client_contact'] = isset($client)?$client->user_mobile:'N/A';
            $appointments[$key]['technician_name'] = isset($technician)?$technician->first_name .' '. $technician->last_name :'N/A';
            $appointments[$key]['items'] = $this->getAppointmentItems($value['id']);
            $appointments[$key]['transaction_date_formatted'] = date('m/d/Y', strtotime($value['transaction_datetime']));
            $appointments[$key]['transaction_time_formatted'] = date('h:i A', strtotime($value['transaction_datetime']));
            $appointments[$key]['transaction_added_formatted'] = date('m/d/Y h:i A', strtotime($value['created_at']));
        }

        return response()->json($appointments);
    }

    function getAppointmentItems($id){
        $items = TransactionItem::where('transaction_id', $id)->get()->toArray();
        foreach($items as $key=>$value){
            if($value['item_type'] === 'service'){
                $service = Service::find($value['item_id']);
                $service_name = $service->service_type_id !== 0 ? ServiceType::find($service->service_type_id)->service_type_name:ServicePackage::find($service->service_package_id)->package_name;
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
}
