<?php
namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use App\PremierLoyaltyCard;
use App\Branch;
use App\User;

class PremiereExport implements FromCollection{
    var $premiers = [];
    public function __construct($request){
        $premiers = PremierLoyaltyCard::whereIn('id', $request->input('selected'))
            ->get();
        foreach($premiers as $key=>$value){
            $branch = Branch::find($value['branch_id']);
            $branch_name = isset($branch->id)?$branch->branch_name:'N/A';
            $premiers[$key]['branch_name']  = $branch_name;
            $premiers[$key]['client']  = User::where('id', $value['client_id'])
                ->select('birth_date', 'user_mobile', 'first_name', 'last_name', 'middle_name', 'username',
                    'email', 'gender','user_address','user_data')->get()->first();
            $premiers[$key]['client']['user_data'] = json_decode($premiers[$key]['client']['user_data']);
        }

        foreach($premiers as $k=>$v){
            $data = json_decode($v['plc_data'],true);
            $this->premiers[] = array('BOSS ID', 'Client', 'Email', 'Contact', 'Birthdate', 'Address', 'Branch', 'Type', 'Replacement Reason', 'Status');
            $this->premiers[] =
                array(
                    $v['client']['user_data']->boss_id,
                    $v['client']['first_name'].' '.$v['client']['last_name'],
                    $v['client']['email'],
                    $v['client']['user_mobile'],
                    date('m/d/Y',strtotime($v['client']['birth_date'])),
                    $v['client']['user_address'],
                    $v['branch_name'],
                    $v['application_type'],
                    $data['reason'],
                    $v['status']
                );
        }
    }

    public function collection(){
        return collect($this->premiers);
    }
}