<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\User;

class ClientController extends Controller{
    public function searchClients(Request $request){

        $keyword = $request->input('keyword');
        if($keyword == ''){
            return response()->json(["result"=>"failed","error"=>"Please Enter Keyword."], 400);
        }
        $clients = User::where('is_client', 1);
        $clients = $clients->where(function($query) use ($keyword){
            $query->where('first_name', 'LIKE', '%' . $keyword . '%')
                    ->orWhere('middle_name', 'LIKE', '%' . $keyword . '%')
                    ->orWhere('last_name', 'LIKE', '%' . $keyword . '%')
                    ->orWhere('email', 'LIKE', '%' . $keyword . '%')
                    ->orWhere('user_address', 'LIKE', '%' . $keyword . '%')
                    ->orWhere('user_mobile', 'LIKE', '%' . $keyword . '%');
        });
        return response()->json($clients->get());
    }

    public function getClient(Request $request){
        return response()->json(User::where('id', $request->segment(4))->get()->first());
    }
}