<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Product;

class ProductController extends Controller{
    public function getProducts(Request $request){
        if($request->segment(4)=='active')
            return response()->json(Product::where('is_active', 1)->get());

        return response()->json(Product::get());
    }
}
