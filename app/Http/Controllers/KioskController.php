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
use App\Config;
use JWTAuth;
use Validator;
use Hash;
use ImageOptimizer;
use App\Transaction;
use App\BranchSchedule;
use App\TransactionItem;
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

        $date_today = date("Y-m-d");
        $dayOfWeek  = idate("w",strtotime($date_today));
        $response   = array();
        $validator  = Validator::make($request->all(), [
            'email'     => 'required|max:255',
            'password'  => 'required|max:255',
        ]);

        $serial_no              = $request['device_serial'];
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

                        $branchQuery    = Branch::where("id",$branch_id)->get()->first();   
                        $arrayKiosk     = json_decode($branchQuery["kiosk_data"],true);

                        if($this->validateIfDeviceIsRegistered($arrayKiosk,$serial_no) == true){
 
                            $cluster_id             = $branchQuery["cluster_id"];
                            $clusterQuery           = BranchCluster::where("id",$cluster_id)->get()->first();
                            $arrayServiceQuery      = Service::whereIn("id",json_decode($clusterQuery["services"],true))->get()->toArray();
                            

                            if($request->input('device') === null){
                                $this->registerToken($u['id'], $token);
                            }
                            else{
                                $this->registerToken($u['id'], $token, $request->input('device'), $request->input('device_info'));
                            }
                           
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
                            
                            $querySchedule  = BranchSchedule::where("branch_id",$branch_id)->orderBy("id","desc")->get()->toArray();

                            foreach($querySchedule as $key => $value) {
                                $schedule_type  = $value["schedule_type"];
                                $date_start     = $value["date_start"];
                                $date_end       = $value["date_end"];
                                $arraySchedData = json_decode($value['schedule_data']);

                                if($schedule_type == "regular"){
                                    $arraySchedule = array(
                                                    "start" => $date_today." ".$arraySchedData[$dayOfWeek]->start,
                                                    "end"   => $date_today." ".$arraySchedData[$dayOfWeek]->end,
                                                    "type"  => $schedule_type,
                                                        );
                                    break;
                                }
                                else{
                                   if(strtotime(date($date_today)) >= strtotime(date($date_start)) && strtotime(date($date_today)) <= strtotime(date($date_end))){
                                        $arraySchedule   = array(
                                                    "start" => $date_today." ".$arraySchedData[$dayOfWeek]->start,
                                                    "end"   => $date_today." ".$arraySchedData[$dayOfWeek]->end,
                                                    "type"  => $schedule_type,
                                                        );
                                        break;
                                    }
                                }
                            }
                            $response['services']        = $arrayServiceQuery;
                            $response['products']        = $arrayProductQuery;
                            $response["branches"]        = $branchQuery;
                            $response["waivers"]         = $arrayWaiver;
                            $response['branch_schedules']= $arraySchedule;
                            $response["user"]            = $u;
                            $response['token']           = $token; 
                            return response()->json($response);

                        }   
                        else{
                            return response()->json(["result"=>"failed","error" => "Sorry, this device is not registered to your Account & Branch. Please see your Kiosk device details at Lay Bare Online.\n\nDashboard->Branches->Edit Branch->Branch Kiosks\n\nCheck the serial code & Alias"],400);
                            // return response()->json(["result"=>"failed","error"=>"Sorry, credentials is already exist. Please contact Branch Receptionist / Supervisor to search your account"],400); 
                        }
                    }
                }
            }
            else{
                 return response()->json(["result"=>"failed","error"=>"Your password is incorrect"],400);
            }
        }
        return response()->json(["result"=>"failed","error"=>"Sorry, user credentials doesn't exist!"],400);
    }

    public function loginClient(Request $request){
        
        $arraySelfResult        = array();
        $validator  = Validator::make($request->all(), [
            'email'     => 'required|max:255',
            'password'  => 'required|max:255',
        ]);

        if ($validator->fails())
            return response()->json(['result'=>'failed','error'=>$validator->errors()->all()], 400);
        
        $email      = $request->input("email");
        $password   = $request->input("password");

        $u = User::where('email', $email)->get()->first();

        if(isset($u['id'])){

            // if($u['is_active'] == 0)
            //     return response()->json(["result"=>"failed","error"=>"Account is inactive. Please check verify it by checking your email address or go to 'Forgot Password' to resend email"],400);
            if($u["level"] != 0){
                return response()->json(["result"=>"failed","error"=>"Sorry, your acount is restricted. Please contact branch supervisor for details"],400);
            }
            if(Hash::check($password, $u['password'])){
                $token       = JWTAuth::fromUser(User::find($u['id']));
                $arraySelfResult = array(
                       "clientid"           => "",
                        "cusid"             => $u["id"],
                        "client_user_id"    => 0,
                        "full_name"         => $u["username"],
                        "client_gender"     => $u["gender"],
                        "client_bdate"      => $u["birth_date"],
                        "premier_branch"    => "0",
                        "client_mobile"     => $u["user_mobile"],
                        "client_profile"    => $u["user_picture"],
                        "client_email"      => $u["email"],
                        "client_fname"      => $u["first_name"],
                        "client_lname"      => $u["last_name"],
                        );
                return response()->json($arraySelfResult);
            }
            return response()->json(["result"=>"failed","error"=>"Incorrect Password"],400);
        }
        return response()->json(["result"=>"failed","error"=>"User not found."],400);
    }


    public function checkLoggedInToken(Request $request){
        
        $date_today     = date("Y-m-d");
        $dayOfWeek      = idate("w",strtotime($date_today));
        $branch_id      = $request->segment(4);
        $api            = $this->authenticateAPI();
        $arraySchedule  = array();
        if($api['result'] === 'success'){
        
            $querySchedule  = BranchSchedule::where("branch_id",$branch_id)->orderBy("id","desc")->get()->toArray();
            foreach($querySchedule as $key => $value) {
                $schedule_type  = $value["schedule_type"];
                $date_start     = $value["date_start"];
                $date_end       = $value["date_end"];
                $arraySchedData = json_decode($value['schedule_data']);

                if($schedule_type == "regular"){
                    $arraySchedule = array(
                                    "start" => $date_today." ".$arraySchedData[$dayOfWeek]->start,
                                    "end"   => $date_today." ".$arraySchedData[$dayOfWeek]->end,
                                    "type"  => $schedule_type,
                                        );
                    break;
                }
                else{
                   if(strtotime(date($date_today)) >= strtotime(date($date_start)) && strtotime(date($date_today)) <= strtotime(date($date_end))){
                        $arraySchedule   = array(
                                    "start" => $date_today." ".$arraySchedData[$dayOfWeek]->start,
                                    "end"   => $date_today." ".$arraySchedData[$dayOfWeek]->end,
                                    "type"  => $schedule_type,
                                        );
                        break;
                    }
                }
            }                            
           return response()->json([
                            "data"              => $api["user"],
                            "branch_schedules"  => $arraySchedule, 
                            "result"            => 'success'
                        ]);
        }
        return response()->json($api, $api["status_code"]);
    }

    public function getClientRecords(Request $request){

        $first_name         = $request->input("first_name");
        $last_name          = $request->input("last_name");
        $bday               = $request->input("bday");
        $contact            = $request->input("contact");
        $email              = $request->input("email");
        $gender             = $request->input("gender");
        $branch_id          = $request->input("branch_id");
        $ifSearch           = $request->input("ifSearch");
        $response           = array();  
        $arrayClientResult  = array();
        $arraySelfResult    = array();
        
        if($email != null){
            $queryUser   = DB::table("users")
                            ->where('first_name', 'LIKE','%'.$first_name.'%')
                            ->where('last_name', 'LIKE','%'.$last_name.'%')
                            ->where("email",$email)
                            ->where('level',"=","0")
                            ->get()
                            ->toArray();
        }
        else if($contact != null){
            $queryUser   = DB::table("users")
                            ->where('first_name', 'LIKE','%'.$first_name.'%')
                            ->where('last_name', 'LIKE','%'.$last_name.'%')
                            ->where("user_mobile",$contact)
                            ->where('level',"=","0")
                            ->get()
                            ->toArray();
        }
        else if($bday != null){
           $queryUser   = DB::table("users")
                            ->where('first_name', 'LIKE','%'.$first_name.'%')
                            ->where('last_name', 'LIKE','%'.$last_name.'%')
                            ->where('level',"=","0")
                            ->where("birth_date",$bday." 00:00:00")
                            ->get()
                            ->toArray();
        }
        else{
              $queryUser   = DB::table("users")
                            ->where('first_name', 'LIKE','%'.$first_name.'%')
                            ->where('last_name', 'LIKE','%'.$last_name.'%')
                            ->where('level',"=","0")
                            ->where("birth_date",$bday." 00:00:00")
                            ->where("user_mobile",$contact)
                            ->where("email",$email)
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
                foreach ($queryBoss as $key => $value) {

                    $generated      = md5(rand(1,600));
                    $boss_id        = $value["clientid"];
                    $boss_fullname  = $value["full_name"];
                    $boss_gender    = lcfirst($value["client_gender"]);
                    $boss_premier   = $value["premier_branch"];
                    $boss_bday      = $value["client_bdate"];
                    $boss_email     = $value["client_email"];
                    $boss_fname     = $value["client_fname"];
                    $boss_lname     = $value["client_lname"];
                    $boss_mobile    = $value["client_mobile"];
                    $tempPassword   = $this->randomPassword(10);
                    //save user from boss
                    $user               = new User;
                    $user->first_name   = $boss_fname;
                    $user->middle_name  = "";
                    $user->last_name    = $boss_lname;
                    $user->username     = $boss_fullname;
                    $user->user_mobile  = $boss_mobile;
                    $user->gender       = $boss_gender;
                    $user->email        = $boss_email;
                    $user->password     = bcrypt($tempPassword);
                    $user->user_address = "";
                    $user->is_confirmed = 0;
                    $user->is_active    = 0;
                    $user->is_agreed    = 0;
                    $user->is_client    = 1;
                    $user->device_data  = '{}';
                    $user->birth_date   = $bday." 00:00:00";
                    $user->user_picture = 'no photo ' . $boss_gender.'.jpg';
                    $user->level        = 0;
                    $user->user_data    = json_encode(array(
                                            "home_branch"       => $branch_id,
                                            "premier_status"    => 0,
                                            "verify_key"        => $generated,
                                            "verify_expiration" => time() + 300
                                            ));
                    $user->save();
                    $user_self_data[] = array(
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
                    if($boss_email != null || $boss_email != ""){

                        $recipient  = $boss_email;
                        $linkUrl    = "http://192.168.1.225/register/verify?email=".$recipient."&key=".$generated;    
                        $title      = "Lay Bare Online Account Confirmation";
                        $content    = "Dear ".$boss_fullname.", <br><br>Thank you for for signing up to Lay Bare Online (Via Kiosk) <br><br><br>
                                    Here's your temporary credentials:<br>
                                     Username: <strong>".$boss_email."</strong><br>
                                    Password: <strong>".$tempPassword."</strong><br><br>
                                    After receiving this email, please confirm your password by clicking the verify button to activate your account<br><a href='".$linkUrl."' class='btn btn-primary'>VERIFY YOUR ACCOUNT</a> <br><br><br>
                                    Login your account once Confirmation is done and change your password immediately.";

                        $content_data = ["content"=>$content, "subject"=>$title];
                        $headers = array("subject"=>$title,"to"=> [["email"=>$recipient, "name"=> $user->username]]);
                        $this->sendMail('email.kiosk_confirmation_email', $content_data, $headers);

                    }
                    $response["user_data_fetch"]    = $user_self_data;
                    return response()->json($response);   
                }  
            }
            else{
                $response["user_data_fetch"] =  $queryBoss;
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
        $countExist   = 0;
        $tempPassword = $this->randomPassword(10);

        $queryCheckIfUserExistByBday    = DB::table("users")
                                            ->where('first_name', 'LIKE','%'.$first_name.'%')
                                            ->where('last_name', 'LIKE','%'.$last_name.'%')
                                            ->where("birth_date",$bday." 00:00:00")
                                            ->where('level',"=","0")
                                            ->get()
                                            ->toArray();
        if(count($queryCheckIfUserExistByBday) > 0){
            $countExist+=1;
        }
        if($email != null){
            $queryCheckIfUserExistByEmail   = DB::table("users")
                                            ->where("email",$email)
                                            ->get()
                                            ->toArray();
            if(count($queryCheckIfUserExistByEmail) > 0){
                $countExist+=1;
            }                                
        }
        if($contact != null){
            $queryCheckIfUserExistByContact  = DB::table("users")
                                            ->where("user_mobile",$contact)
                                            ->where('level',"=","0")
                                            ->get()
                                            ->toArray();
            if(count($queryCheckIfUserExistByContact) > 0){
                $countExist+=1;
            }                                
        }

        if($countExist > 0){
            return response()->json(["result"=>"failed","error"=>"Sorry! It looks like you already visited Lay Bare before. Do you want to search your credentials?"],400);      
        }
        else{

            $generated          = md5(rand(1,600));
            $user               = new User;
            $user->first_name   = $first_name;
            $user->middle_name  = "";
            $user->last_name    = $last_name;
            $user->username     = $first_name .' ' . $last_name;
            $user->user_mobile  = $contact;
            $user->email        = $email;
            $user->password     = bcrypt($tempPassword);
            $user->gender       = $gender;
            $user->user_address = "";
            $user->is_confirmed = 0;
            $user->is_active    = 0;
            $user->is_agreed    = 0;
            $user->level        = 0;
            $user->is_client    = 1;
            $user->device_data  = '{}';
            $user->birth_date   = $bday;
            $user->user_picture = 'no photo ' . $gender.'.jpg';
            $user->user_data    = json_encode(array(
                                    "home_branch"       => $branch_id,
                                    "premier_status"    => 0,
                                    "verify_key"        => $generated,
                                    "verify_expiration" => time() + 300
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
           
            if($email != null || $email != ""){

                $recipient  = $user->email;
                $linkUrl    = "http://192.168.1.225/register/verify?email=".$recipient."&key=".$generated;    
                $title      = "Lay Bare Online Account Confirmation";
                $content    = "Dear ".$user->username.", <br><br>Thank you for for signing up to Lay Bare Online (Via Kiosk) <br><br><br>
                            Here's your temporary credentials:<br>
                             Username: <strong>".$user->email."</strong><br>
                            Password: <strong>".$tempPassword."</strong><br><br>
                            After receiving this email, please confirm your password by clicking the verify button to activate your account<br><a href='".$linkUrl."' class='btn btn-primary'>VERIFY YOUR ACCOUNT</a> <br><br><br>
                            Login your account once Confirmation is done and change your password immediately.";

                $content_data = ["content"=>$content, "subject"=>$title];
                $headers = array("subject"=>$title,"to"=> [["email"=>$recipient, "name"=> $user->username]]);
                $this->sendMail('email.kiosk_confirmation_email', $content_data, $headers);

            }

            $response["user_data_fetch"]    =  $arrayClientResult;
            $response["user_self_data"]     =  $user_self_data;

            return response()->json($response);   
        }
    }

    public function getTodaysQueue(Request $request){

        $branch_id  = $request->segment(4);
        $today      = date("Y-m-d");  
        $response   = array();  
       
        $queryQueue = Transaction::where('transaction_status', "reserved")
                        ->leftJoin('users', 'users.id', '=', 'transactions.client_id')
                        ->where('branch_id', $branch_id)
                        ->whereBetween('transaction_datetime', array($today." 00:00:00", $today." 23:59:00"))
                        ->select("users.first_name","users.last_name","users.id as user_id","transactions.id as transaction_id","transactions.platform","transactions.transaction_type","transactions.reference_no","transactions.technician_id","transactions.transaction_datetime","transactions.waiver_data")
                        ->orderBy('transaction_datetime','asc')
                        ->get();

        foreach($queryQueue as $key => $value){

            $full_name              = $value['first_name']." ".$value['last_name'];
            $transaction_id         = $value['transaction_id'];
            $transaction_type       = $value['transaction_type'];
            $platform               = $value['platform'];
            $reference_no           = $value['reference_no'];
            $technician_id          = $value['technician_id'];
            $client_id              = $value['user_id'];
            $transaction_datetime   = $value['transaction_datetime'];
            $booked_at              = $value['transaction_datetime'];
            $waiver_data            = json_decode($value['waiver_data'],true);
            $ifClientSigned         = "";
            $duration               = 0;


            if($waiver_data == null){
                $ifClientSigned = false;

            }
            else{
                $ifClientSigned = true;
            }

            $items              = TransactionItem::where('transaction_id', $transaction_id)
                                                ->where("item_status","=","reserved")
                                                ->get()->toArray();
            
            foreach($items as $keys => $values){
               
                $start_time     = $values['book_start_time'];
                $end_time       = $values['book_end_time'];
                if($values['item_type'] === 'service'){
                    $service_id      = $values["item_id"];

                    $serviceQuery    = Service::where("id","=",$service_id)
                                            ->select("service_minutes")
                                            ->get()->first();

                    $minutes            = $serviceQuery["service_minutes"];                        
                    $duration           += $minutes;                        
                }
                else{
                    continue;
                }
            }
            $response[] = array(

                    "total_duration"        => $duration,
                    "full_name"             => $full_name,
                    "client_id"             => $client_id,
                    "transaction_type"      => $transaction_type,
                    "platform"              => $platform,
                    "transaction_datetime"  => $transaction_datetime,
                    "transaction_id"        => $transaction_id,
                    "reference_no"          => $reference_no,
                    "technician_id"         => $technician_id,
                    "ifClientSignedWaiver"  => $ifClientSigned
                            );
        }
        return response()->json($response); 
    }

    public function addAppointments(Request $request){
        
        $validator = Validator::make($request->all(), [
            'branch'            => 'required',
            'client'            => 'required',
            'transaction_date'  => 'required',
            'services'          => 'required',
            'platform'          => 'required',
            'transaction_type'  => 'required',
            // 'waiver_data.signature' =>'required'
        ]);
    
            if ($validator->fails())
                return response()->json(['result'=>'failed','error'=>$validator->errors()->all()], 400);

            $objectClient       = $request->input("client");
            $objectBranch       = $request->input("branch");
            $arrayServices      = $request->input("services");
            $arrayProducts      = $request->input("products");
            $arrayWaiver        = $request->input('waiver_data');
           
            $transaction_type   = $request->input('transaction_type');
            $platform           = $request->input('platform');
        

            $branch_id          = $objectBranch["value"];
            $full_name          = $objectClient["label"];
            $client_id          = $objectClient["value"];
            $reference_no       = $this->generateReferenceNo($branch_id);


            $appointment                        = new Transaction;
            $appointment->reference_no          = $reference_no;
            $appointment->branch_id             = $branch_id;
            $appointment->client_id             = $client_id;
            $appointment->transaction_datetime  = date('Y-m-d H:i:s',strtotime($this->getFirstServiceTime($arrayServices)));
            $appointment->transaction_status    = 'reserved';
            $appointment->platform              = $platform;
            $appointment->booked_by_name        = $full_name;
            $appointment->booked_by_id          = $client_id ;
            $appointment->booked_by_type        = "client";
            $appointment->transaction_data      = '{}';
            $appointment->waiver_data           = json_encode($arrayWaiver);
            $appointment->transaction_type      = $transaction_type;
            $appointment->acknowledgement_data  = '{"signature":null}';
            $appointment->technician_id         = 0;
            $appointment->save();

            foreach($arrayServices as $key=>$value){
                $item                   = new TransactionItem;
                $item->transaction_id   = $appointment->id;
                $item->item_id          = $value['id'];
                $item->item_type        = 'service';
                $item->amount           = $value['price'];
                $item->quantity         = 1;
                $item->book_start_time  = date('Y-m-d H:i:s', strtotime($value['start']));
                $item->book_end_time    = date('Y-m-d H:i:s', strtotime($value['end']));
                $item->item_status      = 'reserved';
                $item->item_data        = '{}';
                $item->save();
            }

            foreach($arrayProducts as $key=>$value){
                $item = new TransactionItem;
                $item->transaction_id   = $appointment->id;
                $item->item_id          = $value['id'];
                $item->item_type        = 'product';
                $item->amount           = $value['price'];
                $item->quantity         = $value['quantity'];
                $item->item_status      = 'reserved';
                $item->item_data        = '{}';
                $item->save();
            }
            return response()->json(["result"=>"success"],200);   
       
        // return response()->json($api, $api["status_code"]);
    }




    public function verifyUserSettings(Request $request){
        
        $myPassword     = $request->input("password");
        $api            = $this->authenticateAPI();
        if($api['result'] === 'success'){
            $accountPassword = $api["user"]["password"];
            if(Hash::check($myPassword, $accountPassword)){
                return response()->json(['result'=>'success','data'=>"Successfully found"],200);      
            }
           return response()->json(['result'=>'failed','error'=>"Sorry, the password is incorrect."], 400);
        }
        return response()->json(['result'=>'failed','error'=>"Sorry, no user found. "], 401);
    }


    public function checkDeviceIfRegistered(Request $request){
        
        $serial_no      = $request->input("serial_no");
        $keyword        = '"serial_no":"'.$serial_no.'"';
        $querySerial    = Branch::where("kiosk_data",'LIKE', '%' . $keyword . '%')
                            ->select("id","branch_name")
                            ->get()->toArray();
        return response()->json(['result'=>'success','data'=>$querySerial],200);  
    }

    public function searchClient(Request $request){
        // Request $request
        $client_name = $request->input("client_name");
        $branch_id   = $request->input("branch_id");
        // $client_name = "Sherwin Fajardo";
        // $branch_id   = 10;
        $response    = array();
        $today       = date("Y-m-d");
        // $queryUser   = DB::table("users as a")
        $queryUser = Transaction::leftjoin('users','transactions.client_id','=','users.id')
                        
                        ->where('users.is_client', 1)
                        ->where("users.username","LIKE",'%'.$client_name.'%')
                        ->where("transactions.transaction_datetime","LIKE", $today.'%')
                        ->where("transactions.transaction_status","=","reserved")
                        ->select('users.id as client_id','users.email','users.gender','users.username as full_name','users.birth_date','users.user_mobile','users.user_picture','users.email','transactions.id as transaction_id','transactions.reference_no','transactions.platform','transactions.waiver_data')
                        ->orderBy('transactions.transaction_datetime', 'asc')
                        ->orderBy('transactions.waiver_data', '{"signature":null}')
                        ->get()->toArray();
        foreach ($queryUser as $key => $value) {

            $transaction_id = $value["transaction_id"];
            $objectWaiver   = json_decode($value["waiver_data"]);
            $ifWaiverSigned = false;
            $arrayItems[]     = array();
           
            if($value["waiver_data"] == '{"signature":null}' && $objectWaiver->signature == null){
                $ifWaiverSigned = false;
            }
            else{
                $ifWaiverSigned = true;
            }
            $queryItems = TransactionItem::where("transaction_id",$transaction_id)
                            ->where("item_status","reserved")
                            ->select("item_id")
                            ->get()->pluck("item_id")->toArray();
            $response[] = array(
                    "transaction_id"    => $value["transaction_id"],
                    "client_id"         => $value["client_id"],
                    "client_user_id"    => 0,
                    "full_name"         => $value["full_name"],
                    "client_gender"     => $value["gender"],
                    "client_bdate"      => $value["birth_date"],
                    "premier_branch"    => "0",
                    "client_mobile"     => $value["user_mobile"],
                    "client_profile"    => $value["user_picture"],
                    "client_email"      => $value["email"],
                    "platform"          => $value["platform"],
                    "reference_no"      => $value["reference_no"],
                    "ifWaiverSigned"    => $ifWaiverSigned,
                    "waiver_data"       => $objectWaiver,
                    "items"             => $queryItems
                        );  
        }                
        return response()->json($response,200);   
    }

    public function addWalkinWaiver(Request $request){

        $api = $this->authenticateAPI();
        if($api['result'] === 'success'){
            // $client_id      = $request->input("client_id");
            $transaction_id = $request->input("transaction_id");
            $objectWaiver   = $request->input("object_waiver");
            $appointment                        = Transaction::find($transaction_id);
            $appointment->waiver_data           = json_encode($objectWaiver);
            $appointment->save();
            return response()->json(['result'=>'success','data'=>"Waiver is successfully saved! Please wait to be called. "],200); 
        }
        return response()->json($api, $api["status_code"]);
    }



    function generateReferenceNo($branch_id){
        //branch code year series
        $year = date('Y');
        $branch_code = Branch::find($branch_id)->branch_code;

        $last = Transaction::where('reference_no', 'LIKE', $branch_code.'-'.$year.'-%')
                                ->orderBy('id','DESC')
                                ->get()->first();
        if(isset($last['id'])){
            $reference_no = $last['reference_no'];
            $str = explode("-", $reference_no);
            return $branch_code . '-' . $year . '-' . str_pad(($str[2]+1), 8,"0",STR_PAD_LEFT);
        }
        return $branch_code . '-' . $year . '-' . str_pad(1, 8,"0",STR_PAD_LEFT);
    }

    function getFirstServiceTime($services){

        foreach($services as $key => $value){
            if($key === 0)
                return $value['start'];
        }
        return date('Y-m-d') . ' 00:00:00';
    }

    public function validateIfDeviceIsRegistered($arrayKiosk,$serial_no){
        foreach ($arrayKiosk as $key => $valKiosk) {
            $branchSerial = $valKiosk["serial_no"];
            if($branchSerial == $serial_no){
                return true;
            }
        }
        return false;
    }


    function randomPassword($len=10, $abc="aAbBcCdDeEfFgGhHiIjJkKlLmMnNoOpPqQrRsStTuUvVwWxXyYzZ0123456789") {
        $letters = str_split($abc);
        $str = "";
        for ($i=0; $i<=$len; $i++) {
            $str .= $letters[rand(0, count($letters)-1)];
        }
        return $str;
    }


}


