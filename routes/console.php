<?php

use Illuminate\Foundation\Inspiring;
use App\Transaction;
use App\TransactionItem;
use App\Review;
use Illuminate\Support\Facades\DB;
use Ixudra\Curl\Facades\Curl;
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
Artisan::command('expire',function(){
    Curl::to(env('APP_URL').'/api/appointment/expireAppointments')->get();
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
    Curl::to(env('APP_URL').'/api/technician/fetchEMSTechnicians/' . $cluster_id)->get();
})->describe('Fetch technicians with cluster_id');