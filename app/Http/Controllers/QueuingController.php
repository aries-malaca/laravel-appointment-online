<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\TransactionItem;
use App\Branch;
use App\Transaction;
use Validator;

class QueuingController extends Controller{

    public function index(Request $request){
        $data['branch'] = Branch::where('id', $request->segment(3))
                                ->get()->first();

        if(!isset($data['branch']['id']))
            return view('errors/404');

        if($request->segment(2) == 'web')
            return view('queuing.web', $data);
        elseif($request->segment(2) == 'radio')
            return view('queuing.radio', $data);
        elseif($request->segment(2) == 'mobile')
            return view('queuing.mobile', $data);

        return view('errors/404');
    }

    public function serveAppointment(Request $request){
        $validator = Validator::make($request->all(), [
            'id' => 'required|numeric|not_in:0',
            'technician_id' => 'required|numeric|not_in:0'
        ]);

        if ($validator->fails())
            return response()->json(['result'=>'failed','error'=>$validator->errors()->all()], 400);

        Transaction::where('id', $request->input('id'))
                    ->update(['serve_time'=>date('Y-m-d H:i'),'technician_id'=>$request->input('technician_id')]);

        return response()->json(["result"=>"success"]);
    }

    public function unServeAppointment(Request $request){

        Transaction::where('id', $request->input('id'))
                    ->update(['serve_time'=>null]);

        return response()->json(["result"=>"success"]);
    }
}