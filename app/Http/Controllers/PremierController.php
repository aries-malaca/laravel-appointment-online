<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\PremierLoyaltyCard;
use App\User;
use App\Config;
use Mail;
use Validator;

class PremierController extends Controller{
    function getPremiers(Request $request){
        return response()->json([]);
    }

    function applyPremier(Request $request){
        $validator = Validator::make($request->all(), [
            'branch' => 'required',
            'type' =>   'required|in:New,Replacement',
        ]);

        if ($validator->fails())
            return response()->json(['result'=>'failed','error'=>$validator->errors()->all()], 400);

        $api = $this->authenticateAPI();
        if($api['result'] === 'success'){

            $amount = $this->evaluatePremier($api['user']['email']);
            $boss_id = $this->getBossID($api['user']['email']);

            if(is_array($boss_id)){
                $premier = new PremierLoyaltyCard;
                $premier->client_id = $api['user']['id'];
                $premier->branch_id = $request->input('branch')['value'];
                $premier->application_type = $request->input('type');
                $premier->platform = $request->input('platform');
                $premier->status = $amount?'approved':'denied';
                $premier->reference_no = $boss_id[0];
                $premier->remarks = $amount?'':'Failed to reach the qualified amount';
                $premier->plc_data = '{}';
                $premier->save();

                if($amount)
                    return response()->json(["result" => "success", "amount"=>$amount]);
            }

            return response()->json(["result" => "failed", "error" => "Couldn't fetch BOSS Transactions. Please try after a few moment."]);
        }
        return response()->json($api, $api["status_code"]);
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