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
Route::post('/user/updateUser', 'UserController@updateUser');
Route::post('/user/destroyToken', 'UserController@destroyToken');

//profile update API
Route::post('/user/updateProfile', 'UserController@updateProfile');
Route::post('/user/changePassword', 'UserController@changePassword');
Route::post('/user/uploadPicture', 'UserController@uploadPicture');

Route::get('/user/getPermissions', 'UserLevelController@getPermissions');
Route::get('/user/getUserLevels', 'UserLevelController@getUserLevels');
Route::post('/user/addUserLevel', 'UserLevelController@addUserLevel');
Route::post('/user/updateUserLevel', 'UserLevelController@updateUserLevel');
Route::post('/user/saveLocation', 'UserController@saveLocation');

Route::post('/user/saveLocation', 'UserController@saveLocation');

//logs
Route::get('/audits/getAudits/{id}', 'AuditController@getAudits');
//logs

//FB Login
Route::post('/user/fbLogin', 'UserController@fbLogin');

//clients
Route::get('/client/searchClients', 'ClientController@searchClients');
Route::post('/client/filterClients', 'ClientController@filterClients');
Route::get('/client/getClient/{id}', 'ClientController@getClient');
Route::post('/client/updateInfo', 'ClientController@updateInfo');
Route::post('/client/changePassword', 'ClientController@changePassword');
Route::post('/client/updateTransactionData', 'ClientController@updateTransactionData');

//resend Email Confirmation API
Route::get('/user/sendConfirmation', 'UserController@sendConfirmation');

Route::get('/branch/getBranches/{flag}', 'BranchController@getBranches');
Route::get('/branch/getBranches', 'BranchController@getBranches');
Route::get('/branch/getBranch/{id}', 'BranchController@getBranch');
Route::get('/branch/getBranchSupervisor/{id}', 'BranchController@getBranchSupervisor');
Route::post('/branch/uploadPicture', 'BranchController@uploadPicture');
Route::post('/branch/removePicture', 'BranchController@removePicture');
Route::post('/branch/addBranch', 'BranchController@addBranch');
Route::post('/branch/updateBranch', 'BranchController@updateBranch');

Route::get('/branch/getClusters', 'BranchController@getClusters');
Route::post('/branch/addCluster', 'BranchController@addCluster');
Route::post('/branch/updateCluster', 'BranchController@updateCluster');
Route::post('/branch/registerKiosk', 'BranchController@registerKiosk');
Route::post('/branch/unregisterKiosk', 'BranchController@unregisterKiosk');

Route::get('/region/getRegions', 'RegionController@getRegions');
Route::post('/region/addRegion', 'RegionController@addRegion');
Route::post('/region/updateRegion', 'RegionController@updateRegion');

Route::get('/city/getCities', 'CityController@getCities');
Route::post('/city/addCity', 'CityController@addCity');
Route::post('/city/updateCity', 'CityController@updateCity');

Route::get('/product/getProducts/{flag}', 'ProductController@getProducts');
Route::get('/product/getProducts', 'ProductController@getProducts');
Route::post('/product/addProduct', 'ProductController@addProduct');
Route::post('/product/updateProduct', 'ProductController@updateProduct');

Route::get('/product/getProductGroups/{flag}', 'ProductController@getProductGroups');
Route::get('/product/getProductGroups', 'ProductController@getProductGroups');
Route::post('/product/addProductGroup', 'ProductController@addProductGroup');
Route::post('/product/updateProductGroup', 'ProductController@updateProductGroup');
Route::post('/product/uploadPicture', 'ProductController@uploadPicture');

Route::get('/service/getServices/{flag}', 'ServiceController@getServices');
Route::get('/service/getServices', 'ServiceController@getServices');
Route::post('/service/addService', 'ServiceController@addService');
Route::post('/service/updateService', 'ServiceController@updateService');
Route::post('/service/uploadPicture', 'ServiceController@uploadPicture');

Route::get('/service/getServiceTypes/{flag}', 'ServiceController@getServiceTypes');
Route::get('/service/getServiceTypes', 'ServiceController@getServiceTypes');
Route::post('/service/addServiceType', 'ServiceController@addServiceType');
Route::post('/service/updateServiceType', 'ServiceController@updateServiceType');

Route::get('/service/getServicePackages/{flag}', 'ServiceController@getServicePackages');
Route::get('/service/getServicePackages', 'ServiceController@getServicePackages');
Route::post('/service/addServicePackage', 'ServiceController@addServicePackage');
Route::post('/service/updateServicePackage', 'ServiceController@updateServicePackage');

Route::get('/config/getTerms', 'ConfigController@getTerms');
Route::get('/config/getConfigs', 'ConfigController@getConfigs');
Route::get('/menu/getAllMenus', 'MenuController@getAllMenus');

Route::get('/appointment/getAppointments/{by}/{id}/{flag}', 'AppointmentController@getAppointments');
Route::get('/appointment/getAppointment/{id}', 'AppointmentController@getAppointment');
Route::post('/appointment/addAppointment', 'AppointmentController@addAppointment');
Route::post('/appointment/cancelAppointment', 'AppointmentController@cancelAppointment');
Route::post('/appointment/cancelItem', 'AppointmentController@cancelItem');
Route::post('/appointment/serveAppointment', 'QueuingController@serveAppointment');
Route::post('/appointment/unServeAppointment', 'QueuingController@unServeAppointment');
Route::post('/appointment/completeAppointment', 'AppointmentController@completeAppointment');
Route::get('/appointment/expireAppointments', 'AppointmentController@expireAppointments');
Route::post('/appointment/acknowledgeAppointment', 'AppointmentController@acknowledgeAppointment');

Route::get('/waiver/getWaiverQuestions', 'WaiverController@getWaiverQuestions');

Route::post('/schedule/updateBranchSchedule', 'BranchController@updateBranchSchedule');
Route::post('/schedule/addBranchSchedule', 'BranchController@addBranchSchedule');
Route::post('/schedule/deleteBranchSchedule', 'BranchController@deleteBranchSchedule');

Route::post('/schedule/updateTechnicianShift', 'BranchController@updateTechnicianShift');
Route::get('/schedule/getTechnicianShifts/{branch}', 'BranchController@getTechnicianShifts');
Route::post('/schedule/addTechnicianShift', 'BranchController@addTechnicianShift');
Route::post('/schedule/deleteTechnicianShift', 'BranchController@deleteTechnicianShift');

Route::get('/technician/getTechnicians', 'TechnicianController@getTechnicians');
Route::get('/technician/getTechnician/{id}', 'TechnicianController@getTechnician');
Route::get('/technician/fetchEMSTechnicians/{id}', 'TechnicianController@fetchEMSTechnicians');
Route::get('/technician/getBranchTechnicians/{branch}/{date}', 'TechnicianController@getBranchTechnicians');
Route::post('/technician/addTechnician', 'TechnicianController@addTechnician');
Route::post('/technician/updateTechnician', 'TechnicianController@updateTechnician');
Route::get('/technician/getSchedules/{id}', 'TechnicianController@getSchedules');
Route::post('/technician/addRegularSchedule', 'TechnicianController@addRegularSchedule');
Route::post('/technician/addSingleSchedule', 'TechnicianController@addSingleSchedule');
Route::post('/technician/updateRegularSchedule', 'TechnicianController@updateRegularSchedule');
Route::post('/technician/deleteSchedule', 'TechnicianController@deleteSchedule');

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
Route::post('/premier/completeRequest', 'PremierReviewController@completeRequest');

Route::get('/stats/getAdminStats', 'StatsController@getAdminStats');

Route::get('/message/getConversation/{partner_id}/{limit}', 'MessageController@getConversation');
Route::post('/message/sendMessage', 'MessageController@sendMessage');
Route::post('/message/deleteConversation', 'MessageController@deleteConversation');
Route::post('/message/seenMessages', 'MessageController@seenMessages');
Route::get('/message/getUnreadMessages', 'MessageController@getUnreadMessages');
Route::get('/message/getLastMessage/{sender_id}', 'MessageController@getLastMessage');

Route::get('/faq/getFAQs', 'FAQController@getFAQs');
Route::post('/faq/addFAQ', 'FAQController@addFAQ');
Route::post('/faq/updateFAQ', 'FAQController@updateFAQ');
Route::post('/faq/deleteFAQ', 'FAQController@deleteFAQ');
Route::post('/faq/moveFAQ', 'FAQController@moveFAQ');

Route::get('/career/getCareers', 'CareerController@getCareers');
Route::post('/career/addCareer', 'CareerController@addCareer');
Route::post('/career/updateCareer', 'CareerController@updateCareer');
Route::post('/career/deleteCareer', 'CareerController@deleteCareer');
Route::post('/career/moveCareer', 'CareerController@moveCareer');

Route::get('/promotion/getPromotions', 'PromotionController@getPromotions');
Route::get('/promotion/getPerks', 'PromotionController@getPerks');
Route::post('/promotion/addPromotion', 'PromotionController@addPromotion');
Route::post('/promotion/updatePromotion', 'PromotionController@updatePromotion');
Route::post('/promotion/addPerk', 'PromotionController@addPerk');
Route::post('/promotion/updatePerk', 'PromotionController@updatePerk');
Route::post('/promotion/uploadPicture', 'PromotionController@uploadPicture');

Route::get('/review/getReviews/{by}/{id}', 'ReviewController@getReviews');
Route::get('/review/getReview/{id}', 'ReviewController@getReview');
Route::post('/review/submitReview', 'ReviewController@submitReview');

//sms
Route::get('/campaign/getTemplates', 'CampaignController@getTemplates');
Route::post('/campaign/sendCampaign', 'CampaignController@sendCampaign');
Route::post('/campaign/addTemplate', 'CampaignController@addTemplate');
Route::post('/campaign/updateTemplate', 'CampaignController@updateTemplate');
Route::post('/campaign/deleteTemplate', 'CampaignController@deleteTemplate');
Route::post('/campaign/uploadFile', 'CampaignController@uploadFile');
Route::post('/campaign/removeFile', 'CampaignController@removeFile');
//

//contact
Route::get('/contact/importContacts/{file}/{extension}', 'ContactController@importContacts');
Route::get('/contact/getContacts', 'ContactController@getContacts');
Route::post('/contact/addContact', 'ContactController@addContact');
Route::post('/contact/updateContact', 'ContactController@updateContact');
Route::post('/contact/deleteContact', 'ContactController@deleteContact');
Route::get('/contact/getContactList', 'ContactController@getContactList');
//

//notifications
Route::get('/notification/getUserNotifications', 'NotificationController@getUserNotifications');
//


//mobile
//flag = active:string
// 192.168.1.225/api/user/getUsers?token=token_value
// String url = 192.168.1.225/api/user/getUsers?token=token_value

//Load this every splashscreens
Route::get('/mobile/getAppVersion/{getVersion}/{deviceType}/{deviceName}', 'MobileApiController@getAppVersion');
Route::get('/mobile/getFirstLoadDetails/{version_banner}/{version_commercial}/{version_services}/{version_packages}/{version_products}/{version_branches}', 'MobileApiController@LoadData');
Route::post('/mobile/loginUser', 'MobileApiController@loginUser');
Route::post('/mobile/updateHomeBranch', 'MobileApiController@updateHomeBranch');
Route::post('/mobile/updatePersonalInfo', 'MobileApiController@updatePersonalInfo');
Route::post('/mobile/updateAccount', 'MobileApiController@updateAccount');
Route::post('/mobile/uploadUserImage', 'MobileApiController@uploadUserImage');
Route::post('/mobile/registerUser', 'MobileApiController@registerUser');
Route::post('/mobile/verifyMyPassword', 'MobileApiController@verifyMyPassword');
Route::post('/mobile/FacebookLogin', 'MobileApiController@FacebookLogin');
Route::post('/mobile/updateTerms', 'MobileApiController@updateTerms');
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
//get branch schedules
Route::get('/mobile/getBranchSchedules/{branch_id}/{date}', 'MobileApiController@getBranchSchedules');
//get branch reviews
Route::get('/mobile/getBranchRatings/{branch_id}/{offset}', 'MobileApiController@getBranchRatings');
Route::post('/mobile/reviews/reviewTransaction', 'MobileApiController@reviewTransaction');
Route::get('/mobile/getAppointmentReview', 'MobileApiController@getAppointmentReview');
//queuing(short version)
Route::get('/kiosk/getQueue/{branch_id}', 'KioskController@getTodaysQueue');
Route::get('/mobile/getChatMessage/{recipientID}/{offset}/{latestlastChatID}/{previouslastID}/{ifLatest}', 'MobileApiController@getChatMessage');
Route::post('/mobile/sendChatMessage', 'MobileApiController@sendChatMessage');
Route::get('/mobile/getNotifications', 'MobileApiController@getNotifications');



//Kiosk Configuration (Lay Bare)
Route::get('/kiosk/checkLoggedInToken/{branch_id}', 'KioskController@checkLoggedInToken');
Route::post('/kiosk/getClientRecords', 'KioskController@getClientRecords');
Route::post('/kiosk/addAppointments', 'KioskController@addAppointments');
Route::post('/kiosk/loginClient', 'KioskController@loginClient');
Route::post('/kiosk/loginClient', 'KioskController@loginClient');
Route::post('/kiosk/saveNewUser', 'KioskController@saveNewUser');
//authenticate settings
Route::post('/kiosk/settings/getSettings', 'KioskController@verifyUserSettings');
Route::post('/kiosk/checkDeviceIfRegistered', 'KioskController@checkDeviceIfRegistered');
Route::post('/kiosk/searchClient', 'KioskController@searchClient');
Route::post('/kiosk/addWaiver', 'KioskController@addWalkinWaiver');





