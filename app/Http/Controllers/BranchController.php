<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Branch;

class BranchController extends Controller
{
    public function getBranches(Request $request){

        if($request->segment(4)== 'active'){
            $branches = Branch::where('is_active', 1)->get()->toArray();
        }
        else{
            $branches = Branch::get()->toArray();
        }

        return response()->json($branches);
    }
}
