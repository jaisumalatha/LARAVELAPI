<?php

namespace App\Http\Controllers\Guzzleapi;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;

use App\Models\UserDetail;
use App\Models\UserAuthenticationDetail;
use App\Models\UserPersonalDetail;
use App\Models\UserPaymentDetail;
use App\Models\UserPlanDetail;
use App\Libraries\SendPushNotification;

use App\Libraries\SendSms;

class RegistrationGuzzleController extends Controller
{
    public function user_entryguzzleapi(Request $request)
{
    $client = new \GuzzleHttp\Client();
    $url = "https://www.vidhvaa.in/user-registration";

    $user_email   = trim($request->userEmail);
    $user_mobile    = trim($request->userMobile);
    $device_id     = trim($request->deviceId);
    $otp_code      = trim($request->otpCode);
   
   
    $response = $client->request('POST', 'https://www.vidhvaa.in/user-registration', [
        'form_params' => [
            'userEmail' => $user_email , 'userMobile' => $user_mobile , 'deviceId' => $device_id , 'otpCode' => $otp_code
        ]
    ]);

    $response = $response->getBody()->getContents();
        echo '<pre>';
        print_r($response);
    
   
}

    public function users_entryguzzleapi(Request $request)
    {
        $result         = ['Status' => 400, 'message' => 'No process is done'];
        
        $validator = Validator::make($request->all(), [
            'userMobile' => 'required|regex:/^[6789]\d{9}$/|integer|digits:10',
            'userEmail' => 'required|email:rfc,dns'
        ]);

        if($validator->fails()){  
            $errors = $validator->errors();
            $message = '';
            foreach ($errors->all() as $error) {
               $message .= $error . ' ';
            }       
            $result         = ['Status' => 400, 'message' => $message];
            return response()->json($result, 200);     
        }

        $user_email     = trim($request->userEmail);
        $user_mobile    = trim($request->userMobile);
        $device_id      = trim($request->deviceId);
        $otp_code       = trim($request->otpCode);
            
        $date_time      = date("Y-m-d H:i:s");
        $user_code      = "UR". rand(10000,99999);
        //$firebase_token = trim($request->firebase_token);
        $firebase_token = "-";
        $level          = 0;

        $auth_token     = uniqid(base64_encode(Str::random(30)));
        $user_otp       = rand(1000,9999);
        //$check_user_deatils = UserDetail::orWhere('user_mobile', $user_mobile)->orWhere('user_email', $user_email)->first();
        $user_data    = [];
        $user_data[0] = $user_mobile;
        $user_data[1] = $user_email;

        $check_user_deatils = UserDetail::where('user_status','1')->where('user_mobile', $user_mobile)->first();

        if(!empty($check_user_deatils))
        {
            $check = UserDetail::where('user_mobile', $user_mobile)->where('user_email', $user_email)->first();
            if(!empty($check))
            {
                //Already Registered User
                $user_code = $check->user_code;
                $user_type = $check->user_type;
                $user_mpin = $check->user_mpin;

                $d2 = UserAuthenticationDetail::where('user_code', $user_code)->first();

                if(!empty($d2))
                {
                    UserDetail::where('user_code', $user_code)->update(['auth_token' => $auth_token, 'updated_at' => $date_time]);
                    UserAuthenticationDetail::where('user_code', $user_code)->update(['user_otp' => $user_otp, 'auth_token' => $auth_token, 'user_status' => "0", 'updated_at' => $date_time]);
                    // SendSms::sendOtpSMS($user_otp, $user_mobile);
                    SendSms::sendTestOtpSMS($user_otp, $user_mobile);
                }
                else {
                    UserDetail::where('user_code', $user_code)->update(['auth_token' => $auth_token, 'updated_at' => $date_time]);
                    UserAuthenticationDetail::insert(['user_code' => $user_code, 'auth_token' => $auth_token, 'user_otp' => $user_otp, 'user_status' => "0", 'created_at' => $date_time, 'updated_at' => $date_time]);
                    // SendSms::sendOtpSMS($user_otp, $user_mobile);
                    SendSms::sendTestOtpSMS($user_otp, $user_mobile);
                }
                //$user = UserDetail::create($user_code);
                // $token =  $check->createToken('MyApp')->accessToken;

                $result = ['Status' => true, 'AuthToken' => $auth_token, 'message' => "Already Existing User entry.."];
            }
            else
            {
                $result = ['Status' => false, 'Message' => "Error! Wrong Email Id..."];
            }
        }
        else
        {
        	$check_user_deatils1 = UserDetail::where('user_status','0')->where(function ($query) use($user_data) {
																            $query->where('user_mobile', $user_data[0])
																                  ->orWhere('user_email', $user_data[1]);
																        })->first();
        	if(empty($check_user_deatils1))
        	{

        		 //Fresh Registration
	            $v = UserDetail::insert([ 'user_code' => $user_code,  'user_name' => 'Guest',  'user_email' => $user_email, 'user_mobile' => $user_mobile, 'user_type' => "GUEST", 'user_mpin' => '-', 'user_reg_mode' => 'MOBILE', 'auth_token' => $auth_token, 'user_status' => "0", 'created_at' => $date_time, 'updated_at' => $date_time, 'user_state' => "-", 'device_id' => $device_id]);

	            if(!empty($v))
	            {
	                UserAuthenticationDetail::insert(['user_code' => $user_code, 'auth_token' => $auth_token, 'user_otp' => $user_otp, 'user_status' => "0", 'created_at' => $date_time, 'updated_at' => $date_time]);
	                
	                // SendSms::sendOtpSMS($user_otp, $user_mobile);
                    SendSms::sendTestOtpSMS($user_otp, $user_mobile);

	                // $token =  $check->createToken('MyApp')->accessToken;

	                // $result = ['Status' => 200, 'user_level' => '1', 'user_type' => 'GUEST', 'user_mpin' => '-', 'auth_token' => $auth_token, 'user_code' => $user_code,  'message' => "Entry is succesfull"];
	                $result = ['Status' => true, 'AuthToken' => $auth_token, 'Message' => "OTP Succesfully Send To You Mobile Number..."];
	            }
	            else
	                $result = ['Status' => false, 'Message' => "Please Try Agin...."];
	        }
	        else
	        {
	        	//Fresh registration but already otp pending
	        	$user_code = $check_user_deatils1->user_code;

	        	UserDetail::where('user_code', $user_code)->update(['user_mobile' => $user_mobile, 'user_email' => $user_email, 'auth_token' => $auth_token, 'updated_at' => $date_time]);
                UserAuthenticationDetail::where('user_code', $user_code)->update(['user_otp' => $user_otp, 'auth_token' => $auth_token, 'user_status' => "0", 'updated_at' => $date_time]);
                // SendSms::sendOtpSMS($user_otp, $user_mobile);
                SendSms::sendTestOtpSMS($user_otp, $user_mobile);

                $result = ['Status' => true, 'AuthToken' => $auth_token, 'Message' => "OTP Succesfully Send To You Mobile Number..."];
	        }

                       
        }
        return response()->json($result, 200);
    }

    public function user_otp(Request $request)
    {
        $date_time      = date("Y-m-d H:i:s");
        $result         = ['Status' => 400, 'message' => 'No process is done'];

        if(empty($request->bearerToken()))
        {
            $result = ['Status' => 420, 'message' => "Invalid Token.."];
            return response()->json($result, 200);
        }

        $validator = Validator::make($request->all(), [
            //'firebase_token' => 'required',
            'userOtp'       => 'required'
        ]);

        if($validator->fails()){  
            $errors = $validator->errors();
            $message = '';
            foreach ($errors->all() as $error) {
               $message .= $error . ' ';
            }       
            $result         = ['Status' => 400, 'message' => $message];
            return response()->json($result, 200);     
        }

        // $user_id        = trim($request->user()->token()->user_id);
        $auth_token     = $request->bearerToken();
        $firebase_token = trim($request->firebaseToken);
        $user_otp       = trim($request->userOtp);
        $level          = 0;
        
        $d1 = UserAuthenticationDetail::where('auth_token', $auth_token)->first();

        if(empty($d1))
        {
            $result = ['Status' => false, 'Message' => "Invalid Token.."];
            return response()->json($result, 200);
        }
        $system_otp = $d1->user_otp;
        if($user_otp != $system_otp)
        {
            $result = ['Status' => false, 'Message' => "Invalid OTP.."];
            return response()->json($result, 200);
        }

        $user_code  = $d1->user_code;
                
        $d2 = UserDetail::where('user_code', $user_code)->first();

        if(empty($d2))
        {
            $result = ['Status' => false, 'Message' => "Invalid Token.."];
            return response()->json($result, 200);
        }

        if(!empty($d2->login_status) &&  $d2->login_status == '1') 
        {
            if(!empty($d2->firebase_token))
            {
                $sendNotification = new SendPushNotification;
                $resp = $sendNotification->sendNotification('', 'Logout', $d2->firebase_token);                
            }
        }


        $user_type = "-";
        $user_Name = "-";
        $user_mpin = "-";
        $user_state = false;
        
        $user_name = $d2->user_name;            
        if ($d2->user_type == 'PAID') {
            $level  = 2;
            $user_type = 'PAID';
            $user_mpin = $d2->user_mpin;
        }
        else
        {
            $user_type = 'GUEST';
            $level  = 1;
            $user_mpin = "-";
        }
        if ($d2->user_state != '-' && $d2->user_state != "") {
            $user_state = true;
        }
        $new_auth_token     = uniqid(base64_encode(Str::random(30)));

        UserAuthenticationDetail::where('user_code', $user_code)->update(['user_status' => "1", 'auth_token' => $new_auth_token, 'updated_at' => $date_time, 'user_otp' => '']);

        $data['user_status'] = "1";
        $data['auth_token'] = $new_auth_token;
        if(!empty($firebase_token))
        {
            $data['firebase_token'] = $firebase_token;
        }
        $data['updated_at'] = $date_time;
        $data['login_status'] = "1";

        UserDetail::where('user_code', $user_code)->update($data);

        /*$data = array(
            'user_name'  => $user_name,
            'user_level' => $level, 
            'user_type'  => $user_type, 
            'user_mpin'  => $user_mpin,            
            'user_code'  => $user_code, 
            'auth_token' => $new_auth_token);*/

        $filePath = url('web_images/common.png');
        if(@get_headers(url('uploadkyc').'/'.$user_code.'_KYC.jpg')[0] != 'HTTP/1.1 404 Not Found')
        {
            $filePath = url('uploadkyc').'/'.$user_code . '_KYC.jpg';
        }
        else if(@get_headers(url('uploadkyc').'/'.$user_code.'_KYC.jpeg')[0] != 'HTTP/1.1 404 Not Found')
        {
            $filePath = url('uploadkyc').'/'.$user_code . '_KYC.jpeg';
        }
        else if(@get_headers(url('uploadkyc').'/'.$user_code.'_KYC.png')[0] != 'HTTP/1.1 404 Not Found') 
        {
            $filePath = url('uploadkyc').'/'.$user_code . '_KYC.png';
        }
    

        $result = ['Status' => true, 'Message' => "OTP is successfully verified...", 'UserName'  => $user_name, 'UserLevel' => $level, 'UserType'  => $user_type, 'UserMpin'  => $user_mpin, 'UserCode'  => $user_code, 'AuthToken' => $new_auth_token, 'IsUserStateAvailable' =>$user_state, 'UserEmail' => $d2->user_email, 'UserMobile' => $d2->user_mobile, 'profileImage' => $filePath];

        return response()->json($result, 200);
    }

    public function resend_otp(Request $request)
    {
    	$date_time = date("Y-m-d H:i:s");
		$result = ['Status' => 400, 'message' => 'No process is done'];

        $auth_token = $request->bearerToken();
        $system_otp = rand(1000,9999);

        if(empty($auth_token))
        {
            $result = ['Status' => false, 'Message' => "Invalid Token..."];
            return response()->json($result, 200);
        }

    	$d1 = UserDetail::where('auth_token', $auth_token)->first();

    	if(empty($d1))
    	{
            $result = ['Status' => false, 'Message' => "Invalid Token..."];
            return response()->json($result, 200);
        }

		$user_code = $d1->user_code;
        $user_mobile = $d1->user_mobile;

		$c = UserAuthenticationDetail::where('user_code', $user_code)->update(['user_otp' => $system_otp, 'updated_at' => $date_time]);

        $this->send_message($system_otp, $user_mobile);
		if($c > 0) 
        {
            
        	$result = ['Status' => true, 'Message' => "OTP is resent successfully..."];
        }
        else
        {
        	$result = ['Status' => false, 'Message' => "OTP is not updated..."];
        }
        return response()->json($result, 200);
    }

    public function getUserDetails(Request $request)
    {
        
        $date_time = date("Y-m-d H:i:s");
        $auth_token = trim($request->auth_token);

        $result = ['Status' => 400,  'user_code' => "-",  'user_name' => "-", 'user_email' => "-",  'user_mobile' => "-", 'user_pwd' => "-"];

        if(empty($auth_token))
        {
            $result = ['Status' => 403, 'message' => "Invalid Token..."];
            return response()->json($result, 200);
        }

        $d1 = UserDetail::where('auth_token', $auth_token)->first();

        if(empty($d1))
        {
            $result = ['Status' => 403, 'message' => 'Invalid Token...'];
            return response()->json($result, 200);
        }

        $user_code      = "";
        $user_name      = "";
        $user_email     = "";
        $user_mobile    = "";
        $user_pwd       = "";

        $filePath = url('web_images/common.png');
        if(@get_headers(url('uploadkyc').'/'.$d1->user_code.'_KYC.jpg')[0] != 'HTTP/1.1 404 Not Found')
        {
            $filePath = url('uploadkyc').'/'.$d1->user_code . '_KYC.jpg';
        }
        else if(@get_headers(url('uploadkyc').'/'.$d1->user_code.'_KYC.jpeg')[0] != 'HTTP/1.1 404 Not Found')
        {
            $filePath = url('uploadkyc').'/'.$d1->user_code . '_KYC.jpeg';
        }
        else if(@get_headers(url('uploadkyc').'/'.$d1->user_code.'_KYC.png')[0] != 'HTTP/1.1 404 Not Found') 
        {
            $filePath = url('uploadkyc').'/'.$d1->user_code . '_KYC.png';
        }

        if($d1->user_type == "PAID")
        {
            $user_code   = $d1->user_code;
            $user_name   = $d1->user_name;
            $user_email  = $d1->user_email;
            $user_mobile = $d1->user_mobile;
            $user_pwd    = $d1->user_mpin;

            $user_personal_details = array();

            if(!empty($d1->userpersonaldetail))
            {
                $today = date("Y-m-d");
                //$diff = date_diff(date_create($d1->userpersonaldetail->user_dob), date_create($today));
                $user_personal_details = array(
                    'user_name'          => $d1->userpersonaldetail->user_name,
                    'user_age'           => $d1->UserPersonalDetail->user_dob,
                    'user_address'       => $d1->userpersonaldetail->user_address,
                    'user_city'          => $d1->userpersonaldetail->user_city,
                    'user_district'      => $d1->userpersonaldetail->user_district,
                    'user_state'         => $d1->userpersonaldetail->user_state,
                    'user_qualification' => $d1->userpersonaldetail->user_qualification);
            }

            $result = ['Status' => 200, 'user_code' => $user_code, 'user_name' => $user_name, 'user_email' => $user_email, 'user_mobile' => $user_mobile, 'user_pwd' => $user_pwd, 'user_personal_details' => $user_personal_details, 'profileImage' => $filePath];
        }       
        return response()->json($result, 200);
    }

    public function checkUserDetails($user_mobile, $user_email)
    {
        //Either Mobile No or Email Id is present
        $count = UserDetail::where('user_mobile', $user_mobile)->orWhere('user_email', $user_email)->count();
        if($count == 0) {
            return true;
        }
        else {
           return false;  
        }
    }

    public function sendWelcome()
    {
        //Either Mobile No or Email Id is present
        $d1 = UserDetail::get();
        foreach($d1 as $d)
        {
        	$mobile = $d->user_mobile;

        	$message ="Welcome to Vidhvaa";

        	$this->send_new_message($message, $mobile);

        }
       
    }

    public function send_message($otp, $mobile)
    {
        $sms_url = "https://www.fast2sms.com/dev/bulkV2?authorization=HTO54XcDQ3xKk1iF7S2YUJECztabvLh8GgInjuwPNeqrVBsp6W9Xi5VAsc1oUL3qeQErJC8j4ykxfKl2";
        $sms_url = $sms_url. "&route=s&sender_id=TXTIND&message=2&variables_values=".$otp."&flash=0&numbers=".$mobile;

        $client = new \GuzzleHttp\Client();
        $res = $client->get($sms_url);
        $res_code = $res->getStatusCode();
        $res_content =$res->getBody();   
    }

    public function send_new_message($message, $mobile)
    {
        $sms_url = "https://www.fast2sms.com/dev/bulkV2?authorization=HTO54XcDQ3xKk1iF7S2YUJECztabvLh8GgInjuwPNeqrVBsp6W9Xi5VAsc1oUL3qeQErJC8j4ykxfKl2";
        $sms_url = $sms_url. "&language=english&route=v3&sender_id=TXTIND&message=".$message."&flash=0&numbers=".$mobile;

        $client = new \GuzzleHttp\Client();
        $res = $client->get($sms_url);
        $res_code = $res->getStatusCode();
        $res_content =$res->getBody();   
    }

    public function sendNotification($user_firebase_id)
    {
        $api_key = "AAAAlUyHTRE:APA91bGYLS1wgDGP5H-rA-SdW-wbKtqntZZNMS6z1zuXSg6_Z1P8-lxXv_k1Sh1qf1bMfmoES98ll5SlMFYNSXprse2cR9HPxkXHmaNLniV6ifyiJXc6RLU4DqF7rh5kYeJtVyW0f4Zd";

        $url = 'https://fcm.googleapis.com/fcm/send';

        $fields = array (
            'registration_ids' => array (
                    $user_firebase_id
            ),
            'data' => array (
                    'flag' => "LOGOUT"
            )
        );

        //header includes Content type and api key
        $headers = array(
            'Content-Type:application/json',
            'Authorization:key='.$api_key
        );
                    
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
        $result = curl_exec($ch);
        if ($result === FALSE) {
            die('FCM Send Error: ' . curl_error($ch));
        }
        curl_close($ch);
    }

    public function getPaidUserPersonalDetails(Request $request)
    {
        
        $date_time = date("Y-m-d H:i:s");
        $auth_token = trim($request->auth_token);

        if(empty($auth_token))
        {
            $result = ['Status' => 403, 'message' => "Invalid Token..."];
            return response()->json($result, 200);
        }

        $d1 = UserDetail::where('auth_token', $auth_token)->where('user_type', 'PAID')->first();
        
        if(empty($d1))
        {
            $result = ['Status' => 403, 'message' => 'Invalid Token...'];
            return response()->json($result, 200);
        }

        if(empty($d1->userpersonaldetail))
        {
            $result = ['Status' => 409, 'message' => "User Personal Details Not Available...", 'user_personal_details' => ''];
            return response()->json($result, 200);
        }

        if($d1->user_type == "PAID")
        {
            $today = date("Y-m-d");
            //$diff = date_diff(date_create($d1->userpersonaldetail->user_dob), date_create($today));

            $user_personal_details = array(
                'user_name'          => $d1->userpersonaldetail->user_name,
                'user_age'           => $d1->userpersonaldetail->user_dob,
                'user_address'       => $d1->userpersonaldetail->user_address,
                'user_city'          => $d1->userpersonaldetail->user_city,
                'user_district'      => $d1->userpersonaldetail->user_district,
                'user_state'         => $d1->userpersonaldetail->user_state,
                'user_qualification' => $d1->userpersonaldetail->user_qualification);

            $result = ['Status' => 200, 'message' => "User Personal Details...", 'user_personal_details' => $user_personal_details];
            return response()->json($result, 200);
        }

        $result = ['Status' => 409, 'message' => "Guest User...", 'user_personal_details' => ''];     
        return response()->json($result, 200);
    }

    public function updatePaidUserPersonalDetails(Request $request)
    { 
        $result         = ['Status' => 400, 'message' => 'No process is done'];

        if(empty($request->auth_token))
        {
            $result = ['Status' => 403, 'message' => "Invalid Token1..."];
            return response()->json($result, 401);
        }

        $d1 = UserDetail::where('auth_token', $request->auth_token)->first();
        
        if(empty($d1))
        {
            $result = ['Status' => 403, 'message' => 'Invalid Token2...'];
            return response()->json($result, 401);
        }
        
        $validator = Validator::make($request->all(), [
            'user_name'          => 'required',
            'user_age'           => 'required',
            'user_address'       => 'required',
            'user_city'          => 'required',
            'user_district'      => 'required',
            'user_state'         => 'required',
            'user_qualification' => 'required'
        ]);

        if($validator->fails()){  
            $errors = $validator->errors();
            $message = '';
            foreach ($errors->all() as $error) {
               $message .= $error . ' ';
            }       
            $result         = ['Status' => 400, 'message' => $message];
            return response()->json($result, 200);     
        }

       ;
            
        $date_time      = date("Y-m-d H:i:s");
        $user_code      = $d1->user_code;

        $d1->user_name = $request->user_name;
        $d1->save();
       
        if(!empty($d1->userpersonaldetail))
        {
            $d1->userpersonaldetail->user_name          = $request->user_name;
            $d1->userpersonaldetail->user_dob           = $request->user_age;
            $d1->userpersonaldetail->user_address       = $request->user_address;
            $d1->userpersonaldetail->user_city          = $request->user_city;
            $d1->userpersonaldetail->user_district      = $request->user_district;
            $d1->userpersonaldetail->user_state         = $request->user_state;
            $d1->userpersonaldetail->user_qualification = $request->user_qualification;
            $d1->userpersonaldetail->save();
                
            $result = ['Status' => 200, 'message' => "Personal Details Updated Successfully..."];
        }
        else
        {            
            $uerpersonal = new UserPersonalDetail;

            $uerpersonal->user_code          = $user_code;
            $uerpersonal->user_name          = $request->user_name;
            $uerpersonal->user_dob           = $request->user_age;
            $uerpersonal->user_address       = $request->user_address;
            $uerpersonal->user_city          = $request->user_city;
            $uerpersonal->user_district      = $request->user_district;
            $uerpersonal->user_state         = $request->user_state;
            $uerpersonal->user_qualification = $request->user_qualification;
            $uerpersonal->save();

            if(!empty($uerpersonal))
                $result = ['Status' => 200, 'message' => "Personal Details Stored Successfully..."];
            else
                $result = ['Status' => 200, 'message' => "Personal Details Not Stored Successfully..."];            
        }
        return response()->json($result, 200);
    }

    public function logout(Request $request)
    {
        $date_time = date("Y-m-d H:i:s");
        $auth_token = trim($request->auth_token);

        if(empty($auth_token))
        {
            $result = ['Status' => 403, 'message' => "Invalid Token..."];
            return response()->json($result, 200);
        }

        $d1 = UserDetail::where('auth_token', $auth_token)->first();

        if(empty($d1))
        {
            $result = ['Status' => 403, 'message' => 'Invalid Token...'];
            return response()->json($result, 200);
        }

        $user_code = $d1->user_code;

        UserAuthenticationDetail::where('user_code', $user_code)->update(['auth_token' => '', 'user_otp' => '', 'updated_at' => $date_time]);

        UserDetail::where('user_code', $user_code)->update(['auth_token' => '', 'updated_at' => $date_time, 'login_status' => 0]);

        $result = ['Status' => 200, 'message' => 'User Logout successfully...'];
        return response()->json($result, 200);
    }

    public function testApi(Request $request)
    {

        if(!array_key_exists('authorization', $request->header()))
        {
            $result         = ['Status' => false, 'message' => 'Invalid Token...'];
            return response()->json($result, 401);
        }

        if(empty($request->header('authorization')))
        {
            $result         = ['Status' => false, 'message' => 'Invalid Token...'];
            return response()->json($result, 401); 
        }

        if(!strrchr($request->header('authorization'), 'Bearer'))
        {
            $result         = ['Status' => false, 'message' => 'Invalid Token...'];
            return response()->json($result, 401); 
        }        

        $authToken = str_replace('Bearer ', '', trim($request->header('authorization')));

        if($authToken != "anUwcEVBQmw5YTFNOVRVck1VeG1IQkdSZnVvdHhu6004379beccec")
        {
            $result         = ['Status' => false, 'message' => 'Invalid Token...'];
            return response()->json($result, 401); 
        }

        $data[0] = array(
                'user_name'          => 'aaa',
                'user_address'       => '80, feet road',
                'user_city'          => 'madurai',
                'user_district'      => 'madurai',
                'user_state'         => 'Tamil Nadu',
                'user_qualification' => 'MBA'
            );

        $data[1] = array(
                'user_name'          => 'bbb',
                'user_address'       => '80, feet road',
                'user_city'          => 'theni',
                'user_district'      => 'theni',
                'user_state'         => 'Tamil Nadu',
                'user_qualification' => 'MBA'
            );
        
        $result = ['Status' => true, 'message' => 'User Details', 'user_details' => $data];
             
        return response()->json($result, 200);
    }

    public function storeUserDetails(Request $request)
    {
        $result         = ['Status' => 400, 'message' => 'No process is done'];
        
        $validator = Validator::make($request->all(), [
            'user_mobile' => 'required|regex:/^[6789]\d{9}$/|integer|digits:10',
            'user_email'  => 'required|email:rfc,dns',
            'otp_token'   => 'required'
        ]);

        if($validator->fails()){  
            $errors = $validator->errors();
            $message = '';
            foreach ($errors->all() as $error) {
               $message .= $error . ' ';
            }       
            $result         = ['Status' => 400, 'message' => $message];
            return response()->json($result, 200);     
        }

        $user_email     = trim($request->user_email);
        $user_mobile    = trim($request->user_mobile);
        $otp_token      = trim($request->otp_token);
            
        $date_time      = date("Y-m-d H:i:s");
        $user_code      = "UR". rand(10000,99999);
        $firebase_token = trim($request->firebase_token);
        $level          = 0;

        $auth_token     = uniqid(base64_encode(Str::random(30)));
        $user_otp       = rand(1000,9999);
        
        $user_data    = [];
        $user_data[0] = $user_mobile;
        $user_data[1] = $user_email;

        $check_user_deatils = UserDetail::where('user_status','1')->where('user_mobile', $user_mobile)->first();

        if(!empty($check_user_deatils))
        {
            $check = UserDetail::where('user_mobile', $user_mobile)->first();
            if(!empty($check))
            {
                //Already Registered User
                $user_code = $check->user_code;
                $user_type = $check->user_type;
                $user_mpin = $check->user_mpin;

                $d2 = UserAuthenticationDetail::where('user_code', $user_code)->first();

                if(!empty($d2))
                {
                    UserDetail::where('user_code', $user_code)->update(['auth_token' => $auth_token, 'updated_at' => $date_time]);
                    UserAuthenticationDetail::where('user_code', $user_code)->update(['user_otp' => $user_otp, 'auth_token' => $auth_token, 'user_status' => "0", 'updated_at' => $date_time]);
                    // $msg =  "%3C%23%3E Your VIDHVAA App code is: ". $user_otp ." " . $otp_token;
                    $msg =  "%3C%23%3E OTP for your verification  on VIDHVAA is " . $user_otp . " and is valid for <2 minutes>. Do not share this OTP to anyone for security reasons.";
                    SendSms::sendSMS($msg, $user_mobile);
                }
                else {
                    UserDetail::where('user_code', $user_code)->update(['auth_token' => $auth_token, 'updated_at' => $date_time]);
                    UserAuthenticationDetail::insert(['user_code' => $user_code, 'auth_token' => $auth_token, 'user_otp' => $user_otp, 'user_status' => "0", 'created_at' => $date_time, 'updated_at' => $date_time]);
                    // $msg =  "%3C%23%3E Your VIDHVAA App code is: ". $user_otp ." " . $otp_token;

                    $msg =  "%3C%23%3E OTP for your verification  on VIDHVAA is " . $user_otp . " and is valid for <2 minutes>. Do not share this OTP to anyone for security reasons.";

                    SendSms::sendSMS($msg, $user_mobile);
                }
                //$user = UserDetail::create($user_code);
                // $token =  $check->createToken('MyApp')->accessToken;

                $result = ['Status' => 200, 'auth_token' => $auth_token, 'user_code' => $user_code, 'message' => "Already Existing User entry.."];
            }
            else
            {
                $result = ['Status' => 204, 'message' => "Either Mobile No or Email Id is already present..."];
            }
        }
        else
        {
            $check_user_deatils1 = UserDetail::where('user_status','0')->where(function ($query) use($user_data) { $query->where('user_mobile', $user_data[0])->orWhere('user_email', $user_data[1]); })->first();
            if(empty($check_user_deatils1))
            {                
                $v = UserDetail::insert([ 'user_code' => $user_code,  'user_name' => 'Guest',  'user_email' => $user_email, 'user_mobile' => $user_mobile, 'user_type' => "GUEST", 'user_mpin' => '-', 'user_reg_mode' => 'MOBILE', 'auth_token' => $auth_token, 'user_status' => "0", 'created_at' => $date_time, 'updated_at' => $date_time]);

                if(!empty($v))
                {
                    UserAuthenticationDetail::insert(['user_code' => $user_code, 'auth_token' => $auth_token, 'user_otp' => $user_otp, 'user_status' => "0", 'created_at' => $date_time, 'updated_at' => $date_time]);
                    // $msg =  "%3C%23%3E Your ExampleApp code is: ". $user_otp ." " . $otp_token;
                    $msg =  "%3C%23%3E OTP for your verification  on VIDHVAA is " . $user_otp . " and is valid for <2 minutes>. Do not share this OTP to anyone for security reasons.";
                    SendSms::sendSMS($msg, $user_mobile);                  
                    $result = ['Status' => 200, 'auth_token' => $auth_token, 'message' => "OTP Succesfully Send To You Mobile Number..."];
                }
                else
                    $result = ['Status' => 204, 'message' => "Please Try Agin...."];
            }
            else
            {              
                $user_code = $check_user_deatils1->user_code;

                UserDetail::where('user_code', $user_code)->update(['user_mobile' => $user_mobile, 'user_email' => $user_email, 'auth_token' => $auth_token, 'updated_at' => $date_time]);
                UserAuthenticationDetail::where('user_code', $user_code)->update(['user_otp' => $user_otp, 'auth_token' => $auth_token, 'user_status' => "0", 'updated_at' => $date_time]);
                // $msg =  "%3C%23%3E Your ExampleApp code is: ". $user_otp ." " . $otp_token;
                $msg =  "%3C%23%3E OTP for your verification  on VIDHVAA is " . $user_otp . " and is valid for <2 minutes>. Do not share this OTP to anyone for security reasons.";
                SendSms::sendSMS($msg, $user_mobile);

                $result = ['Status' => 200, 'auth_token' => $auth_token, 'message' => "OTP Succesfully Send To You Mobile Number..."];
            }          
        }
        return response()->json($result, 200);
    }

    public function sendOneTimePassword(Request $request)
    {
        $date_time      = date("Y-m-d H:i:s");
        $result         = ['Status' => 400, 'message' => 'No process is done'];

        if(empty($request->auth_token))
        {
            $result = ['Status' => 420, 'message' => "Invalid Token.."];
            return response()->json($result, 200);
        }

        $validator = Validator::make($request->all(), [            
            'user_otp'       => 'required'
        ]);

        if($validator->fails()){  
            $errors = $validator->errors();
            $message = '';
            foreach ($errors->all() as $error) {
               $message .= $error . ' ';
            }       
            $result         = ['Status' => 400, 'message' => $message];
            return response()->json($result, 200);     
        }

        $auth_token     = trim($request->auth_token);
        $firebase_token = trim($request->firebase_token);
        $user_otp       = trim($request->user_otp);
        $level          = 0;
        
        $d1 = UserAuthenticationDetail::where('auth_token', $auth_token)->first();

        if(empty($d1))
        {
            $result = ['Status' => 420, 'message' => "Invalid Token.."];
            return response()->json($result, 200);
        }

        $system_otp = $d1->user_otp;
        if($user_otp != $system_otp)
        {
            $result = ['Status' => 420, 'message' => "Invalid OTP.."];
            return response()->json($result, 200);
        }

        $user_code  = $d1->user_code;
                
        $d2 = UserDetail::where('user_code', $user_code)->first();

        if(empty($d2))
        {
            $result = ['Status' => 403, 'message' => "Invalid Token.."];
            return response()->json($result, 200);
        }

        if(!empty($d2->login_status) &&  $d2->login_status == '1') 
        {
            if(!empty($d2->firebase_token))
            {
                $sendNotification = new SendPushNotification;
                $resp = $sendNotification->sendNotification('', 'Logout', $d2->firebase_token);
            }
        }
        $user_type = "-";
        $user_Name = "-";
        $user_mpin = "-";
        
        $user_name = $d2->user_name;            
        if ($d2->user_type == 'PAID') {
            $level  = 2;
            $user_type = 'PAID';
            $user_mpin = $d2->user_mpin;
        }
        else
        {
            $user_type = 'GUEST';
            $level  = 1;
            $user_mpin = "-";
        }

        $new_auth_token     = uniqid(base64_encode(Str::random(30)));

        UserAuthenticationDetail::where('user_code', $user_code)->update(['user_status' => "1", 'auth_token' => $new_auth_token, 'updated_at' => $date_time, 'user_otp' => '']);

        $data['user_status'] = "1";
        $data['auth_token'] = $new_auth_token;
        if(!empty($firebase_token))
        {
            $data['firebase_token'] = $firebase_token;
        }
        $data['updated_at'] = $date_time;
        $data['login_status'] = "1";

        UserDetail::where('user_code', $user_code)->update($data);       

        $result = ['Status' => 200, 'message' => "OTP is successfully verified...", 'user_name'  => $user_name, 'user_level' => $level, 'user_type'  => $user_type, 'user_mpin'  => $user_mpin, 'user_code'  => $user_code, 'auth_token' => $new_auth_token];

        return response()->json($result, 200);
    }

    public function resendOneTimePassword(Request $request)
    {
        $date_time = date("Y-m-d H:i:s");
        $result = ['Status' => 400, 'message' => 'No process is done'];

        $auth_token = trim($request->auth_token); 
        $otp_token = trim($request->otp_token); 
        $system_otp = rand(1000,9999);

        if(empty($auth_token) and empty($otp_token))
        {
            $result = ['Status' => 403, 'message' => "Invalid Token..."];
            return response()->json($result, 200);
        }

        $d1 = UserDetail::where('auth_token', $auth_token)->first();

        if(empty($d1))
        {
            $result = ['Status' => 403, 'message' => "Invalid Token..."];
            return response()->json($result, 200);
        }

        $user_code = $d1->user_code;
        $user_mobile = $d1->user_mobile;

        $c = UserAuthenticationDetail::where('user_code', $user_code)->update(['user_otp' => $system_otp, 'updated_at' => $date_time]);
        
        // $msg =  "%3C%23%3E Your ExampleApp code is: ". $user_otp ." " . $otp_token;
        $msg =  "%3C%23%3E OTP for your verification  on VIDHVAA is " . $user_otp . " and is valid for <2 minutes>. Do not share this OTP to anyone for security reasons.";
        SendSms::sendSMS($msg, $user_mobile);
        if($c > 0) 
        {
            
            $result = ['Status' => 200, 'message' => "OTP is resent successfully..."];
        }
        else
        {
            $result = ['Status' => 409, 'message' => "OTP is not updated..."];
        }
        return response()->json($result, 200);
    }
}