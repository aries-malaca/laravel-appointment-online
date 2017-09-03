<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

//token validator API
Route::get('/user/getUser' ,  'UserController@getUser');

//profile update API
Route::patch('/user/updateProfile' ,  'UserController@updateProfile');

//resend Email Confirmation API
Route::get('/user/resendConfirmation' ,  'UserController@resendConfirmation');