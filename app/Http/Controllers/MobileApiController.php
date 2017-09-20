<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Advertisment;
use App\ServiceType;
use App\Service;
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


		$service_array 		  = array();
		// $service_price_array  = array();
		// $service_single_query  = ServiceType::where("is_active","=","1")
		// 						// ->select("service_name,service_description,service_picture")
		// 						->get();
		

		// foreach($service_single_query as $rowService){
		// 	$service_array[] = array(
		// 					'service_name' => $rowService->service_name,
		// 					'desc'         => $rowService->service_description,
		// 					'image' 	   => $rowService->service_picture,
		// 						);
		// }		

		$service_price_query = Service::leftJoin('service_types','services.service_type_id','=','service_types.id')
                                    ->leftJoin('service_packages','services.service_package_id','=','service_packages.id')
                                    ->select('services.*','service_name','package_name','service_description')
                                    ->where('services.is_active', 1)->get();  

       foreach($service_price_query as $rowServicePrice){
       		if($rowServicePrice->service_gender == "female"){
       			$price_female = $rowServicePrice->service_price;
       			$price_male   = "";
       		}
       		else{
       			$price_male   = $rowServicePrice->service_price;
       			$price_female = "";
       		}
			$service_array[] = array(
							'name' 	   		=> $rowServicePrice->service_name == "" ? $rowServicePrice->package_name : $rowServicePrice->service_name,
							'desc' 	   		=> $rowServicePrice->service_description,
							'duration' 	    => $rowServicePrice->service_minutes,
							'price_female'  => $price_female,
							'price_male'    => $price_male,
							'image' 	    => $rowServicePrice->service_picture,
								);
		}		

                  




		$response['carousel']   = $advertisement_query;
		$response['service']    = $service_array;

		$response['date_today'] = $today ;

  	   // ("id");
      //  ("service_name");
      //  ("desc");
      //  ("image");
      //  ("price_male");
      //  ("price_female");
      //  ("updated_at");




		return response()->json($response);





	}




}
