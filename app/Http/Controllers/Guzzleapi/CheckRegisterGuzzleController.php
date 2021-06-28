<?php

namespace App\Http\Controllers\NewApi;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Helper\Helper;


class CheckRegisterGuzzleController extends Controller
{
    //
    public function checkRegister(Request $request)
	{
        $authToken  = $request->bearerToken();
        $examCode   = trim($request->examCode);
        $registerNo = trim($request->registerNo);

        $authStatus = true;
        $dateTime   = date("Y-m-d H:i:s");

        list($userStatus, $userCode, $userName, $userMobile, $userEmail) = GetMobileCommon::getUserDetails($authToken);

        if($userStatus == 0) {
          $authStatus = false;
        }

        if($authStatus)
        {
            $registerDetails = UserRegistrationDetail::where('user_code', $userCode)->where('exam_code', $examCode)->first();

            if($registerDetails) {
                return response()->json(['Status' => true, 'Message' => 'User is already Registered..'], 200);
            }
            else
            {
                $paymentDetails = UserPaymentDetail::select('register_no')->where('user_code', $userCode)->first();

                if($paymentDetails)
                {
                    $systemRegisterNo  = $paymentDetails->register_no;

                    if($systemRegisterNo == $registerNo)
                    {
                        $queryStatus = UserRegistrationDetail::insert(['user_code' => $userCode, 'register_no' => $registerNo, 
                                                                'exam_code' => $examCode, 'register_status' => '1',
                                                                'created_at' => $dateTime, 'updated_at' => $dateTime]);

                        if($queryStatu) {
                            return response()->json(['Status' => true, 'Message' => 'User is registered Successfully..'], 200);
                        }
                        else{
                            return response()->json(['Status' => false, 'Message' => 'Unexpected Error occurs..'], 200);
                        }
                    }
                    else {
                        return response()->json(['Status' => false, 'Message' => 'Invalid Register No'], 200);
                    }
                }
            }
        }
        else {
            return response()->json(['status' => false, 'Message' => 'Authentication Failure'], 401);
        }
    }


    //new modification 24-05-2021
    public function checkExamRegisterNumber(Request $request)
    {
        $authToken = $request->bearerToken();        
        if(empty($authToken))
        {
            return response()->json(['status' => false, 'Message' => 'Authentication Failure'], 401);            
        }

        $userDetail = UserDetail::where('auth_token', $authToken)->first(); 
        if(empty($userDetail))
        {
            return response()->json(['status' => false, 'Message' => 'Authentication Failure'], 401);
        }

        $validator = Validator::make($request->all(), [
            'planCode'    => 'required',
            'examCode'    => 'required',
            'registerNo'  => 'required',
            'examType'    => 'required'
        ]);

        if($validator->fails()){  
            $errors = $validator->errors();
            $message = '';
            foreach ($errors->all() as $error) {
               $message .= $error . ' ';
            }       
            $result         = ['status' => false, 'message' => $message];
            return response()->json($result, 200);
        }

        
        $planCode   = trim($request->planCode);
        $examCode   = trim($request->examCode);
        $registerNo = trim($request->registerNo);
        $examType   = trim($request->examType);

        $userPlanDetail = UserPlanDetail::where('user_code', $userDetail->user_code)->where('plan_code', $planCode)->where('plan_status', 1)->first();

        if(empty($userPlanDetail))
        {
            return response()->json(['status' => false, 'Message' => 'Invalid Subscription'], 200);
        }
        // return response()->json(['Status' => false, 'Message' => $userPlanDetail->userpaymentdetail], 200);

        $planEndDate = $userPlanDetail->plandetail->plan_end_date;
        
        if(!empty($planDetail) && (strtotime($planEndDate) > strtotime(date('Y-m-d'))))
        {
            return response()->json(['status' => false, 'Message' => 'User subscriber plan has been expired...'], 200);
        }

        $userRegisterNumber = $userPlanDetail->userpaymentdetail->register_no;

        if($userRegisterNumber != $registerNo)
        {
            return response()->json(['Status' => false, 'Message' => 'Invalid Register No'], 200);
        }

        $registerDetails = $userDetail->userregistration->where('plan_code', $planCode)->where('exam_code', $examCode)->where('exam_type', $examType)->where('register_no', $registerNo);
        // return response()->json(['Status' => $registerDetails, 'Message' => $userDetail->userregistration], 200);
        if(count($registerDetails) > 0)
        {
            return response()->json(['Status' => true, 'Message' => 'User is already Registered..', 'Data' => false], 200);
        }
        
        $inputArray = array(
                'plan_code'         => $planCode, 
                'user_code'         => $userDetail->user_code,
                'register_no'       => $registerNo,
                'exam_code'         => $examCode, 
                'exam_type'         => $examType, 
                'register_status'   => '1',
                'created_at'        => date('Y-m-d H:i:s'), 
                'updated_at'        => date('Y-m-d H:i:s'));

        $queryStatus = UserRegistrationDetail::insert($inputArray);

        if($queryStatus > 0)
        {
            return response()->json(['Status' => true, 'Message' => 'User is registered Successfully...', 'Data' => true], 200);
        }
        return response()->json(['Status' => false, 'Message' => 'Unexpected Error occurs..'], 200);
    }
}