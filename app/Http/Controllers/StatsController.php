<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Branch;
use App\User;
use App\Service;
use App\Product;
use App\Transaction;
use App\Technician;


class StatsController extends Controller{
    function getAdminStats(){
        return response()->json(
            array("clients"=>User::where('is_client', 1)->count(),
                  "products"=>Product::count(),
                  "services"=>Service::count(),
                  "appointments"=>Transaction::where('transaction_status', 'reserved')->count(),
                  "technicians"=>Technician::count(),
                  "branches"=>Branch::count(),
                )
        );
    }
}
