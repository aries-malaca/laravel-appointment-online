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
use App\ServicePackage;
use DateTime;
use Validator;
use Hash;
use DB;
use Mail;
use JWTAuth;
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

        $price_male       = "";
        $price_female     = "";
        $duration_male    = "";
        $duration_female  = "";

		$service_single_query  = ServiceType::where("is_active","=","1")
								->get();
		foreach($service_single_query as $rowService){
            $price_male       = "0.00";
            $price_female     = "0.00";
            $duration_male    = "0";
            $duration_female  = "0";
			$service_type_id 	  = $rowService->id;
			$serv		          = DB::table('services')
								 ->where("is_active","=","1")
								 ->where("service_type_id","=",$service_type_id)
                                 ->select("service_price","service_minutes","service_gender")
								 ->get();

            foreach ($serv as $row) {
                if($row->service_gender == "male"){
                    $price_male    = $row->service_price;
                    $duration_male = $row->service_minutes;
                }       
                else if($row->service_gender == "female"){
                    $price_female    = $row->service_price;
                    $duration_female = $row->service_minutes;
                }    
                else{
                    $price_male      = "0.00";
                    $price_female    = "0.00";
                    $duration_female = "0";
                    $duration_male   = "0";
                }        
            }            

		
	
			$service_array[] 	= array(
							'id'    		  => $rowService->id,
							'service_name'    => $rowService->service_name,
							'desc'            => $rowService->service_description,
							'image' 	   	  => str_replace(" ","%20",$rowService->service_picture),
							'service_type' 	  => $service_type_id,
							'price_male' 	  => $price_male,
							'price_female' 	  => $price_female,
							'duration_male'   => $duration_male,
							'duration_female' => $duration_female,
							'updated_at' 	  => $rowService->updated_at,
								);
       
		}		


		//papalitan mamaya
		 $product_query  =  DB::table('products')
                                    ->leftJoin('product_groups','products.product_group_id','=','product_groups.id')
                                    ->select('products.*','product_group_name','product_picture', 'product_description')
                                    ->where('products.is_active', 1)
                                    ->where('products.product_price', '>',"0")
                                    ->get();
        

        foreach ($product_query as $rowProduct) {
            $product_array[] = array(
                            'id'                    => $rowProduct->id,
                            'name'                  => $rowProduct->product_group_name,
                            'size'                  => $rowProduct->product_size,
                            'image'                 => str_replace(" ","%20",$rowProduct->product_picture),
                            'desc'                  => $rowProduct->product_description,
                            'variant'               => $rowProduct->product_size,
                            'updated_at'            => $rowProduct->updated_at,
                            'price'                 => number_format($rowProduct->product_price,2)
                                );
        }
        
        $response['carousel']    = $advertisement_query;
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
       		
       		$total_discount  	= 5550;
            // file_get_contents("http://boss.lay-bare.com/laybare-online/client_discounts.php?email=".$email);	
			$total_transaction 	= 230;
            // file_get_contents("http://boss.lay-bare.com/laybare-online/new_trans.php?email=".$email);
			// $total_discount		= "190";
			// $total_transaction  = "5520";
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
        					"image" 			=> str_replace(" ","%20",$rowUsers->user_picture),
        					"terms" 			=> $rowUsers->is_agreed,
        					"total_transaction" => number_format((int)($total_transaction),2),
        					"total_discount" 	=> number_format((int)($total_discount),2),
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

        $bday 			    = new DateTime($request->input('addBday'));
        $birth_date         = $bday->format("Y-m-d H:i:s");
        $branch 		    = $request->input('addBranch');
        
        $gender 		    = lcfirst($request->input('addGender'));
        $device             = $request->input('addDevice');
        $device_name        = $request->input('addDeviceName');
        $facebook_id        = $request->input('addFBID');

        $user 			    = new User;
        $user->first_name 	= $request->input('addFname');
        $user->middle_name 	= $request->input('addMname');
        $user->last_name 	= $request->input('addLname');
        $user->username     = $request->input('addFname').' '.$request->input('addLname');
        $user->user_mobile 	= "0".$request->input('addMobile');
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
        $user->user_picture = "";
        if($facebook_id == "" || $facebook_id == null){
            $user->user_data    = json_encode(array(
                                        "home_branch"    =>  $branch,
                                        "premier_status" => 0
                                        ));
            $user->device_data  = '[]';
            $user->save();
            return response()->json([
                                "result"        =>"success", 
                                "isFacebook"    =>  false
                            ]);
        }
        else{

            $user->device_data  = '[]';
            $user->user_data    = json_encode(array(
                                        "home_branch"    => $branch,
                                        "premier_status" => 0,
                                        "facebook_id"    => $facebook_id,
                                        ));

            $user->save();
            $clientID = $user->id;
            $filename = $clientID.'_'.time().'.jpg';
            $data     = file_get_contents('https://graph.facebook.com/'.$facebook_id.'/picture?type=large');
            file_put_contents(public_path('images/users/').$filename, $data);

            $token    = JWTAuth::fromUser($user);
            $this->registerToken($clientID, $token,$device,$device_name);
            $array_response         = array( 
                            "result"        =>  "success",
                            "isFacebook"    =>   true,
                            "image"         =>  $filename,
                            "token"         =>  $token,
                            "client_id"     =>  $clientID
                                );

            $user    = User::find($clientID);
            $user->user_picture = $filename;   
            $user->save();
            
            return response()->json($array_response);
        }
    }

    public function verifyMyPassword(Request $request){

        $rule  = [
                    // required and has to match the password field
                    'verify_email'      => 'required|max:255',
                    'verify_birth_date' => 'required|max:255'
                ];
        $message = [
                    'verify_email.required'             => 'Email is empty', 
                    'verify_birth_date.required'        => 'Birth date is empty'
                ];

        $validator = Validator::make($request->all(),$rule,$message);


        if ($validator->fails()) {
            return response()->json(['result'=>'failed','error'=>$validator->errors()->all()], 400);
        }

        $email      = $request->input('verify_email');
        $bday       = new DateTime($request->input('verify_birth_date'));
        $birth_date = $bday->format("Y-m-d H:i:s");

        $user = User::where('email', $email)
                            ->where('birth_date', 'LIKE',$birth_date.'%')
                            ->get()->first();


        if(!isset($user['id'])) {
            if($result = $this->selfMigrateClient($email,null, $birth_date) ){
                $user = User::where('id', $result['id'])->get()->first();
            }
        }

        if(isset($user['id'])) {
            $generated = md5(rand(1,600));
            $user_data = json_decode($user['user_data'],true);
            $user_data['reset_password_key'] = $generated;
            $user_data['reset_password_expiration'] = time() + 300;
            User::where('id', $user['id'])
                        ->update(['user_data'=> json_encode($user_data)]);

            Mail::send('email.reset_password', ["user"=>$user, "generated"=>$generated], function ($message) use($user) {
                $message->from('notification@system.lay-bare.com', 'Lay Bare Online - Mobile Application');
                $message->subject('Password Reset');
                $message->to($user['email'], $user['first_name']);
            });
            return response()->json(["result"=>"success"]);
        }
        return response()->json([
                            "result" => "failed",
                            "error"  => "No email address or birthdate exist"

                        ],400);
    }

    public function FacebookLogin(Request $request){
        

        $rule  = [
                    'fb_email'      => 'required|max:255'
                ];
        $message = [
                    'fb_email.required'             => 'Email must not empty.'
                ];

        $validator = Validator::make($request->all(),$rule,$message);

        if ($validator->fails()) {
            return response()->json(['result'=>'failed','error'=>$validator->errors()->all()], 400);
        }

        $facebook_id        = $request->input("fb_id");
        $email              = $request->input("fb_email");
        $bday               = $request->input("fb_bday");
        $fname              = $request->input("fb_fname");
        $lname              = $request->input("fb_lname");
        $gender             = $request->input("fb_gender");
        $image              = $request->input("fb_image");
        $device             = $request->input("device");
        $device_name        = $request->input("device_info");
        $branch_id          = "";  
        $branch_name        = ""; 

        $user_fb_login_query = User::where('user_data','LIKE', '%"facebook_id":"'.$facebook_id.'"%')
                                 ->where('email','=',$email)
                                 ->get()
                                 ->first();
        if($email != ""){
            $user_fb_login_query  = User::where('email','=',$email)
                                  ->get()
                                  ->first();
        }
        if(count($user_fb_login_query)){                        
                          
            $clientID = $user_fb_login_query['id']; 
            $token                  = JWTAuth::fromUser($user_fb_login_query);
            $this->registerToken($clientID, $token,$device,$device_name);
            $client                 = User::find($clientID);
            $data                   = json_decode($client->user_data,true);
            $data['facebook_id']    = $facebook_id;
            $branch_id              = $data['home_branch'];
            $client->user_data      = json_encode($data);
            $client->last_activity  = date('Y-m-d H:i:s');
            $client->last_login     = date('Y-m-d H:i:s');
            $client->is_confirmed   = 1;
            $client->save();
            $query_branch = DB::table('branches')
                                    ->where('id','=',$branch_id)
                                    ->get()  
                                    ->first();                                     
            $branch_name  = $query_branch->branch_name;
            $objResult = array(
                        "clientID"              => $clientID,
                        "first_name"            => $user_fb_login_query->first_name,
                        "middle_name"           => $user_fb_login_query->middle_name,
                        "last_name"             => $user_fb_login_query->last_name,
                        "email"                 => $user_fb_login_query->email,
                        "address"               => $user_fb_login_query->user_address,
                        "gender"                => strtolower($user_fb_login_query->gender),
                        "image"                 => $user_fb_login_query->user_picture,
                        "birthday"              => $user_fb_login_query->birth_date,
                        "mobile"                => $user_fb_login_query->user_mobile,
                        "terms"                 => $user_fb_login_query->is_agreed,
                        "branch_id"             => $branch_id,
                        "branch_name"           => $branch_name,
                        "total_transaction"     => "5500",
                        "total_discount"        => "340",
                        "token"                 => $token 
                        );
             return response()->json([
                        "result"        =>"success",
                        "isAlready"     => true,
                        "objResult"     => $objResult
                    ]);
        }
        else{
            return response()->json(['result'=>'success',"isAlready"=> false, "error" => "Email not found. Redirecting.."]);
        }
        return response()->json(['result'=>'failed', "error" => "Cannot proceed to login to facebook"],400);

    }

    //update terms and conditions applied
    public function updateTerms(Request $request){
        $api      = $this->authenticateAPI();
        

        if($api['result'] === 'success'){
            $clientID = $request->input('client_id');
            $query                   = User::find($clientID);
            $query->is_agreed        = "1";
            $query->save();
            return response()->json(['result'=>'success',"clientID" => $clientID]);
        }
        else{
             // return response()->json($api, $api["status_code"]);
        }
       
    }

    public function getPackageWithDescription(Request $request){

       if($request->segment(4)=='active')
            $data = ServicePackage::where('is_active', 1)->get()->toArray();
        else{
            $data = ServicePackage::get()->toArray();
        }
        foreach ($data as $key=>$value){
            $query  = Service::where('service_package_id','=',$data[$key]['id'])
                            ->select('service_minutes','service_price','id as service_id')
                            ->get()
                            ->first();

            $data[$key]['service_desc'] = implode(', ',ServiceType::whereIn('id', json_decode($value['package_services']))->pluck('service_name')->toArray());
            $data[$key]['service_duration'] = $query['service_minutes'];
            $data[$key]['service_price']    = $query['service_price'];
            $data[$key]['service_id']       = $query['service_id'];
        } 
        return response()->json($data);
    }

    public function getServices(Request $request){
        
        if($request->segment(4)!=""){
            $gender = $request->segment(4);

            $query  =  DB::table('services as a')
                        ->leftJoin('service_types as b','a.service_type_id','=','b.id')
                        ->where('a.service_type_id', '<>',0)
                        ->where('a.is_active', 1)
                        ->where('a.service_gender', $gender)
                        ->select('a.id','a.service_gender','a.service_minutes','a.service_price','b.service_name','b.service_description','b.service_picture','b.id as service_type_id')
                        ->get();
        }
        else{
              $query  =  DB::table('services as a')
                        ->leftJoin('service_types as b','a.service_type_id','=','b.id')
                        ->where('a.service_type_id', '<>',0)
                        ->where('a.is_active', 1)
                        ->select('a.id','a.service_gender','a.service_minutes','a.service_price','b.service_name','b.service_description','b.service_picture','b.id as service_type_id')
                        ->get();

        }
        return response()->json($query);
        
    }




}
