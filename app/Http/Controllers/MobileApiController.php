<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Advertisment;
use App\ServiceType;
use App\Service;
use App\Product;
use App\ProductGroup;
use DB;
class MobileApiController extends Controller
{

    //with data
	public function SapnuPuas(Request $request){
		return response()->json();
	}

	//get only
	public function SapnuPuas1(){
		return response()->json($user = User::take(100)->get());
	}

	public function LoadData(){

		$response = array();
		$today    = date('Y-m-d');
		$advertisement_query  = Advertisment::where("is_active","=","1")
							  ->select("ads_image as images","description")
							  ->orderBy('created_at')
							  ->get();

		$duration_male 		= "";
		$duration_female 	= "";
		$price_male 		= "";
		$price_female 		= "";
		$gender             = "";	
		$serv = 1;		  
		$service_array 		= array();
		$product_array   	= array();
		$option = array();
		$service_single_query  = ServiceType::where("is_active","=","1")
								->get();
		foreach($service_single_query as $rowService){
			$service_type_id 	 = $rowService->id;
			$serv_count 		 = DB::table('services')
								 ->where("is_active","=","1")
								 ->where("service_type_id","=",$service_type_id)
								 ->count();
			if($serv_count <= 0){
				$option['price_female']		="";
				$option['price_male']		="";
				$option['duration_female']	="";
				$option['duration_male']	="";
			}
			else{
				$service_price_query = DB::table('services')
									 ->where("is_active","=","1")
									 ->where("service_type_id","=",$service_type_id)
									 ->get();
				foreach($service_price_query as $rowServicePrice){	
					$gender 	= $rowServicePrice->service_gender;
					// echo $gender;
					if($gender == "male"){
						$option['price_male']        = number_format($rowServicePrice->service_price,2);
						$option['duration_male']	 = $rowServicePrice->service_minutes;
						// $option['duration_female']	 = "";
						// $option['price_female']		 = "";
					}
					else if($gender == "female"){
						$option['price_female']      = number_format($rowServicePrice->service_price,2);
						$option['duration_female']	 = $rowServicePrice->service_minutes;
						// $option['duration_male']	="";
						// $option['price_male']		="";
					}
					else{
						$option['price_female']		="";
						$option['price_male']		="";
						$option['duration_female']	="";
						$option['duration_male']	="";
					}
				}	
			}
				
			$service_array[] 	= array(
							'id'    		  => $rowService->id,
							'service_name'    => $rowService->service_name,
							'desc'            => $rowService->service_description,
							'image' 	   	  => $rowService->service_picture,
							'service_type' 	  => $service_type_id,
							'price_male' 	  => $option['price_female'],
							'price_female' 	  => $option['price_male'],
							'duration_male'   => $option['duration_female'],
							'duration_female' => $option['duration_male'],
							'updated_at' 	  => $rowService->updated_at,
								);
		}		

		//papalitan mamaya
		// $product_query = DB::table('products as a')
		// 				->join('product_groups as b','a.product_group_id','=','b.id')
		// 				->where("a.is_active","=","1")
		// 				->select("a.id as product_id","a.product_name","a.product_desc","a.product_price","a.product_price")
		// 				->get();
		// foreach ($product_query as $rowProduct) {
		// 	$product_array[] = array(
		// 					'id' => $rowProduct->product_id,
		// 					'id' => $rowProduct->product_id,
		// 					'id' => $rowProduct->product_id,
		// 						);
		// }
        
        $response['carousel']   = $advertisement_query;
		$response['services']    = $service_array;
		$response['products']    = $product_array;
		$response['date_today'] = $today;
		
		return response()->json($response);
	}

	public function getToken(Request $request){

	}



}
