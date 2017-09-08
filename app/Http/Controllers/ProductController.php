<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Product;
use Validator;

class ProductController extends Controller{
    public function getProducts(Request $request){
        if($request->segment(4)=='active')
            return response()->json(Product::where('is_active', 1)->get());

        return response()->json(Product::get());
    }

    public function addProduct(Request $request){
        $api = $this->authenticateAPI();
        if($api['result'] === 'success'){
            $validator = Validator::make($request->all(), [
                'product_code' => 'required|max:255|unique:products,product_code',
                'product_name' => 'required|max:255|unique:products,product_name',
                'product_price' => 'required|numeric'

            ]);
            if ($validator->fails()) {
                return response()->json(['result'=>'failed','error'=>$validator->errors()->all()], 400);
            }

            $product = new Product;
            $product->product_code = $request->input('product_code');
            $product->product_name = $request->input('product_name');
            $product->product_description = $request->input('product_description');
            $product->product_price = $request->input('product_price');
            $product->product_picture = 'no photo.jpg';
            $product->is_active = 1;
            $product->product_data = '{}';
            $product->save();

            return response()->json(["result"=>"success"]);
        }
        return response()->json($api, $api["status_code"]);
    }

    public function updateProduct(Request $request){
        $api = $this->authenticateAPI();
        if($api['result'] === 'success'){
            $validator = Validator::make($request->all(), [
                'product_code' => 'required|max:255|unique:products,product_code,'.$request->input('id'),
                'product_name' => 'required|max:255|unique:products,product_name,'.$request->input('id'),
                'product_price' => 'required|numeric'

            ]);
            if ($validator->fails()) {
                return response()->json(['result'=>'failed','error'=>$validator->errors()->all()], 400);
            }

            $product = Product::find($request->input('id'));
            $product->product_code = $request->input('product_code');
            $product->product_name = $request->input('product_name');
            $product->product_description = $request->input('product_description');
            $product->product_price = $request->input('product_price');
            $product->save();

            return response()->json(["result"=>"success"]);
        }
        return response()->json($api, $api["status_code"]);
    }

    public function uploadPicture(Request $request){
        $api = $this->authenticateAPI();
        if($api['result'] === 'success') {
            //valid extensions
            $valid_ext = array('jpeg', 'gif', 'png', 'jpg');
            //check if the file is submitted
            if ($request->hasFile('file')) {
                $file = $request->file('file');
                $ext = $file->getClientOriginalExtension();

                //check if extension is valid
                if (in_array($ext, $valid_ext)) {
                    $file->move('images/products/', $request->input('product_id') . '_' . $file->getClientOriginalName());
                    $product = Product::find($request->input('product_id'));
                    $product->product_picture = $request->input('product_id') . '_' . $file->getClientOriginalName();
                    $product->save();
                    return response()->json(["result"=>"success"],200);
                }
                return response()->json(["result"=>"failed","error"=>"Invalid File Format."],400);
            }
            return response()->json(["result"=>"failed","error"=>"No File to be uploaded."], 400);
        }

        return response()->json($api, $api["status_code"]);
    }
}
