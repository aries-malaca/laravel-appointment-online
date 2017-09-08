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
Route::get('/user/getUser', 'UserController@getUser');
Route::get('/user/getUsers', 'UserController@getUsers');

Route::post('/user/addUser', 'UserController@addUser');
Route::patch('/user/updateUser', 'UserController@updateUser');

//profile update API
Route::patch('/user/updateProfile', 'UserController@updateProfile');
Route::patch('/user/changePassword', 'UserController@changePassword');
Route::post('/user/uploadPicture', 'UserController@uploadPicture');

Route::get('/user/getUserLevels', 'UserLevelController@getUserLevels');
Route::post('/user/addUserLevel', 'UserLevelController@addUserLevel');
Route::patch('/user/updateUserLevel', 'UserLevelController@updateUserLevel');

//FB Login
Route::post('/user/fbLogin', 'UserController@fbLogin');

//clients
Route::get('/client/searchClients', 'ClientController@searchClients');
Route::get('/client/getClient/{id}', 'ClientController@getClient');

//resend Email Confirmation API
Route::get('/user/resendConfirmation', 'UserController@resendConfirmation');

Route::get('/branch/getBranches/{flag}', 'BranchController@getBranches');
Route::get('/branch/getBranches', 'BranchController@getBranches');

Route::get('/region/getRegions', 'RegionController@getRegions');
Route::post('/region/addRegion', 'RegionController@addRegion');
Route::patch('/region/updateRegion', 'RegionController@updateRegion');

Route::get('/city/getCities', 'CityController@getCities');
Route::post('/city/addCity', 'CityController@addCity');
Route::patch('/city/updateCity', 'CityController@updateCity');


