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
Route::patch('/client/updateInfo', 'ClientController@updateInfo');
Route::patch('/client/changePassword', 'ClientController@changePassword');
Route::patch('/client/updateSettings', 'ClientController@updateSettings');


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

Route::get('/config/getTerms', 'ConfigController@getTerms');
Route::get('/config/getConfigs', 'ConfigController@getConfigs');
Route::get('/menu/getAllMenus', 'MenuController@getAllMenus');

Route::get('/appointment/getAppointments/{by}/{id}/{flag}', 'AppointmentController@getAppointments');
Route::get('/appointment/getAppointment/{id}', 'AppointmentController@getAppointment');
Route::post('/appointment/addAppointment', 'AppointmentController@addAppointment');
Route::patch('/appointment/cancelAppointment', 'AppointmentController@cancelAppointment');
Route::patch('/appointment/callAppointment', 'QueuingController@callAppointment');
Route::patch('/appointment/unCallAppointment', 'QueuingController@unCallAppointment');
Route::patch('/appointment/cancelItem', 'AppointmentController@cancelItem');
Route::patch('/appointment/serveAppointment', 'QueuingController@serveAppointment');
Route::patch('/appointment/unServeAppointment', 'QueuingController@unServeAppointment');
Route::patch('/appointment/completeAppointment', 'AppointmentController@completeAppointment');
Route::get('/appointment/expireAppointments', 'AppointmentController@expireAppointments');

Route::get('/waiver/getWaiverQuestions', 'WaiverController@getWaiverQuestions');

Route::patch('/schedule/updateBranchSchedule', 'BranchController@updateBranchSchedule');
Route::post('/schedule/addBranchSchedule', 'BranchController@addBranchSchedule');
Route::patch('/schedule/deleteBranchSchedule', 'BranchController@deleteBranchSchedule');

Route::get('/technician/getTechnicians', 'TechnicianController@getTechnicians');
Route::get('/technician/fetchEMSTechnicians', 'TechnicianController@fetchEMSTechnicians');
Route::get('/technician/getBranchTechnicians/{branch}/{date}', 'TechnicianController@getBranchTechnicians');

Route::get('/premier/getPremiers/{client}/{status}', 'PremierController@getPremiers');
Route::post('/premier/applyPremier', 'PremierController@applyPremier');
Route::post('/premier/movePremier', 'PremierController@movePremier');
Route::post('/premier/recheckPremier', 'PremierController@recheckPremier');
Route::post('/premier/sendPremierVerification', 'PremierController@sendPremierVerification');
Route::post('/premier/sendReviewRequest', 'PremierReviewController@sendReviewRequest');
Route::post('/premier/exportExcel', 'PremierController@exportExcel');
Route::get('/premier/getRequests', 'PremierReviewController@getRequests');
Route::get('/premier/getAllRequests', 'PremierReviewController@getAllRequests');
Route::post('/premier/deleteRequest', 'PremierReviewController@deleteRequest');
Route::patch('/premier/completeRequest', 'PremierReviewController@completeRequest');

Route::get('/stats/getAdminStats', 'StatsController@getAdminStats');

Route::get('/message/getConversation/{partner_id}', 'MessageController@getConversation');
Route::post('/message/sendMessage', 'MessageController@sendMessage');
Route::post('/message/deleteConversation', 'MessageController@deleteConversation');
Route::patch('/message/seenMessages', 'MessageController@seenMessages');
Route::get('/message/getContactList', 'MessageController@getContactList');
Route::get('/message/countUnseenMessages/{partner_id}', 'MessageController@countUnseenMessages');
Route::get('/message/getLastMessage/{sender_id}', 'MessageController@getLastMessage');

Route::get('/faq/getFAQs', 'FAQController@getFAQs');
Route::post('/faq/addFAQ', 'FAQController@addFAQ');
Route::patch('/faq/updateFAQ', 'FAQController@updateFAQ');
Route::post('/faq/deleteFAQ', 'FAQController@deleteFAQ');
Route::patch('/faq/moveFAQ', 'FAQController@moveFAQ');

Route::get('/career/getCareers', 'CareerController@getCareers');
Route::post('/career/addCareer', 'CareerController@addCareer');
Route::patch('/career/updateCareer', 'CareerController@updateCareer');
Route::post('/career/deleteCareer', 'CareerController@deleteCareer');
Route::patch('/career/moveCareer', 'CareerController@moveCareer');

Route::get('/promotion/getPromotions', 'PromotionController@getPromotions');
Route::post('/promotion/addPromotion', 'PromotionController@addPromotion');
Route::patch('/promotion/updatePromotion', 'PromotionController@updatePromotion');
Route::post('/promotion/deletePromotion', 'PromotionController@deletePromotion');



//mobile
//flag = active:string
// 192.168.1.225/api/user/getUsers?token=token_value
// String url = 192.168.1.225/api/user/getUsers?token=token_value

//Load this every splashscreens
Route::get('/mobile/getAppVersion/{getVersionOffline}/{getVersionOnline}/{deviceType}/{deviceName}', 'MobileApiController@getAppVersion');
Route::get('/mobile/getFirstLoadDetails/{version_banner}/{version_commercial}/{version_services}/{version_packages}/{version_products}/{version_branches}', 'MobileApiController@LoadData');

Route::get('/mobile/getClientDetails', 'MobileApiController@getUser');

Route::patch('/mobile/updateHomeBranch', 'MobileApiController@updateHomeBranch');
Route::patch('/mobile/updatePersonalInfo', 'MobileApiController@updatePersonalInfo');
Route::patch('/mobile/updateAccount', 'MobileApiController@updateAccount');
Route::patch('/mobile/uploadUserImage', 'MobileApiController@uploadUserImage');
Route::patch('/mobile/registerUser', 'MobileApiController@registerUser');

Route::post('/mobile/verifyMyPassword', 'MobileApiController@verifyMyPassword');
Route::patch('/mobile/FacebookLogin', 'MobileApiController@FacebookLogin');

Route::patch('/mobile/updateTerms', 'MobileApiController@updateTerms');
// Route::post('/forgot/requestPassword', 'PasswordController@requestPassword');
Route::get('/mobile/getPackageWithDescription/{flag}', 'MobileApiController@getPackageWithDescription');
Route::get('/mobile/getServices/{flag}', 'MobileApiController@getServices');
Route::get('/mobile/getServices/', 'MobileApiController@getServices');
Route::get('/mobile/getPLCDetails/{ifAll}', 'PremierController@getPLCDetails');
Route::get('/mobile/getRequests/getAllPLCRequestAndApplication', 'MobileApiController@getPLCAllLogs');

Route::get('/mobile/getClientTransactions/{clientid}', 'MobileApiController@getClientTransactions');
Route::get('/mobile/getTotalTransactionAmount', 'MobileApiController@getTotalTransactionAmount');

//get appointment & events by start and end month
Route::post('/mobile/getAppointmentsByMonth', 'MobileApiController@getAppointmentsByMonth');

