<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\PremierLoyaltyCard;
use App\User;
use App\Config;
use App\Branch;
use Mail;
use Validator;
use Excel;
use App\PlcReviewRequest;
use App\Exports\PremiereExport;

class PremierController extends Controller{
    function getPremiers(Request $request){
        if($request->segment(4) != 'all')
            $premiers = PremierLoyaltyCard::where('client_id','=', $request->segment(4));
        else
            $premiers = PremierLoyaltyCard::where('id','>',0);

        if($request->segment(5) != 'all')
            $premiers = $premiers->where('status', $request->segment(5));

        $premiers = $premiers->get()->toArray();

        foreach($premiers as $key=>$value){
            $branch = Branch::find($value['branch_id']);
            $branch_name = isset($branch->id)?$branch->branch_name:'N/A';
            $premiers[$key]['branch_name']  = $branch_name;
            $premiers[$key]['client']  = User::where('id', $value['client_id'])
                                                ->select('birth_date', 'user_mobile', 'first_name', 'last_name', 'middle_name', 'username',
                                                            'email', 'gender','user_address')->get()->first();
            $premiers[$key]['plc_data']     = json_decode($value['plc_data']);
            $premiers[$key]['date_applied'] = date('m/d/Y', strtotime($value['created_at']));
        }
        return response()->json($premiers);
    }

    function applyPremier(Request $request){
        $validator = Validator::make($request->all(), [
            'branch' => 'required',
            'type' =>   'required|in:New,Replacement',
        ]);

        if ($validator->fails())
            return response()->json(['result'=>'failed','error'=>$validator->errors()->all()], 400);

        $api = $this->authenticateAPI();
        if($api['result'] === 'success') {

            if ($status = $this->hasPendingApplication($api['user']['id']))
                return response()->json(['result' => 'failed', 'error' => "You already have " . $status . " application."], 400);

            if ($request->input('type') == 'New') {
                if (!$this->isQualifiedForNew($api['user']['id']))
                    return response()->json(['result' => 'failed', 'error' => "Not qualified for new application."], 400);
            } 
            else {
                if (!$this->isQualifiedForReplacement($api['user']['id']))
                    return response()->json(['result' => 'failed', 'error' => "Not qualified for replacement."], 400);
            }

            $amount = $this->evaluatePremier($api['user']['email']);
            $boss_client = $this->getBossClient($api['user']['email']);

            if (!empty($boss_client)) {
                if (!$amount) {
                    $find = PremierLoyaltyCard::where('client_id', $api['user']['id'])
                                                ->where('status', 'denied')->get()->first();
                    if (isset($find['id']))
                        $premier = PremierLoyaltyCard::find($find['id']);
                }

                if (!isset($premier))
                    $premier = new PremierLoyaltyCard;

                $data = $request->input('type')=='New'?array("reason"=>""):array("reason"=>$request->input('reason'));

                $premier->client_id = $api['user']['id'];
                $premier->branch_id = $request->input('branch')['value'];
                $premier->application_type = $request->input('type');
                $premier->platform = $request->input('platform');
                $premier->status = $amount ? 'approved' : 'denied';
                $premier->reference_no = $boss_client['custom_client_id'];
                $premier->remarks = $amount ? '' : 'Failed to reach the qualified amount';
                $premier->plc_data = json_encode($data);
                $premier->created_at = date('Y-m-d H:i:s');
                $premier->save();

                $u                  = User::find($premier->client_id);
                $user_data          = json_decode($u->user_data);
                $user_data->boss_id = $boss_client['custom_client_id'];
                $u->user_data       = json_encode($user_data);
                $u->save();

                if($premier->remarks=='')
                    return response()->json(["result" => 'success', "amount" => $amount]);
                else
                    return response()->json(["result" => 'failed', "error" => $premier->remarks], 400);
            }

            return response()->json(["result" => "failed", "error" => "Couldn't fetch BOSS Transactions. Please try after a few moment."], 400);
        }
        return response()->json($api, $api["status_code"]);
    }

    function hasPendingApplication($client_id){
        $find = PremierLoyaltyCard::where('status', '<>','denied')
                                    ->where('status', '<>','picked_up')
                                    ->where('status', '<>','deleted')
                                    ->where('client_id', $client_id)->orderBy('created_at', 'DESC')->get()->first();
        if(isset($find['id']))
            return $find['status']=='approved'?'pending':$find['status'];

        return false;
    }

    function isQualifiedForNew($client_id){
        return PremierLoyaltyCard::where('status', '=','picked_up')
                ->where('client_id', $client_id)
                ->count() == 0;
    }

    function isQualifiedForReplacement($client_id){
        return PremierLoyaltyCard::where('status', '=','picked_up')
                                ->where('client_id', $client_id)
                                ->count()>0;
    }

    function evaluatePremier($email){
        $url = Config::where('config_name', 'FETCH_BOSS_TRANSACTIONS')->get()->first();

        if(isset($url['id'])){
            $transactions = json_decode(file_get_contents($url->config_value . $email), true);
            $total = 0;
            $minimum = Config::where('config_name', 'PLC_MINIMUM_TRANSACTIONS_AMOUNT')->get()->first();

            if(isset($minimum['id']))
                $minimum = $minimum->config_value;

            foreach($transactions as $key=>$value)
                $total += $value['net_amount'];
            return ($total >= $minimum ? $total:false);
        }

        return false;
    }

    public function sendPremierVerification(Request $request){
        $api = $this->authenticateAPI();
        $u = false;

        if($api['result'] === 'success'){
            //this block for resend purposes
            $u = $this->dispatchPremierVerification($api['user']['email'], $request->input("data"), $request->input("result"));
        }

        if($u)
            return response()->json(["result"=>"success"]);

        return response()->json(["result"=>"failed"]);
    }

    public function dispatchPremierVerification($email, $data, $result){
        $user = User::where('email', $email)->get()->first();

        if(isset($user['id'])){
            if($result){
                $user_data = json_decode($user['user_data']);
                $user_data->premier_status = 1;

                User::where('email', $email)
                    ->update(["user_data" => json_encode($user_data)]);
            }

            $headers = array("subject"=>'Premier Loyalty Card Application',
                             "to"=> [["email"=>$email, "name"=>  $user['username']]]);
            $this->sendMail('email.plc_application', ["user"=>$user, "data"=>$data], $headers);
            $this->sendMail('email.plc_result', ["user"=>$user, "result"=>$result], $headers);

            return true;
        }
        return false;
    }

    function exportExcel(Request $request){
        $time = time();
        $api = $this->authenticateAPI();

        if($api['result'] === 'success'){
            Excel::store(new PremiereExport($request), 'temp/PLC_' . $time.'.xlsx', 'public_root');
            return response()->json(["result"=>"success", "filename"=>'PLC_' . $time.'.xlsx']);
        }

        return response()->json(["result"=>"failed"]);
    }

    function movePremier(Request $request){
        $api = $this->authenticateAPI();

        if($api['result'] === 'success'){
            PremierLoyaltyCard::whereIn('id', $request->input('selected'))
                                ->update(["status"=>$request->input('move_to')]);
            
            return response()->json(["result"=>"success"]);
        }

        return response()->json(["result"=>"failed"]);
    }

    //mobile - usage
    function getPLCDetails(Request $request){

        $api = $this->authenticateAPI();
        $ifAll      = $request->segment(4);
        if($api['result'] === 'success'){
            $client_id  = $api['user']['id'];
            $response   = array();
            if ($ifAll == "false") {
                $premiers = PremierLoyaltyCard::where('client_id', '=', $client_id)->orderBy('created_at', 'DESC')->get()->first();
                if (isset($premiers['id'])) {
                    $branch = Branch::find($premiers['branch_id']);
                    $branch_name = isset($branch->id) ? $branch->branch_name : 'N/A';
                    $premiers['branch_name'] = $branch_name;
                    $premiers['plc_data'] = json_decode($premiers['plc_data']);
                    $premiers['date_applied'] = date('m/d/Y', strtotime($premiers['created_at']));
                }
            } 
            else {
                $premiers = PremierLoyaltyCard::where('client_id', '=', $client_id)->orderBy('created_at', 'DESC')->get();
                foreach ($premiers as $key => $value) {
                    $branch = Branch::find($value['branch_id']);
                    $branch_name = isset($branch->id) ? $branch->branch_name : 'N/A';
                    $premiers[$key]['branch_name'] = $branch_name;
                    $premiers[$key]['plc_data'] = json_decode($value['plc_data']);
                    $premiers[$key]['date_applied'] = date('m/d/Y', strtotime($value['created_at']));
                }
            }
            $data = PlcReviewRequest::where('client_id', $client_id)->get()->toArray();
            foreach($data as $key=>$value){
                $client = User::find($value['client_id']);
                $data[$key]['name'] = ($client->id?$client->username:'');
                $data[$key]['status_html'] = '<span class="badge '.($value['status']=='pending'?'badge-info':'badge-success').'">'. $value['status'] .'</span>';
                $user = User::find($value['updated_by_id']);
                $data[$key]['updated_by'] =  (isset($user->id)?$user->username:'');
                $data[$key]['processed_date_formatted'] =  isset($value['processed_date'])?date('m/d/Y',strtotime($value['processed_date'])):'';
            }

            $response["application"] = $premiers;
            $response["request"]     = $data;
            return response()->json($response);
       }
       return response()->json($api, $api["status_code"]);
    }
}