<?php

use Illuminate\Foundation\Inspiring;
use App\Transaction;
use App\TransactionItem;
use App\Review;
use Illuminate\Support\Facades\DB;
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
    file_get_contents(env('APP_URL').'/api/appointment/expireAppointments');
})->describe('Executes expire appointment');

Artisan::command('reset-data',function(){
    Transaction::truncate();
    TransactionItem::truncate();
    Review::truncate();
})->describe('Truncate transactions, transaction_items, reviews');

Artisan::command('auditing:clear',function(){
    DB::table('audits')->delete();
})->describe('Clear audits');

Artisan::command('auditing:clean',function(){
    DB::table('audits')->where('new_values', '[]')->where('event', 'updated')->delete();
})->describe('Clear audits having no data');

Artisan::command('fetch-technicians {cluster_id}',function($cluster_id){
    file_get_contents(env('APP_URL').'/api/technician/fetchEMSTechnicians/' . $cluster_id);
})->describe('Fetch technicians with cluster_id');