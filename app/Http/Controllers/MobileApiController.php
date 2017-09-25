<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Advertisment;
use App\ServiceType;
use App\Service;
use App\Product;
use App\ProductGroup;
use App\Branch;
use App\Config;
use DateTime;
use Validator;
use Hash;
use DB;
class MobileApiController extends Controller
{

    

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

	


	public function getUser(){
        $api = $this->authenticateAPI();
        $response = array();

        if($api['result'] === 'success'){
            $user_data = json_decode($api['user']['user_data'],true);
            if($api['user']['is_client'] == 1){
                $branch = Branch::find($user_data['home_branch']);
                if(isset($branch->id)){
                    $branch = $branch->branch_name;
                }
                else{
                    $branch = 'N/A';
                }
                $api['user']['branch'] = ["value"=>$user_data['home_branch'], "label"=> $branch];
            }
            else{
            	//respond a non client 
            	return response()->json(["result"=>"failed","error"=>"You are not allowed to use the Mobile app"],400); 
            }
           
          	$rowUsers = $api['user'];
          	$bday     = new DateTime($rowUsers->birth_date);
            $email 	  = $rowUsers->email;
       		
   //     		$total_discount  	= file_get_contents("http://boss.lay-bare.com/laybare-online/client_discounts.php?email=".$email);	
			// $total_transaction 	= file_get_contents("http://boss.lay-bare.com/laybare-online/new_trans.php?email=".$email);
			$total_discount		= "190";
			$total_transaction  = "5520";
        	$response[] = array(
        					"id" 				=> $rowUsers->id,
        					"fname" 			=> ucfirst($rowUsers->first_name),
        					"mname" 			=> ucfirst($rowUsers->middle_name),
        					"lname" 			=> ucfirst($rowUsers->last_name),
        					"address" 			=> ucwords($rowUsers->user_address),
        					"bday" 				=> $bday->format("m/d/Y"),
        					"mobile" 			=> $rowUsers->user_mobile,
        					"email" 			=> $email,
        					"cusgender" 		=> $rowUsers->gender,
        					"branch" 			=> $rowUsers->branch,
        					"image" 			=> $rowUsers->user_picture,
        					"terms" 			=> $rowUsers->is_agreed,
        					"total_transaction" => number_format($total_transaction,2),
        					"total_discount" 	=> number_format($total_discount,2),
        						);

            return response()->json($response,$api["status_code"]);
        }
        // return response()->json($api, $api["status_code"]);
    }


    public function updateHomeBranch(Request $request){
    	$api = $this->authenticateAPI();
        if($api['result'] === 'success'){
           $validator = Validator::make($request->all(), [
                'branch' => 'required_if:is_client,1',
            ]);
            if ($validator->fails()) {
                return response()->json(['result'=>'failed','error'=>$validator->errors()->all()], 400);
            }

            $clientID  = $request->input('edit_client_id');
            $branch_id = $request->input('edit_home_branch_id');	

            $client 				= User::find($clientID);
            $data 					= json_decode($client->user_data);
            $data->home_branch 		= $branch_id;
            $client->user_data 		= json_encode($data);
            $client->save();
            return response()->json(["result"=>"success"]);
        }
        return response()->json($api, $api["status_code"]);
    }


    public function updatePersonalInfo(Request $request){
    	$api = $this->authenticateAPI();

        if($api['result'] === 'success'){
           $validator = Validator::make($request->all(), [
                'edit_fname'    => 'required|max:255',
                'edit_mname'    => 'required|max:255',
                'edit_lname'    => 'required|max:255',
                'edit_contact'  => 'required|max:255',
                'edit_address'  => 'required|max:255',
                'edit_bday'     => 'required|date',
                'edit_gender'   => 'required|max:255',
            ]);

            if ($validator->fails()) {
                return response()->json(['result'=>'failed','error'=>$validator->errors()->all()], 400);
            }
            
			$clientID     = $request->input('edit_client_id');
            $first_name   = $request->input('edit_fname');
            $middle_name  = $request->input('edit_mname');
            $last_name    = $request->input('edit_lname');
            $address   	  = $request->input('edit_address');
            $mobile       = $request->input('edit_contact');
            $bday   	  = new DateTime($request->input('edit_bday'));
            $birth_date   = $bday->format("Y-m-d H:i:s");
            $gender       = $request->input('edit_gender');

            $client 				= User::find($clientID);
            $client->first_name 	= $first_name;
            $client->middle_name 	= $middle_name;
            $client->last_name 		= $last_name;
            $client->user_address 	= $address;
            $client->user_mobile 	= $mobile;
            $client->birth_date 	= $birth_date;
            $client->gender 	    = $gender;
            $client->save();	

            return response()->json(["result"=>"success"]);
        }
        return response()->json($api, $api["status_code"]);
    }

 	public function updateAccount(Request $request){
    	$api = $this->authenticateAPI();

        if($api['result'] === 'success'){
        	
        	$clientID     		= $request->input('edit_client_id');
           	$email        		= $request->input('edit_email');
           	$old_password 		= $request->input('edit_old_password');
           	$new_password 		= $request->input('edit_new_password');
           	$confirm_password 	= $request->input('edit_confirm_password');
           	
        	if(!Hash::check($old_password, $api['user']['password'] ))
                return response()->json(["result"=>"failed","error"=>"Your old password is incorrect."], 400);

            $rule 	= [
	            		// required and has to match the password field
		            	'edit_email'    		 => 'required|max:255',
		                'edit_new_password'      => 'required|regex:/^.*(?=.*[a-zA-Z])(?=.*[0-9]).*$/|min:10',
            		];
            $message = [
            			'edit_email.required'	 		=> 'Please provide your Email address', 
            			'edit_new_password.required'	=> 'Please provide your new password', 
            			'edit_new_password.regex'		=> 'Your password must be atleast 10 alphanumeric.',
            		];

            $validator = Validator::make($request->all(),$rule,$message);
            if ($validator->fails()) {
                return response()->json(['result'=>'failed','error'=>$validator->errors()->all()], 400);
            }

            $client 				= User::find($clientID);
            $client->password       = bcrypt($new_password);
            $client->save();	
        
            return response()->json(["result"=>"success"]);
        }
        return response()->json($api, $api["status_code"]);
    }

    public function uploadUserImage(Request $request){
    	$api = $this->authenticateAPI();
        if($api['result'] === 'success') {

            if($request->input('upload_image') === null)
                return response()->json(["result"=>"failed","error"=>"Warning! No File to be uploaded."], 400);

            $clientID   		= $request->input('upload_client_id');
            $image 				= $request->input('upload_image');

            // list($type, $image)  = explode(';',$image);
            // list(,$image) 		 = explode(',', $image);


            // if($type == 'data:image/jpeg')
            //    $ext  = 'jpg';
            // elseif($type == 'data:image/png')
            //     $ext = 'png';
            // else
            //     return response()->json(["result"=>"failed","error"=>"Warning! Invalid File Format."],400);



            $filename = $clientID . '_' . time().'.jpg';
            $image = base64_decode($image);
            file_put_contents(public_path('images/users/'). $filename, $image );
           
            $user = User::find($clientID);
            if($user->user_picture != 'no photo ' . $user->gender .'.jpg')
                if(file_exists(public_path('/images/users/'.$user->user_picture)))
                    unlink(public_path('/images/users/'.$user->user_picture));

            $user->user_picture = $filename;
            $user->save();

            return response()->json(["result"=>"success"],200);
    	}
	}

	public function registerUser(Request $request){

		
	 	$rule 	= [
            		// required and has to match the password field
	            	'addLname'          => 'required|max:255',
	            	'addFname'          => 'required|max:255',
		            'addMobile'         => 'required|max:255',
		            'addAddress'        => 'required|max:255',
		            'addEmail'          => 'required|email|unique:users,email',
		            'addGender'         => 'required',
		            'addBranch'         => 'required|not_in:0',
		            'addPassword'       => 'required|regex:/^.*(?=.*[a-zA-Z])(?=.*[0-9]).*$/',
		            'addBday'        	=> 'required'
        		];
        $message = [
        			'addFname.required'				=> 'First name.', 
        			'addLname.required'	 			=> 'Last name', 
        			'addMobile.required'			=> 'Contact no.', 
        			'addAddress.required'			=> 'Complete address.', 
        			'addEmail.required'				=> 'Email address.', 
        			'addEmail.email'				=> 'Email is not valid', 
        			'addEmail.unique'				=> 'Email address is already taken', 
        			'addGender.required'			=> 'Gender.', 
        			'addBranch.required'			=> 'Home Branch.', 
        			'addPassword.regex'			    => 'Password must be atleast 10 alphanumeric.', 
        			'addBday.required'			    => 'Birth date'
        		];

        $validator = Validator::make($request->all(),$rule,$message);

        if ($validator->fails())
            return response()->json(['result'=>'failed','error'=>$validator->errors()->all()], 400);
        $bday 			= new DateTime($request->input('addBday'));
        $birth_date     = $bday->format("Y-m-d H:i:s");
        $branch 		= $request->input('addBranch');
        $gender 		= strtolower($request->input('addGender'));
        $user 			= new User;
        $user->first_name 	= $request->input('addFname');
        $user->middle_name 	= $request->input('addMname');
        $user->last_name 	= $request->input('addLname');
        $user->user_mobile 	= $request->input('addMobile');
        $user->username 	= $request->input('addFname')." ".$request->input('addLname');
        $user->email 		= $request->input('addEmail');
        $user->password 	= bcrypt($request->input('addPassword'));
        $user->gender 		= $gender;
        $user->birth_date 	= $birth_date;
        $user->user_address = $request->input('addAddress');
        $user->level 		= 0;
        $user->is_client 	= 1;
        $user->is_active 	= 0;
        $user->is_confirmed = 0;
        $user->is_agreed 	= 1;
        $user->user_data 	= json_encode(array("home_branch"=>$branch,
                                             "premier_status"=>0));
        $user->device_data  = '[]';
        $user->user_picture = 'no photo '.$gender.'.jpg';
        $user->save();

        return response()->json(["result"=>"success"]);
	}






}
