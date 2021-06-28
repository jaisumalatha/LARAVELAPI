<?php

namespace App\Http\Controllers\Guzzleapi;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Helper\Helper;

class UpdateProfileGuzzleController extends Controller
{
    public function UserProfile_guzzle(Request $request)
    {
        $authToken = 'YTRjZXlaZHJXR3NaRWtzTjVvMFhWb01lVXM3Y0VU60d05c6d506ee';    
        $URI='http://13.235.243.15/api/get-paid-user-details';
        $headers = ['Content-Type' => 'application/json', 'Authorization' => 'Bearer ' . $authToken];
        $params['headers']       = $headers;
        $paramsList = [];
        $params[\GuzzleHttp\RequestOptions::JSON] 	 = $paramsList;
        $request = Helper::PostApi(
            $URI, 
            $params
        );
        return $request;
    }

    public function updatePaidUserDetails(Request $request)
    {
        $authToken = $request->bearerToken();
        
        if(empty($authToken))
        {
            return response()->json(['Status' => false, 'Message' => 'Authentication Failure'], 401);
        }
        $userDetail = UserDetail::where('auth_token', $authToken)->first();
        if(empty($userDetail))
        {
            return response()->json(['Status' => false, 'Message' => 'Authentication Failure'], 401);
        }

        if($userDetail->user_type != 'PAID') 
        {
            return response()->json(['Status' => false, 'Message' => 'Authentication Failure'], 401);
        }

        $validator = Validator::make($request->all(), [
            'userName'          => 'required',
            'userAge'           => 'required',
            'userAddress'       => 'required',
            'userCity'          => 'required',
            'userDistrict'      => 'required',
            'userState'         => 'required',
            'userQualification' => 'required'
        ]);

        if($validator->fails()){  
            $errors = $validator->errors();
            $message = '';
            foreach ($errors->all() as $error) {
               $message .= $error . ' ';
            }       
            $result         = ['Status' => false, 'message' => $message];
            return response()->json($result, 200);
        }

        $userCode        = $userDetail->user_code;
        $personName      = trim($request->userName);

        $personalDetails = UserPersonalDetail::where('user_code', $userCode)->first();

        if(empty($personalDetails))
        {
            return response()->json(['Status' => false, 'Message' => 'Sorry! No personal details available..'], 200);
        }

        $userPersonalDetails = [
                            'user_name'          => $personName,
                            'user_dob'           => trim($request->userAge),
                            'user_address'       => trim($request->userAddress),
                            'user_city'          => trim($request->userCity),
                            'user_district'      => trim($request->userDistrict),
                            'user_state'         => trim($request->userState),
                            'user_qualification' => trim($request->userQualification),
                            'updated_at'         => date('Y-m-d H:i:s')
                        ];

        $queryStatus = UserPersonalDetail::where('user_code', $userCode)->update($userPersonalDetails);
        
        if($queryStatus)
        {
            $updateStatus = UserDetail::where('user_code', $userCode)->update(['user_name' => $personName, 'user_state'=> trim($request->userState),  'updated_at' => date('Y-m-d H:i:s')]);

            $userDetail = UserDetail::where('auth_token', $authToken)->first();

            if(empty($userDetail))
            {
                return response()->json(['Status' => false, 'Message' => 'Authentication Failure'], 401);
            }

            $userState          = $userDetail->user_state;
            $userStateAvailable = (!empty($userState) && $userState != '-')?true:false;

            $filePath = url('web_images/common.png');
            if(@get_headers(url('uploadkyc').'/'.$userDetail->user_code.'_KYC.jpg')[0] != 'HTTP/1.1 404 Not Found')
            {
                $filePath = url('uploadkyc').'/'.$userDetail->user_code . '_KYC.jpg';
            }
            else if(@get_headers(url('uploadkyc').'/'.$userDetail->user_code . '_KYC.jpeg')[0] != 'HTTP/1.1 404 Not Found')
            {
                $filePath = url('uploadkyc').'/'.$userDetail->user_code . '_KYC.jpeg';
            }
            else if(@get_headers(url('uploadkyc').'/'.$userDetail->user_code.'_KYC.png')[0] != 'HTTP/1.1 404 Not Found') 
            {
                $filePath = url('uploadkyc').'/'.$userDetail->user_code . '_KYC.png';
            }
            return response()->json(['Status' => true, 'Message' => 'Profile Details updated successfully...', 'UserName' => $userDetail->user_name, 'UserMpin' => $userDetail->user_mpin, 'UserType' => $userDetail->user_type, 'UserCode' => $userDetail->user_code, 'UserLevel' => $userDetail->user_mpin, 'AuthToken' => $userDetail->auth_token, 'IsUserStateAvailable'  => $userStateAvailable, 'UserEmail' => $userDetail->user_email, 'UserMobile' => $userDetail->user_mobile, 'profileImage' => $filePath], 200);
        }
        else {
            return response()->json(['Status' => false, 'Message' => 'Profile Details is not updated...'], 200);
        }
    }
}