<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\User;
use App\UserLevel;
use App\Branch;
use App\BranchCluster;
use App\PlcReviewRequest;
use App\BranchController;
use JWTAuth;
use Validator;
use Hash;
use ImageOptimizer;
use Facebook\Facebook;
use App\Service;
use App\ServiceType;
use App\ServicePackage;
use App\ProductGroup;
use App\WaiverQuestion;
use App\Product;
use Mail;
use DB;

class KioskController extends Controller{
    

    public function login(Request $request){

        $response = array();
        $validator = Validator::make($request->all(), [
            'email'     => 'required|max:255',
            'password'  => 'required|max:255',
        ]);

        if ($validator->fails())
            return response()->json(['result'=>'failed','error'=>$validator->errors()->all()], 400);

        //attempt to login the system
        $u = User::where('email', $request['email'])->get()->first();

        if(isset($u['id'])){
            if($u['is_active'] == 0){
                return response()->json(["result"=>"failed","error"=>"Account is inactive. Please check verify it by checking your email address or go to 'Forgot Password' to resend email"],400);
            }
            if(Hash::check($request['password'], $u['password'])){
                $token = JWTAuth::fromUser(User::find($u['id']));
                $user_level = $u['level'];
                if($user_level != 2){
                    return response()->json(["result"=>"failed","error"=>"Sorry, your account is not available in Kiosk System."],400);
                }
                else{
                    $decodeUserData         = json_decode($u['user_data'],true);
                    $branch_id1             = $decodeUserData['branches'];
                    $branch_id              = $branch_id1[0];
                    $arrayServiceQuery[]    = array();
                    $arrayProductQuery[]    = array();
                    $branchQuery[]          = array();

                    if($branch_id == 0){
                       return response()->json(["result"=>"failed","error"=>"Your home branch is not yet set. Please contact our IT Staff @itdev@lay-bare.com to set your home branch. "],400);
                    }
                    else{
                         if($request->input('device') === null){
                            $this->registerToken($u['id'], $token);
                        }
                        else{
                            $this->registerToken($u['id'], $token, $request->input('device'), $request->input('device_info'));
                        }
                        $branchQuery            = Branch::where("id",$branch_id)->get()->first();
                        $cluster_id             = $branchQuery["cluster_id"];
                        $clusterQuery           = BranchCluster::where("id",$cluster_id)->get()->first();
                        $arrayServiceQuery      = Service::whereIn("id",json_decode($clusterQuery["services"],true))
                                                    ->get()
                                                    ->toArray();
                        foreach($arrayServiceQuery as $key => $value){
                            $service_package_id = $value["service_package_id"];
                            $service_type_id    = $value["service_type_id"];
                            $service_id         = $value["id"];
                            if($service_package_id == 0){
                                $query = ServiceType::where("id",$service_type_id)->select("service_name","service_description","service_picture")->get() ->first();
                                $arrayServiceQuery[$key]["service_name"]        = $query["service_name"];
                                $arrayServiceQuery[$key]["service_description"] = $query["service_description"];
                                $arrayServiceQuery[$key]["service_picture"]     = "services/".$query["service_picture"];
                            }
                            else{
                                $service_description = "";
                                $query = ServicePackage::where("id",$service_package_id)->select("package_name as service_name","package_image as service_picture","package_services")->get()->first();
                                $service_description = implode(', ',ServiceType::whereIn('id', json_decode($query['package_services']))->pluck('service_name')->toArray());
                                $arrayServiceQuery[$key]["service_name"]        = $query["service_name"];
                                $arrayServiceQuery[$key]["package_services"]    = json_decode($query['package_services']);
                                $arrayServiceQuery[$key]["service_description"] = $service_description;
                                $arrayServiceQuery[$key]["service_picture"]     = "services/".$query["service_picture"];                                                
                            }
                        }
                        $arrayProductQuery =  Product::whereIn("id",json_decode($clusterQuery["products"],true))->get()->toArray();
                        foreach($arrayProductQuery as $key => $value){  
                            $product_group_id  = $value["product_group_id"];
                            $query             = ProductGroup::where("id",$product_group_id)
                                                        ->select("product_group_name","product_description","product_picture")
                                                        ->get()
                                                        ->first();
                            $arrayProductQuery[$key]["product_name"]        = $query["product_group_name"];
                            $arrayProductQuery[$key]["product_description"] = $query["product_description"];
                            $arrayProductQuery[$key]["product_picture"]     = "products/".$query["product_picture"];                                                    
                        }

                        $arrayWaiver = WaiverQuestion::get()->toArray();
				        foreach($arrayWaiver as $key=>$value){
				            $arrayWaiver[$key]['question_data'] = json_decode($value['question_data']);
				        }                             
                        $response['services'] 	= $arrayServiceQuery;
                        $response['products'] 	= $arrayProductQuery;
                        $response["branches"] 	= $branchQuery;
                        $response["waivers"] 	= $arrayWaiver;
                        $response["user"]     	= $u;
                        $response['token']    	= $token; 
                        return response()->json($response);
                    }
                }
            }
            else{
                 return response()->json(["result"=>"failed","error"=>"Your password is incorrect"],400);
            }
        }
        return response()->json(["result"=>"failed","error"=>"Sorry, user credentials doesn't exist!"],400);
    }

    public function checkLoggedInToken(){
        $api = $this->authenticateAPI();
        if($api['result'] === 'success'){
           return response()->json(["data"=>$api["user"], "result"=>'success']);
        }
        return response()->json($api, $api["status_code"]);
    }

    public function getClientRecords(Request $request){

        $first_name  = $request->input("first_name");
        $last_name   = $request->input("last_name");
        $bday        = $request->input("bday");
        $contact     = $request->input("contact");
        $email       = $request->input("email");
        $gender      = $request->input("gender");
        $branch_id   = $request->input("branch_id");
        $ifSearch    = $request->input("ifSearch");
        $arrayClientResult              = array();
        $response                       = array();    
        $arraySelfResult                = array();
        if($email != null){
            $queryUser   = DB::table("users")
                            ->Where("email",$email)
                            ->get()
                            ->toArray();
        }
        else{
              $queryUser   = DB::table("users")
                            ->where('first_name', 'LIKE','%'.$first_name.'%')
                            ->where('last_name', 'LIKE','%'.$last_name.'%')
                            ->where('level',"=","0")
                            ->where("birth_date",$bday." 00:00:00")
                            ->where(function ($query) use($bday,$email,$contact){
                                $query->orWhere("email",$email)
                                      ->orWhere("user_mobile",$contact);
                            })
                            ->get()
                            ->toArray();
        }
        if(count($queryUser) > 0){
            foreach ($queryUser as $key => $value) {
              $arrayClientResult[] = array(
                           "clientid"           => "",
                            "cusid"             => $value->id,
                            "client_user_id"    => 73,
                            "full_name"         => $value->username,
                            "client_gender"     => $value->gender,
                            "client_bdate"      => $value->birth_date,
                            "premier_branch"    => "0",
                            "client_mobile"     => $value->user_mobile,
                            "client_profile"    => $value->user_picture,
                            "client_email"      => $value->email,
                            "client_fname"      => $value->first_name,
                            "client_lname"      => $value->last_name,
                            );
            }
            $response["user_data_fetch"] = $arrayClientResult;
            $response["user_self_data"]  = $arraySelfResult;
            return response()->json($response);
        }
        else{
            $getdata = http_build_query(
                array(
                    'lname'    => $last_name,
                    'fname'    => $first_name,
                    'email'    => $email,
                    'contact'  => $contact,
                    'bday'     => $bday
                   )
                );
            $opts = array('http' =>
                 array(
                    'method'  => 'GET',
                    'content' => $getdata
                )
            );
            $context   = stream_context_create($opts);
            $queryBoss = file_get_contents("https://boss.lay-bare.com/laybare-online/get-client-profile-silk.php?".$getdata);
            $queryBoss = json_decode($queryBoss,true);
            if(count($queryBoss) > 0){
                
                if($ifSearch == "false"){
                    $user               = new User;
                    $user->first_name   = $first_name;
                    $user->middle_name  = "";
                    $user->last_name    = $last_name;
                    $user->username     = $first_name .' ' . $last_name;
                    $user->user_mobile  = $contact;
                    $user->email        = $email;
                    $user->password     = bcrypt(12345);
                    $user->gender       = $gender;
                    $user->user_address = "";
                    $user->is_confirmed = 0;
                    $user->is_active    = 0;
                    $user->is_agreed    = 0;
                    $user->device_data  = '{}';
                    $user->birth_date   = $bday;
                    $user->user_picture = 'no photo ' . $gender.'.jpg';
                    $user->level        = 0;
                    $user->is_client    = 0;
                    $user->user_data    = json_encode(array(
                                            "home_branch"    => $branch_id,
                                            "premier_status" => 0
                                            ));
                    $user->save();
                    $user_self_data = array(
                                "clientid"          => "",
                                "cusid"             => $user->id,
                                "client_user_id"    => "",
                                "full_name"         => $user->username,
                                "client_gender"     => $user->gender,
                                "client_bdate"      => $user->birth_date,
                                "premier_branch"    => "0",
                                "client_mobile"     => $user->user_mobile,
                                "client_profile"    => $user->user_picture,
                                "client_email"      => $user->email,
                                "client_fname"      => $user->first_name,
                                "client_lname"      => $user->last_name
                            );
                    $response["user_data_fetch"]    =  $arrayClientResult;
                    $response["user_self_data"]     =  $user_self_data;
                }
                
                return response()->json($response);   
            }
            else{
                $response["user_data_fetch"] =  $queryBoss;
                $response["user_self_data"]  =  $arraySelfResult;
                return response()->json($response);
            }
        }  
    }


    public function saveNewUser(Request $request){
        
        $first_name  = $request->input("first_name");
        $last_name   = $request->input("last_name");
        $bday        = $request->input("bday");
        $contact     = $request->input("contact");
        $email       = $request->input("email");
        $gender      = $request->input("gender");
        $branch_id   = $request->input("branch_id");
        $arrayClientResult              = array();
        $response                       = array();    
        $arraySelfResult                = array();

        $user               = new User;
        $user->first_name   = $first_name;
        $user->middle_name  = "";
        $user->last_name    = $last_name;
        $user->username     = $first_name .' ' . $last_name;
        $user->user_mobile  = $contact;
        $user->email        = $email;
        $user->password     = bcrypt(12345);
        $user->gender       = $gender;
        $user->user_address = "";
        $user->is_confirmed = 0;
        $user->is_active    = 0;
        $user->is_agreed    = 0;
        $user->device_data  = '{}';
        $user->birth_date   = $bday;
        $user->user_picture = 'no photo ' . $gender.'.jpg';
        $user->level        = 0;
        $user->is_client    = 0;
        $user->user_data    = json_encode(array(
                                "home_branch"    => $branch_id,
                                "premier_status" => 0
                                ));
        $user->save();
        $user_self_data = array(
                    "clientid"          => "",
                    "cusid"             => $user->id,
                    "client_user_id"    => "",
                    "full_name"         => $user->username,
                    "client_gender"     => $user->gender,
                    "client_bdate"      => $user->birth_date,
                    "premier_branch"    => "0",
                    "client_mobile"     => $user->user_mobile,
                    "client_profile"    => $user->user_picture,
                    "client_email"      => $user->email,
                    "client_fname"      => $user->first_name,
                    "client_lname"      => $user->last_name
                );
        $response["user_data_fetch"]    =  $arrayClientResult;
        $response["user_self_data"]     =  $user_self_data;

        return response()->json($response);   
    }

}


