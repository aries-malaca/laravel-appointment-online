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
Route::patch('/user/destroyToken', 'UserController@destroyToken');

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
Route::get('/branch/getBranch/{id}', 'BranchController@getBranch');
Route::post('/branch/uploadPicture', 'BranchController@uploadPicture');
Route::patch('/branch/removePicture', 'BranchController@removePicture');
Route::post('/branch/addBranch', 'BranchController@addBranch');
Route::patch('/branch/updateBranch', 'BranchController@updateBranch');

Route::get('/branch/getClusters', 'BranchController@getClusters');
Route::post('/branch/addCluster', 'BranchController@addCluster');
Route::patch('/branch/updateCluster', 'BranchController@updateCluster');

Route::get('/region/getRegions', 'RegionController@getRegions');
Route::post('/region/addRegion', 'RegionController@addRegion');
Route::patch('/region/updateRegion', 'RegionController@updateRegion');

Route::get('/city/getCities', 'CityController@getCities');
Route::post('/city/addCity', 'CityController@addCity');
Route::patch('/city/updateCity', 'CityController@updateCity');

Route::get('/product/getProducts/{flag}', 'ProductController@getProducts');
Route::get('/product/getProducts', 'ProductController@getProducts');
Route::post('/product/addProduct', 'ProductController@addProduct');
Route::patch('/product/updateProduct', 'ProductController@updateProduct');

Route::get('/product/getProductGroups/{flag}', 'ProductController@getProductGroups');
Route::get('/product/getProductGroups', 'ProductController@getProductGroups');
Route::post('/product/addProductGroup', 'ProductController@addProductGroup');
Route::patch('/product/updateProductGroup', 'ProductController@updateProductGroup');
Route::post('/product/uploadPicture', 'ProductController@uploadPicture');

Route::get('/service/getServices/{flag}', 'ServiceController@getServices');
Route::get('/service/getServices', 'ServiceController@getServices');
Route::post('/service/addService', 'ServiceController@addService');
Route::patch('/service/updateService', 'ServiceController@updateService');
Route::post('/service/uploadPicture', 'ServiceController@uploadPicture');

Route::get('/service/getServiceTypes/{flag}', 'ServiceController@getServiceTypes');
Route::get('/service/getServiceTypes', 'ServiceController@getServiceTypes');
Route::post('/service/addServiceType', 'ServiceController@addServiceType');
Route::patch('/service/updateServiceType', 'ServiceController@updateServiceType');

Route::get('/service/getServicePackages/{flag}', 'ServiceController@getServicePackages');
Route::get('/service/getServicePackages', 'ServiceController@getServicePackages');
Route::post('/service/addServicePackage', 'ServiceController@addServicePackage');
Route::patch('/service/updateServicePackage', 'ServiceController@updateServicePackage');