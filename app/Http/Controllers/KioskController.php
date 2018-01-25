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
use App\Product;
use Mail;

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
                        $arrayProductQuery      = Product::whereIn("id",json_decode($clusterQuery["products"],true))
                                                    ->get()
                                                    ->toArray();

                        $response['services'] = $arrayServiceQuery;
                        $response['products'] = $arrayProductQuery;
                        $response["branches"] = $branchQuery;
                        $response["user"]     = $u;
                        $response['token']    = $token; 
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

}


