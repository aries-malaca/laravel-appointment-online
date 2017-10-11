<?php

use Illuminate\Foundation\Inspiring;
use App\Transaction;
use App\TransactionItem;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->describe('Display an inspiring quote');


Artisan::command('expire',function(){
    Transaction::where("transaction_datetime","<", date('Y-m-d H:i',time()-86400))
                ->where('transaction_status','reserved')
                ->update(["transaction_status"=>"expired"]);
    TransactionItem::where("book_start_time","<", date('Y-m-d H:i',time()-86400))
        ->where('item_status','reserved')
        ->update(["item_status"=>"expired"]);
});


Artisan::command('pull-technicians',function(){

});