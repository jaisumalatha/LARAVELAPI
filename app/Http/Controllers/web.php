<?php

use Illuminate\Support\Facades\Route;

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

/*Route::get('/', function () {
    return view('welcome');
});*/



Route::get('/','HomeController@index');
Route::get('comingsoon','HomeController@comingsoon');
Route::prefix('/')->group(__DIR__.'/admin.php');

//STAFF ENTRY
Route::get('staff_entry','Admin\AD_Staff_Controller@entry');
Route::post('staff_store','Admin\AD_Staff_Controller@store');
Route::get('staff_view','Admin\AD_Staff_Controller@view');
Route::get('staff_change_status/{user_code}','Admin\AD_Staff_Controller@changeStatus');

Route::get('staff_login_view_1','Admin\AD_Staff_Login_Controller@view_1');
Route::get('staff_login_view_2','Admin\AD_Staff_Login_Controller@view_2');
Route::get('staff_login_view_3/{session_id}','Admin\AD_Staff_Login_Controller@view_3');

/**
*STAFF PANEL
**/

Route::get('staff_dashboard','SD_Dashboard_Controller@index')->middleware("validstaff");

//Daily Task
Route::get('sd_daily_task_entry','SD_Daily_Task_Controller@entry')->middleware("validstaff");
Route::post('sd_daily_task_store','SD_Daily_Task_Controller@store')->middleware("validstaff");
Route::get('sd_daily_task_view','SD_Daily_Task_Controller@view')->middleware("validstaff");
Route::get('sd_daily_task_edit/{dy_code}','SD_Daily_Task_Controller@edit')->middleware("validstaff");
Route::post('sd_daily_task_update','SD_Daily_Task_Controller@update')->middleware("validstaff");
Route::get('sd_daily_task_change_status/{dy_code}','SD_Daily_Task_Controller@changeStatus')->middleware("validstaff");

//Daily Task Question
Route::get('sd_daily_question_entry','SD_Daily_Task_Question_Controller@entry')->middleware("validstaff");
Route::post('sd_daily_question_store','SD_Daily_Task_Question_Controller@store')->middleware("validstaff");
Route::get('sd_daily_question_view_1','SD_Daily_Task_Question_Controller@view_1')->middleware("validstaff");
Route::get('sd_daily_question_view_2','SD_Daily_Task_Question_Controller@view_2')->middleware("validstaff");
Route::get('sd_daily_question_view_3','SD_Daily_Task_Question_Controller@view_3')->middleware("validstaff");
Route::get('sd_daily_question_view_print','SD_Daily_Task_Question_Controller@view_print')->middleware("validstaff");
Route::get('sd_daily_question_edit/{dy_ques_id}','SD_Daily_Task_Question_Controller@edit')->middleware("validstaff");
Route::post('sd_daily_question_update','SD_Daily_Task_Question_Controller@update')->middleware("validstaff");

//Model Exam
Route::get('sd_model_exam_entry','SD_MEX_Controller@entry')->middleware("validstaff");
Route::post('sd_model_exam_store','SD_MEX_Controller@store')->middleware("validstaff");
Route::get('sd_model_exam_view','SD_MEX_Controller@view')->middleware("validstaff");

//Model Exam Question
Route::get('sd_model_question_entry','SD_MEX_Question_Controller@entry')->middleware("validstaff");
Route::post('sd_model_question_store','SD_MEX_Question_Controller@store')->middleware("validstaff");
Route::get('sd_model_question_view_1','SD_MEX_Question_Controller@view_1')->middleware("validstaff");
Route::post('sd_model_question_view_2','SD_MEX_Question_Controller@view_2')->middleware("validstaff");
Route::post('sd_model_question_view_print','SD_MEX_Question_Controller@view_print')->middleware("validstaff");
Route::get('sd_model_question_edit/{mq_ques_id}','SD_MEX_Question_Controller@edit')->middleware("validstaff");
Route::post('sd_model_question_update','SD_MEX_Question_Controller@update')->middleware("validstaff");
Route::get('sd_model_question_view_3/{mq_ques_id}','SD_MEX_Question_Controller@view_3')->middleware("validstaff");

Route::get('sd_model_question_entry_1','SD_MEX_Question_Controller@entry_1')->middleware("validstaff");
Route::get('sd_model_question_entry_2','SD_MEX_Question_Controller@entry_2')->middleware("validstaff");

//Material Model Entry
Route::get('sd_material_model_entry','SD_Material_Model_Controller@entry')->middleware("validstaff");
Route::post('sd_material_model_store','SD_Material_Model_Controller@store')->middleware("validstaff");
Route::get('sd_material_model_view','SD_Material_Model_Controller@view')->middleware("validstaff");

//Material Model Question Entry
Route::get('sd_material_model_question_entry_1','SD_Material_Question_Controller@entry_1')->middleware("validstaff");
Route::get('sd_material_model_question_entry_2','SD_Material_Question_Controller@entry_2')->middleware("validstaff");
Route::get('sd_material_model_question_entry','SD_Material_Question_Controller@entry')->middleware("validstaff");
Route::post('sd_material_model_question_store','SD_Material_Question_Controller@store')->middleware("validstaff");
Route::get('sd_material_model_question_view_1','SD_Material_Question_Controller@view_1')->middleware("validstaff");
Route::post('sd_material_model_question_view_2','SD_Material_Question_Controller@view_2')->middleware("validstaff");
Route::post('sd_material_model_question_view_print','SD_Material_Question_Controller@view_print')->middleware("validstaff");
Route::get('sd_material_model_question_edit/{mm_ques_id}','SD_Material_Question_Controller@edit')->middleware("validstaff");
Route::post('sd_material_model_question_update','SD_Material_Question_Controller@update')->middleware("validstaff");


//Online Exam
Route::get('sd_online_exam_entry','SD_Online_Controller@entry')->middleware("validstaff");
Route::post('sd_online_exam_store','SD_Online_Controller@store')->middleware("validstaff");
Route::get('sd_online_exam_view','SD_Online_Controller@view')->middleware("validstaff");
Route::get('sd_online_exam_edit/{on_code}','SD_Online_Controller@edit')->middleware("validstaff");
Route::post('sd_online_exam_update','SD_Online_Controller@update')->middleware("validstaff");
Route::get('sd_online_exam_status/{on_code}','SD_Online_Controller@changeStatus')->middleware("validstaff");

//Online Exam Question
Route::get('sd_online_question_entry','SD_Online_Question_Controller@entry')->middleware("validstaff");
Route::post('sd_online_question_store','SD_Online_Question_Controller@store')->middleware("validstaff");
Route::get('sd_online_question_view_1','SD_Online_Question_Controller@view_1')->middleware("validstaff");
Route::post('sd_online_question_view_2','SD_Online_Question_Controller@view_2')->middleware("validstaff");
Route::post('sd_online_question_view_print','SD_Online_Question_Controller@view_print')->middleware("validstaff");
Route::get('sd_online_question_edit/{on_ques_id}','SD_Online_Question_Controller@edit')->middleware("validstaff");
Route::post('sd_online_question_update','SD_Online_Question_Controller@update')->middleware("validstaff");
Route::get('sd_online_question_view_3/{on_ques_id}','SD_Online_Question_Controller@view_3')->middleware("validstaff");

Route::get('sd_online_question_entry_1','SD_Online_Question_Controller@entry_1')->middleware("validstaff");
Route::get('sd_online_question_entry_2','SD_Online_Question_Controller@entry_2')->middleware("validstaff");

//Material Language Entry
Route::get('sd_material_language_entry','SD_Material_Language_Controller@entry')->middleware("validstaff");
Route::post('sd_material_language_store','SD_Material_Language_Controller@store')->middleware("validstaff");
Route::get('sd_material_language_view','SD_Material_Language_Controller@view')->middleware("validstaff");
Route::get('sd_material_language_edit/{ls_code}','SD_Material_Language_Controller@edit')->middleware("validstaff");
Route::post('sd_material_language_update','SD_Material_Language_Controller@update')->middleware("validstaff");
Route::get('sd_material_language_change_status/{ls_code}','SD_Material_Language_Controller@changeStatus')->middleware("validstaff");

//Material Language Study Entry
Route::get('sd_material_language_study_entry','SD_Material_Language_Study_Controller@entry')->middleware("validstaff");
Route::post('sd_material_language_study_store','SD_Material_Language_Study_Controller@store')->middleware("validstaff");
Route::get('sd_material_language_study_view','SD_Material_Language_Study_Controller@view')->middleware("validstaff");
Route::get('sd_material_language_study_view_pdf','SD_Material_Language_Study_Controller@view_pdf')->middleware("validstaff");
Route::get('sd_material_language_study_view_pdf_sample','SD_Material_Language_Study_Controller@view_pdf_sample')->middleware("validstaff");

//Material Language Question Entry
Route::get('sd_material_language_question_entry_1','SD_Material_LanguageQ_Controller@entry_1')->middleware("validstaff");
Route::get('sd_material_language_question_entry_2','SD_Material_LanguageQ_Controller@entry_2')->middleware("validstaff");
Route::post('sd_material_language_question_store','SD_Material_LanguageQ_Controller@store')->middleware("validstaff");
Route::get('sd_material_language_question_view_1','SD_Material_LanguageQ_Controller@view_1')->middleware("validstaff");
Route::get('sd_material_language_question_view_2','SD_Material_LanguageQ_Controller@view_2')->middleware("validstaff");
Route::get('sd_material_language_question_view_print','SD_Material_LanguageQ_Controller@view_print')->middleware("validstaff");
Route::get('sd_material_language_question_edit/{ls_ques_id}','SD_Material_LanguageQ_Controller@edit')->middleware("validstaff");
Route::post('sd_material_language_question_update','SD_Material_LanguageQ_Controller@update')->middleware("validstaff");

//Material General Studies
Route::get('sd_material_general_upload_entry','SD_Material_General_Controller@entry')->middleware("validstaff");
Route::post('sd_material_general_upload_store','SD_Material_General_Controller@store')->middleware("validstaff");
Route::get('sd_material_general_upload_view','SD_Material_General_Controller@view')->middleware("validstaff");
Route::get('sd_material_general_upload_view_1','SD_Material_General_Controller@view_1')->middleware("validstaff");
Route::post('sd_material_general_upload_view_2','SD_Material_General_Controller@view_2')->middleware("validstaff");
Route::get('sd_material_general_view_pdf','SD_Material_General_Controller@view_pdf')->middleware("validstaff");
Route::get('sd_material_general_view_pdf_sample','SD_Material_General_Controller@view_pdf_sample')->middleware("validstaff");

//Material General Studies Questions
Route::get('sd_material_general_question_entry_1','SD_Material_GeneralQ_Controller@entry_1')->middleware("validstaff");
Route::get('sd_material_general_question_entry_2','SD_Material_GeneralQ_Controller@entry_2')->middleware("validstaff");
Route::post('sd_material_general_question_store','SD_Material_GeneralQ_Controller@store')->middleware("validstaff");

Route::get('sd_material_general_question_edit/{gs_code}','SD_Material_GeneralQ_Controller@edit')->middleware("validstaff");
Route::post('sd_material_general_question_update','SD_Material_GeneralQ_Controller@update')->middleware("validstaff");

//Material General Studies
Route::get('sd_material_general_question_view_1','SD_Material_GeneralQ_Controller@view_1')->middleware("validstaff");
Route::post('sd_material_general_question_view_2','SD_Material_GeneralQ_Controller@view_2')->middleware("validstaff");
Route::post('sd_material_general_question_view_print','SD_Material_GeneralQ_Controller@view_print')->middleware("validstaff");
Route::get('sd_material_general_question_view_3','SD_Material_GeneralQ_Controller@view_3')->middleware("validstaff");

//Free Daily Task
Route::get('sd_free_daily_task_entry','SD_Free_Task_Controller@entry')->middleware("validstaff");
Route::post('sd_free_daily_task_store','SD_Free_Task_Controller@store')->middleware("validstaff");
Route::get('sd_free_daily_task_view','SD_Free_Task_Controller@view')->middleware("validstaff");
Route::get('sd_free_daily_task_edit/{dy_code}','SD_Free_Task_Controller@edit')->middleware("validstaff");
Route::post('sd_free_daily_task_update','SD_Free_Task_Controller@update')->middleware("validstaff");
Route::get('sd_free_daily_task_change_status/{dy_code}','SD_Free_Task_Controller@changeStatus')->middleware("validstaff");

//Free Daily Task Question
Route::get('sd_free_daily_question_entry','SD_Free_Task_Question_Controller@entry')->middleware("validstaff");
Route::post('sd_free_daily_question_store','SD_Free_Task_Question_Controller@store')->middleware("validstaff");
Route::get('sd_free_daily_question_view_1','SD_Free_Task_Question_Controller@view_1')->middleware("validstaff");
Route::get('sd_free_daily_question_view_2','SD_Free_Task_Question_Controller@view_2')->middleware("validstaff");
Route::get('sd_free_daily_question_view_3','SD_Free_Task_Question_Controller@view_3')->middleware("validstaff");
Route::get('sd_free_daily_question_view_print','SD_Free_Task_Question_Controller@view_print')->middleware("validstaff");
Route::get('sd_free_daily_question_edit/{dy_ques_id}','SD_Free_Task_Question_Controller@edit')->middleware("validstaff");
Route::post('sd_free_daily_question_update','SD_Free_Task_Question_Controller@update')->middleware("validstaff");

//Jobnews
Route::get('sd_jobnews_entry','SD_Jobnews_Controller@entry')->middleware("validstaff");
Route::post('sd_jobnews_store','SD_Jobnews_Controller@store')->middleware("validstaff");
Route::get('sd_jobnews_view','SD_Jobnews_Controller@view')->middleware("validstaff");
Route::get('sd_jobnews_edit/{job_code}','SD_Jobnews_Controller@edit')->middleware("validstaff");
Route::post('sd_jobnews_update','SD_Jobnews_Controller@update')->middleware("validstaff");
Route::get('sd_jobnews_status/{job_code}','SD_Jobnews_Controller@changeStatus')->middleware("validstaff");

//Jobnews
Route::get('sd_announcement_entry','SD_Announcement_Controller@entry')->middleware("validstaff");
Route::post('sd_announcement_store','SD_Announcement_Controller@store')->middleware("validstaff");
Route::get('sd_announcement_view','SD_Announcement_Controller@view')->middleware("validstaff");
Route::get('sd_announcement_edit/{ann_code}','SD_Announcement_Controller@edit')->middleware("validstaff");
Route::post('sd_announcement_update','SD_Announcement_Controller@update')->middleware("validstaff");
Route::get('sd_announcement_status/{ann_code}','SD_Announcement_Controller@changeStatus')->middleware("validstaff");

//Push Notification
Route::get('sd_notify_entry','SD_Notification_Controller@entry')->middleware("validstaff");
Route::post('sd_notify_store','SD_Notification_Controller@store')->middleware("validstaff");

Route::get('sd_notify_view_1','SD_Notification_Controller@view_1')->middleware("validstaff");
Route::get('sd_notify_view_2','SD_Notification_Controller@view_2')->middleware("validstaff");

//Password Change
Route::get('sd_password_entry','SD_Password_Controller@entry')->middleware("validstaff");
Route::post('sd_password_update','SD_Password_Controller@update')->middleware("validstaff");

Route::get('send_message','API_Registration_New_Controller@sendWelcome');
Route::get('test_message','API_Forget_MpinController@sendOTP');
// Route::get('paymentDetails','ANDR_NewPayment_Controller@paymentDetails');
Route::get('paymentDetails','ANDR_NewPayment_Controller@getPlanDetailList');
Route::post('mobile-payment-checkout','ANDR_NewPayment_Controller@paymentCheckout');
Route::get('mobile-payment-update','ANDR_NewPayment_Controller@updatePayment');
Route::get('mobile-payment-user-details','ANDR_NewPayment_Controller@paymentUserDetails');
Route::post('mobile-payment-user-update','ANDR_NewPayment_Controller@updateUserDetails');


//HYBRID ANDROID UI
//Material
Route::get('an_material','ANDR_Material_Controller@page_1');
Route::get('an_material_practise','ANDR_Material_Controller@page_2');
Route::get('an_payment','ANDR_Payment_Controller@pay_1');
Route::post('an_payment_store','ANDR_Payment_Controller@pay_2');
Route::get('an_payment_success','ANDR_Payment_Controller@pay_3');
Route::get('an_payment_mpin','ANDR_Payment_Controller@pay_4');
Route::post('an_payment_mpin_update','ANDR_Payment_Controller@mpin_update');

Route::get('an_payment_new','ANDR_Payment_Controller@new_pay_1');
Route::get('an_payment_success_new','ANDR_Payment_Controller@new_pay_2');
Route::get('an_payment_details','ANDR_Payment_Controller@new_pay_3');
Route::post('an_payment_store_new','ANDR_Payment_Controller@new_pay_4');
Route::get('an_model_exam','ANDR_Model_Exam_Controller@exam_1');
Route::get('an_exam_post','ANDR_Model_Exam_Controller@storeDetail');

//Federal Bank Test
Route::get('federal-form-view','FederalBankController@viewForm');
Route::post('federal-bank-submit','FederalBankController@submitForm');
Route::post('federal-bank-response','FederalBankController@paymentResponse');

//Guzzle Api
Route::get('test-guzzle','Test_Controller@sampleGuzzle');

//WEB UI
//USER REGISTRATION
Route::get('register','WEB_Signup_Controller@pay_1');
Route::post('register_store','WEB_Signup_Controller@pay_2');
Route::get('user_payment/{user_code}','WEB_Signup_Controller@pay_3');
Route::get('payment_success','WEB_Signup_Controller@pay_3');
Route::get('payment_mpin','WEB_Signup_Controller@pay_4');

//HOME PAGE
Route::get('home','Home_Controller@index');

//Firebase
Route::get('firebase','Admin\AD_Test_Controller@index');
Route::get('test','Admin\AD_Test_Controller@setText');


//Payment
Route::get('r_oder','Admin\AD_Test_Controller@order_creation');
Route::get('r_oder_capture','Admin\AD_Test_Controller@order_capture');
Route::get('new_payment','ANDR_Payment_Controller@order_creation');

//Route::get('an_new_payment_1','ANDR_Payment_New_Controller@pay_1');
//Route::get('an_new_payment_11','ANDR_Payment_New_Controller@pay_11');
//Route::post('an_new_payment_2','ANDR_Payment_New_Controller@pay_2');
//Route::get('an_new_payment_3','ANDR_Payment_New_Controller@pay_3');
//Route::get('an_new_payment_4','ANDR_Payment_New_Controller@pay_4');
//Route::post('an_new_payment_5','ANDR_Payment_New_Controller@pay_5');

Route::get('paymentDetails1','ANDR_MyPayment_Controller@paymentDetails');
    Route::get('paymentcheckoutform','ANDR_MyPayment_Controller@paymentcheckoutform');
    Route::post('paymentinsert','ANDR_MyPayment_Controller@paymentinsert');

Route::group(['middleware' => 'prevent-back-history'],function(){
  	Route::get('an_new_payment_1','ANDR_Payment_New_Controller@pay_1');
	Route::get('an_new_payment_11','ANDR_Payment_New_Controller@pay_11');
	Route::post('an_new_payment_2','ANDR_Payment_New_Controller@pay_2');
	Route::get('an_new_payment_3','ANDR_Payment_New_Controller@pay_3');
	Route::get('{planCode}/an_new_payment_4','ANDR_Payment_New_Controller@pay_4');
	Route::post('an_new_payment_5','ANDR_Payment_New_Controller@pay_5');
});

Route::get('sendSMS/{phone_number}', 'SMSController@sendSMS');


//WEBSITE

Route::get('clear-cache', function() {
    $exitCode = Artisan::call('cache:clear');
    session()->flush();
  });

  Route::get('clear-cache2', function() {
    $exitCode = Artisan::call('cache:clear');
  });

// Route::get('/','Home\HomeController@index');

Route::group(['middleware' => 'WebPreventBackHistory'],function() 
{
    Auth::routes();
	// ->middleware("preventafterlogin")
    Route::get('/home', 'HomeController@index');
    Route::get('vidhvaaindex','Home\HomeController@index');
    Route::get('vidhvaaabout','About\AboutController@about');
    Route::get('dailytask','Services\DailytaskController@dailytask');
    Route::get('onlinemodelexamfront','Services\OnlinemodelexamController@onlinemodelexamfront');
    Route::get('scheduletest','Services\ScheduletestController@scheduletest');
    Route::get('currentaffairs','Services\CurrentaffairsController@currentaffairs');
    Route::get('upcoming','Home\HomeController@upcoming');
    Route::get('sendLinkViaSMS','Home\HomeController@sendLinkViaSMS');

    Route::get('vidhvaaloginpage','Login\LoginController@vidhvaaloginpage')->middleware("alreadylogin");
    Route::get('loginnextForm','Login\LoginController@loginnextForm')->middleware("alreadylogin");
    Route::get('logindetailsget','Login\LoginController@logindetailsget')->middleware("alreadylogin");
    Route::get('loginmaildetailscheck','Login\LoginController@loginmaildetailscheck')->middleware("alreadylogin");

   //Route::get('loginmaildetailscheck','Login\UserloginController@loginDetailsCheck')->middleware("alreadylogin");
   //Route::get('vidhvaaloginpage','Login\UserloginController@index')->middleware("alreadylogin");

    Route::get('vidhvaavideos','Home\HomeController@vidhvaavideos');
    Route::get('motivationVideoApi','API_MotVideo_Controller@index');

    /* New Login Functions */
	
	Route::get('checkLoginFormDetails','Login\UserloginController@checkLoginFormDetails');

    Route::get('userLogin','Login\UserloginController@index')->middleware("alreadylogin");
    Route::get('userDetailsCheck','Login\UserloginController@loginDetailsCheck')->middleware("alreadylogin");
    Route::get('userSignup','Login\UsersignupController@index')->middleware("alreadylogin");
    Route::get('userSignUpCheck','Login\UsersignupController@userSignUp')->middleware("alreadylogin");
    Route::get('userSignUpDetails','Login\UsersignupController@signUpDetailsInsert')->middleware("alreadylogin");

    Route::get('userLoginDetails','Login\UserloginController@userLoginDetails')->middleware("alreadylogin");
    Route::get('userLoginAPI','Login\UserloginController@userLogin')->middleware("alreadylogin");
    Route::get('userLoginOTPAPI','Login\UserloginController@userLoginOTPAPI')->middleware("alreadylogin");
    Route::get('userLoginOTPSendAPI','Login\UserloginController@userLoginOTPSendAPI')->middleware("alreadylogin");
    Route::get('userLoginStateAPI','Login\UserloginController@userLoginStateAPI')->middleware("alreadylogin");
    Route::get('userState','Login\UserloginController@userState');
    Route::get('userStateAPI','Login\UserloginController@userStateAPI');

    Route::get('paymentAuthDetails','Services\PaymentController@paymentAuthDetails');
    Route::get('paymentUserDetails','Services\PaymentController@paymentUserDetails');

});

  

Route::group(['middleware' => 'WebPreventBackHistory2'],function() {

/* Login pages routes */

/* after signup page rotes */
Route::get('vidhvaaupsc','Course\UpscController@vidhvaaupsc')->middleware("validuser");
Route::get('vidhvaassc','Course\SscController@vidhvaassc')->middleware("validuser");
Route::get('vidhvaabanking','Course\BankingController@vidhvaabanking')->middleware("validuser");
Route::get('vidhvaarrb','Course\RrbController@vidhvaarrb')->middleware("validuser");
Route::get('vidhvaatnpsc','Course\TnpscController@vidhvaatnpsc')->middleware("validuser");
Route::get('studiesmetirials','Course\TnpscController@studiesmetirials')->middleware("validuser");
Route::get('studiesmetirialview','Course\TnpscController@studiesmetirialview')->middleware("validuser");
Route::get('bookbackpdfview','Course\TnpscController@bookbackpdfview')->middleware("paiduser");
Route::get('userlogout','Home\HomeController@destroysessionvar')->middleware("validuser");
Route::get('languagemetirialview','Course\TnpscController@languagemetirialviewpdf')->middleware("validuser");
Route::get('previousyearquestionview','Course\TnpscController@previousyearquestionview')->middleware("paiduser");
Route::get('getUPSCsyllabus','Course\UpscController@getUPSCPdf')->middleware("validuser");
Route::get('getSSCsyllabus','Course\SscController@getSSCPdf')->middleware("validuser");
Route::get('getBANKINGsyllabus','Course\BankingController@getBANKINGPdf')->middleware("validuser");
Route::get('getRRBsyllabus','Course\RrbController@getRRBPdf')->middleware("validuser");
Route::get('vidhvaatnpsctamil','Course\TnpscController@vidhvaatnpsctamil')->middleware("validuser");
Route::get('currentaffairsview','Services\CurrentaffairsController@currentaffairsview')->middleware("validuser");
Route::get('getVidhvaaCurrentAffairs','Course\TnpscController@getVidhvaaCurrentAffairs')->middleware("paiduser");

Route::get('profile','Services\ProfileController@index')->middleware("validuser");

/* payment */

/*
Route::get('vidhvaalogin2','Services\PaymentController@paymentinsertform', function() {
    $exitCode = Artisan::call('cache:clear');
});  */
Route::post('paymentinsertbefore','Services\PaymentController@paymentinsertbefore')->middleware("validuser");
Route::get('paymentafterform','Services\PaymentController@paymentafterform')->middleware("validuser");
Route::post('paymentdatainsert','Services\PaymentController@paymentdatainsert')->middleware("validuser");
Route::post('subscribeforminsert','Services\PaymentController@subscribeforminsert')->middleware("validuser");
Route::post('uploadUserProfilePic','Services\ProfileController@uploadUserProfilePic');
Route::get('smssend','Login\LoginController@smssend');


/* after payment page rotes */

//middleware("paiduser")

Route::get('dailytaskinstruction','Services\DailytaskController@dailytaskinstruction')->middleware("paiduser");
Route::get('dailytaskinstructiontime','Services\DailytaskController@dailytaskinstructiontime')->middleware("paiduser");
Route::get('onlinedailytask','Services\DailytaskController@onlinedailytask')->middleware("paiduser");
Route::get('dailytaskdetailsselect','Services\DailytaskController@dailytaskdetailsselect')->middleware("paiduser");
Route::post('dailytaskdetailsinsert','Services\DailytaskController@dailytaskdetailsinsert')->middleware("paiduser");
Route::get('onlinedailytaskresult','Services\DailytaskController@onlinedailytaskresult')->middleware("paiduser");
Route::get('onlineweeklydailytask','Services\DailytaskController@onlineweeklydailytask')->middleware("paiduser");
Route::get('weeklydailytaskinstruction','Services\DailytaskController@weeklydailytaskinstruction')->middleware("paiduser");
Route::get('weeklydailytaskdetailsselect','Services\DailytaskController@weeklydailytaskdetailsselect')->middleware("paiduser");
Route::get('weeklydailytaskdetailsinsert','Services\DailytaskController@weeklydailytaskdetailsinsert')->middleware("paiduser");
Route::get('onlineweeklydailytaskresult','Services\DailytaskController@onlineweeklydailytaskresult')->middleware("paiduser");

Route::get('onlinemodelexaminstruction','Services\OnlinemodelexamController@onlinemodelexaminstruction')->middleware("paiduser");
Route::get('onlinemodelinstructiontime','Services\OnlinemodelexamController@onlinemodelinstructiontime')->middleware("paiduser");
Route::get('onlinemodelexam','Services\OnlinemodelexamController@onlinemodelexam')->middleware("paiduser");
Route::post('modelexamdetailsinsert','Services\OnlinemodelexamController@modelexamdetailsinsert')->middleware("paiduser");
Route::get('modelexamdetailsselect','Services\OnlinemodelexamController@modelexamdetailsselect')->middleware("paiduser");
Route::get('modelexamresult','Services\OnlinemodelexamController@modelexamresult')->middleware("paiduser");
Route::get('dailytaskanswerscreen','Services\DailytaskController@dailytaskanswerscreen')->middleware("paiduser");
Route::get('weeklytaskanswerscreen','Services\DailytaskController@weeklytaskanswerscreen')->middleware("paiduser");
Route::get('modelexamanswerscreen','Services\OnlinemodelexamController@modelexamanswerscreen')->middleware("paiduser");
Route::get('modelquestionsenglish','Course\TnpscController@modelquestionsenglish')->middleware("paiduser");
Route::get('materialmodelresults','Course\TnpscController@materialmodelresults')->middleware("paiduser");
Route::get('materialmodelexamresults','Course\TnpscController@materialmodelexamresults')->middleware("paiduser");

Route::get('generalstudiesquestion','Course\TnpscController@generalstudiesquestion')->middleware("paiduser");
Route::get('generalstudiesquestionresult','Course\TnpscController@generalstudiesquestionresult')->middleware("paiduser");

Route::get('changeMpin','Services\ProfileController@changeMpinDetail')->middleware("paiduser");
Route::get('changeNewMpin','Services\ProfileController@changeNewMpin')->middleware("paiduser");
Route::get('forgorMpin','Services\ProfileController@forgorMpinDetail')->middleware("paiduser");
Route::get('changeUserDetails','Services\ProfileController@changeUserDetails')->middleware("paiduser");
Route::post('checkMPIN','Services\ProfileController@checkMPIN');
Route::get('languagemetirial','Course\TnpscController@languagemetirial')->middleware("validuser");
Route::get('onlinemodelexamtimer','Services\OnlinemodelexamController@onlinemodelexamtimer')->middleware("validuser");

Route::get('modelexamRegisterNumber','Services\OnlinemodelexamController@modelexamRegisterNumber')->middleware("paiduser");
Route::get('modelexamRegistrationNumberDetails','Services\OnlinemodelexamController@modelexamRegistrationNumberDetails')->middleware("paiduser");
Route::get('modelexamchooselanguage','Services\OnlinemodelexamController@modelexamchooselanguage')->middleware("paiduser");


Route::get('examschedule','Services\ExamscheduleController@examschedule');
Route::post('packageproduct','Services\ExamscheduleController@packageproduct');
Route::get('packageproductremove','Services\ExamscheduleController@packageproductremove');
Route::post('examscheduleInsert','Services\ExamscheduleController@examscheduleInsert');
Route::get('examscheduleRemove','Services\ExamscheduleController@examscheduleRemove');
Route::get('leaderboard','Services\ProfileController@leaderboard');
Route::get('leaderboardscore','Services\ProfileController@leaderboardscore');

Route::get('dailytaskRegisterNumber','Services\DailytaskController@dailytaskRegisterNumber')->middleware("paiduser");
Route::get('dailytaskRegistrationNumberDetails','Services\DailytaskController@dailytaskRegistrationNumberDetails')->middleware("paiduser");
Route::get('dailytaskchooselanguage','Services\DailytaskController@dailytaskchooselanguage')->middleware("paiduser");
Route::get('onlinedailytaskexamtimer','Services\DailytaskController@onlinedailytaskexamtimer')->middleware("validuser");


Route::get('onlineliveexam','Services\OnlineliveexamController@onlineliveexam');
Route::get('exammenginstruction','Services\OnlineliveexamController@exammenginstruction')->middleware("paiduser");
Route::get('onlineliveexaminstructiontime','Services\OnlineliveexamController@onlineliveexaminstructiontime')->middleware("paiduser");
Route::get('onlineliveexamtask','Services\OnlineliveexamController@onlineliveexamtask')->middleware("paiduser");
Route::post('onlinelivedetailsinsert','Services\OnlineliveexamController@onlinelivedetailsinsert')->middleware("paiduser");
Route::get('onlineliveexamresult','Services\OnlineliveexamController@onlineliveexamresult')->middleware("paiduser");
Route::get('onlineliveexamselect','Services\OnlineliveexamController@onlineliveexamselect')->middleware("paiduser");
Route::get('onlineliveexamtimer','Services\OnlineliveexamController@onlineliveexamtimer')->middleware("paiduser");
Route::get('liveexamanswerscreen','Services\OnlineliveexamController@liveexamanswerscreen')->middleware("paiduser");
Route::get('liveexamRegisterNumber','Services\OnlineliveexamController@liveexamRegisterNumber')->middleware("paiduser");
Route::get('liveexamRegistrationNumberDetails','Services\OnlineliveexamController@liveexamRegistrationNumberDetails')->middleware("paiduser");
Route::get('onlineexamchooselanguage','Services\OnlineliveexamController@onlineexamchooselanguage')->middleware("paiduser");
//Route::get('onlineliveexaminstructiontime','Services\OnlineliveexamController@onlineliveexaminstructiontime')->middleware("paiduser");

Route::get('weeklydailytaskRegisterNumber','Services\DailytaskController@weeklydailytaskRegisterNumber')->middleware("paiduser");
Route::get('weeklydailytaskRegistrationNumberDetails','Services\DailytaskController@weeklydailytaskRegistrationNumberDetails')->middleware("paiduser");
Route::get('weeklydailytaskchooselanguage','Services\DailytaskController@weeklydailytaskchooselanguage')->middleware("paiduser");
Route::get('weeklydailytaskinstruction2','Services\DailytaskController@weeklydailytaskinstruction2')->middleware("paiduser"); 
Route::get('weeklydailytaskinstructiontime','Services\DailytaskController@weeklydailytaskinstructiontime')->middleware("paiduser");
Route::get('freetaskinstructiontime2','Services\DailytaskController@freetaskinstructiontime2')->middleware("paiduser");
Route::get('getGeneralStudiesAudio','Course\TnpscController@getGeneralStudiesAudio')->middleware("paiduser");

Route::get('getGeneralStudiesAudioStream','Course\TnpscController@getGeneralStudiesAudioStream')->middleware("paiduser");

Route::get('vidhvaalogin2','Services\PaymentController@paymentform', function() {
    $exitCode = Artisan::call('cache:clear');
});

Route::get('paymentPlanSelect','Services\PaymentController@paymentPlanSelect')->middleware("validuser");
Route::get('paymentPlanSelectCheckout','Services\PaymentController@paymentPlanSelectCheckout')->middleware("validuser");
Route::get('paymentPlanSelectCheckoutSuccess','Services\PaymentController@paymentPlanSelectCheckoutSuccess')->middleware("validuser");

Route::get('paymentDetailsForm','Services\PaymentController@paymentDetailsForm')->middleware("validuser");
Route::post('paymentDetailsFormPayment','Services\PaymentController@paymentDetailsFormPayment')->middleware("validuser");
Route::post('paymentDetailsFormSuccess','Services\PaymentController@paymentDetailsFormSuccess')->middleware("validuser");

Route::get('paymentcheckout','Services\PaymentController@paymentcheckoutform',['middleware' => 'auth']);
Route::get('languagemetirialviewaudioview','Course\TnpscController@languagemetirialviewaudioview',['middleware' => 'auth']);

});

Route::get('generalStudiesPdf','Course\TnpscController@getGeneralStudiesPdf')->middleware("validuser");
Route::get('getTNPSCsyllabus','Course\TnpscController@getTNPSCPdf')->middleware("validuser");
Route::get('languagemetirialviewpdfview','Course\TnpscController@languagemetirialviewpdfview')->middleware("validuser");
Route::get('getVidhvaaCurrentAffairsPdf','Course\TnpscController@getVidhvaaCurrentAffairsPdf')->middleware("paiduser");
Route::get('getBookBack','Course\TnpscController@getBookBackPdf')->middleware("validuser");
Route::get('previousyearquestionviewpdf','Course\TnpscController@previousyearquestionviewpdf')->middleware("paiduser");
Route::get('getReferenceBookes','Course\TnpscController@getReferenceBookes')->middleware("validuser");
Route::get('privacy','Home\HomeController@privacy');
Route::get('termsAndConditions','Home\HomeController@termsAndConditions');

Route::get('faq', function () {
    return view('designs/home/faq');
});

//Route::get('sendotpsms','Login\LoginController@sendotpsms');


Route::get('/db-test', function() {
   if(DB::connection()->getDatabaseName())
   {
      echo "conncted sucessfully to database ".DB::connection()->getDatabaseName();
   }
});


//vaishnu devi 
//plan detail list
// Route::get('plan/{code}/details','ANDR_NewPayment_Controller@getPlanDetailList');




//jaisumalatha
Route::get('getcheck','Guzzleapi\GuzzleUserloginController@getRequest');