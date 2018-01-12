<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\TransactionItem;
use App\Branch;

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

    public function unCallAppointment(Request $request){
        TransactionItem::where('id', $request->input('item_id'))
            ->update(['item_data'=>json_encode(['called'=>0])]);
        return response()->json(["result"=>"success"]);
    }
}
