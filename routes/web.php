<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', 'HomeController@index');
Route::get('/home', 'HomeController@index');
Route::get('/waiver/{id}', 'WaiverController@viewWaiver');
Route::get('/forgot', 'PasswordController@index');
Route::post('/forgot/requestPassword', 'PasswordController@requestPassword');
Route::get('/forgot/verify', 'PasswordController@verifyPassword');

Route::post('/auth/login' ,  'UserController@login');
Route::post('/auth/loginKiosk' ,  'KioskController@login');
Route::post('/auth/register' ,  'UserController@register');
Route::get('/register/verify', 'UserController@registerVerify');
Route::get('/queuing/{platform}/{branch_id}', 'QueuingController@index');

Auth::routes();