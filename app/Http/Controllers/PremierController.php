<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\PremierLoyaltyCard;
use App\User;
use App\Config;
use App\Branch;
use Mail;
use Validator;

class PremierController extends Controller{
    function getPremiers(Request $request){
        if($request->segment(4) != 'all')
            $premiers = PremierLoyaltyCard::where('client_id','=', $request->segment(4));
        else
            $premiers = PremierLoyaltyCard::where('id','>',0);

        if($request->segment(5) != 'all')
            $premiers = $premiers->where('status','=', $request->segment(4));

        $premiers = $premiers->get()->toArray();

        foreach($premiers as $key=>$value){
            $branch = Branch::find($value['branch_id']);
            $branch_name = isset($branch->id)?$branch->branch_name:'N/A';

            $premiers[$key]['branch_name'] = $branch_name;
            $premiers[$key]['plc_data'] = json_decode($value['plc_data']);
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
            } else {
                if (!$this->isQualifiedForReplacement($api['user']['id']))
                    return response()->json(['result' => 'failed', 'error' => "Not qualified for replacement."], 400);
            }

            $amount = $this->evaluatePremier($api['user']['email']);
            $boss_id = $this->getBossID($api['user']['email']);

            if (!empty($boss_id)) {
                if (!$amount) {
                    $find = PremierLoyaltyCard::where('client_id', $api['user']['id'])
                        ->where('status', 'denied')->get()->first();
                    if (isset($find['id']))
                        $premier = PremierLoyaltyCard::find($find['id']);
                }

                if (!isset($premier))
                    $premier = new PremierLoyaltyCard;

                $premier->client_id = $api['user']['id'];
                $premier->branch_id = $request->input('branch')['value'];
                $premier->application_type = $request->input('type');
                $premier->platform = $request->input('platform');
                $premier->status = $amount ? 'approved' : 'denied';
                $premier->reference_no = $boss_id[0];
                $premier->remarks = $amount ? '' : 'Failed to reach the qualified amount';
                $premier->plc_data = '{}';
                $premier->created_at = date('Y-m-d H:i:s');
                $premier->save();


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
                                    ->where('client_id', $client_id)->orderBy('created_at', 'DESC')->get()->first();

        if(isset($find['id']))
            return $find['status']=='approved'?'pending':$find['status'];

        return false;
    }

    function isQualifiedForNew($client_id){
        return PremierLoyaltyCard::where('status', '=','picked-up')
                ->where('client_id', $client_id)
                ->count() == 0;
    }

    function isQualifiedForReplacement($client_id){
        return PremierLoyaltyCard::where('status', '=','picked-up')
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

        //default return if not authenticated
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

            Mail::send('email.plc_application', ["user"=>$user, "data"=>$data], function ($message) use($user) {
                $message->from('notification@system.lay-bare.com', 'LBO');
                $message->subject('Premier Loyalty Card Application');
                $message->to('aries@lay-bare.com', $user['username']);
            });

            Mail::send('email.plc_result', ["user"=>$user, "result"=>$result], function ($message) use($user) {
                $message->from('notification@system.lay-bare.com', 'LBO');
                $message->subject('Premier Loyalty Card Application');
                $message->to('aries@lay-bare.com', $user['username']);
            });

            return true;
        }
        return false;
    }
}