<?php

namespace App\Http\Controllers\Guzzleapi;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helper\Helper;

class OnlineExamGuzzleController extends Controller
{
    //
    public function ExamCode_guzzle(Request $request)
	{
      $authToken = 'YTRjZXlaZHJXR3NaRWtzTjVvMFhWb01lVXM3Y0VU60d05c6d506ee';
      $URI='http://13.235.243.15/api/online-exam-code';
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


  public function checkRegister($userCode, $examCode)
  {
      $paymentDetails = UserPaymentDetail::select('register_no')->where('user_code', $userCode)->first();

      if($paymentDetails)
      {
        $systemRegisterNo  = $paymentDetails->register_no;

        $registerQuery = UserRegistrationDetail::where('user_code', $userCode)
                                                     ->where('register_no', $systemRegisterNo)
                                                     ->where('exam_code', $examCode)->first();

        if($registerQuery){
          return true;
        }
      }

      return false;
  }

  public function getStreamUrl(Request $request)
  {
      $authToken    = $request->bearerToken();
      $languageCode = trim($request->languageCode);
     
      $streamProtocol = "ws:";
      $streamAddress  = "awslive.vidhvaa.in:5080";
      $streamType     = "WebRTCAppEE";
      $streamName     = "-";
      $streamId       = "";
      $streamStatus   = false;

      list($userStatus, $userCode, $userName, $userMobile, $userEmail) = GetMobileCommon::getUserDetails($authToken);

      $validateResult = GetMobileCommon::validateFields($request, [ 'languageCode'   => 'required' ]);

      if(!$validateResult['Status']) {
          return response()->json($validateResult, 200);
      }

      if($userStatus == 0) {
          return response()->json(['status' => false, 'Message' => 'Authentication Failure'], 401);
      }

      if($languageCode == "LN01") {
          $streamName = "websocket";
          $streamId   = "059950418227589746904341";
      }
      else if($languageCode == "LN02") {
          $streamName = "websocket";
          $streamId   = "059950418227589746904341";
      }

      return response()->json(['Status' => true, 'Message' => 'Success', 'StreamProtocol' => $streamProtocol, 'StreamAddress' => $streamAddress, 
                                'StreamType' => $streamType, 'StreamName' => $streamName, 
                                'StreamId' => $streamId, 'StreamStatus' => $streamStatus], 200);
  }


  public function getTermsAndConditions(Request $request)
  {
      $authToken    = $request->bearerToken();
      $languageCode = trim($request->languageCode);
     
      $authStatus       = true;
      $termsConditions  = [];
      $tempDateeTime         = date("Y-m-d H:i:s");

      $validateResult = GetMobileCommon::validateFields($request, [ 'languageCode'   => 'required' ]);

      if(!$validateResult['Status']) {
          return response()->json($validateResult, 200);
      }

      list($userStatus, $userCode, $userName, $userMobile, $userEmail) = GetMobileCommon::getUserDetails($authToken);

      if($userStatus == 0) {
          $authStatus = false;
      }

      if($authStatus)
      {
          if($languageCode == "LN01")
          {
              array_push($termsConditions, ['IndexValue' => 0, 
                  'StateName' => 'இணையவழி நேரலைத் தேர்வை மாணவர்கள் எப்படி எழுத வேண்டும் என்பதற்கு முன்மாதிரித் தேர்வாகும்.']);   //1
              array_push($termsConditions, ['IndexValue' => 1, 
                  'StateName' => 'இணையவழி நேரலைத் தேர்விற்கு பதிவு செய்த மாணவர்கள் மட்டுமே மாதிரித் தேர்வில் பங்கு பெற முடியும்.']);   //1
              array_push($termsConditions, ['IndexValue' => 2, 
                  'StateName' => 'மாணவர்கள் பதிவு செய்த கைப்பேசி எண் மற்றும் Mail Id சரிபார்க்கப்பட்டு தேர்வெழுத அனுமதிக்கப்படுவர். ']);   //1
              array_push($termsConditions, ['IndexValue' => 3, 
                  'StateName' => 'இத்தேர்வில் 100 கேள்விகள் கேட்கப்படும்.']);   //1
              array_push($termsConditions, ['IndexValue' => 4, 
                  'StateName' => 'அனைத்து வினாக்களும் இணையவழி நேரலைத் தேர்விற்கு இணையான தரத்தில் வழங்கப்படும்.']);   //1
              array_push($termsConditions, ['IndexValue' => 5, 
                  'StateName' => 'வினாக்களுக்கான காலஅளவு நிர்ணயிக்கப்பட்டுயிருக்கும்.']);   //1
              array_push($termsConditions, ['IndexValue' => 6, 
                  'StateName' => 'தேர்வு முடிவுகள் உடனடியாக வழங்கப்படும்.']);   //1
              array_push($termsConditions, ['IndexValue' => 7, 
                  'StateName' => 'தரவரிசைப்பட்டியல் வெளியிடப்பமாட்டாது.']);   //1
            
          }
          else if($languageCode == "LN02")
          {
              array_push($termsConditions, ['IndexValue' => 0, 
                  'StateName' => '100 numbers of questions with 18 seconds to answer each question.']);   //1
              array_push($termsConditions, ['IndexValue' => 1, 
                  'StateName' => 'Answer the questions as fast as you can to develop and attain time management.']);   //1
              array_push($termsConditions, ['IndexValue' => 2, 
                  'StateName' => 'You can get the rank list and solutions for the questions at end of the task. ']);   //1
              array_push($termsConditions, ['IndexValue' => 3, 
                  'StateName' => 'The Examiner who are maintaining the steady rank list over a month will be given scholarship for attaining higher education.']);   //1
              array_push($termsConditions, ['IndexValue' => 4, 
                  'StateName' => 'You can get the rank list and solutions for the questions at end of the task.']);   //1
              
          }

           return response()->json(['Status' => true, 'Message' => 'success', 'TermsAndConditions' => $termsConditions], 200);
          
          
      }
      else {
        return response()->json(['status' => false, 'Message' => 'Authentication Failure'], 401);
      }

  }

  public function getTaskQuestions(Request $request)
  {
      $authToken    = $request->bearerToken();
      $languageCode = trim($request->languageCode);

     
      $authStatus       = true;
      $modelQuestions   = [];
      $dateTime         = date("Y-m-d H:i:s");
      $questionStatus   = false;
      $localStatus      = false;

      $validateResult = GetMobileCommon::validateFields($request, [ 'languageCode'   => 'required' ]);

      if(!$validateResult['Status']) {
          return response()->json($validateResult, 200);
      }

      list($userStatus, $userCode, $userName, $userMobile, $userEmail) = GetMobileCommon::getUserDetails($authToken);

      if($userStatus == 0) {
          $authStatus = false;
      }

      if($authStatus)
      {

          $status = 0;
          $modelExam = OnlineExam::orderby('on_date', 'DESC')->limit(1)->get();
          $modelCode = "0";
          $modelDate = "-";
          $mq_time = "-";
          
          foreach($modelExam as $d)
          {
              $dat        = $d->on_date;
              $tim        = $d->on_time;
              $modelCode  = $d->on_code;
              $localDate  = explode(" ", $dat);
              $modelDate  = $localDate[0]." ".$tim;
              $currDate   = date("Y-m-d H:i:s");
              $curTime    = strtotime($currDate);
              $myTime     = strtotime($modelDate);

              if($myTime >= $curTime)
              {
                  $modelDateOnly = $localDate[0];
                  $modelTimeOnly = $tim;
                  $localStatus   = true;
              }
          }
          //Get Data
          
          if($localStatus)
          {
            $d1 = OnlineExamQuestion::where('on_code', $modelCode)->where('ln_code', $languageCode)->orderByRaw("CAST(on_order as UNSIGNED) ASC")->get();
          
            $q = 0;
            foreach($d1 as $d)
            {
                $mq_seconds = "18";
                if($d->on_seconds != null && $d->on_seconds != "") {
                    $mq_seconds = $d->on_seconds;
                } 

                array_push($modelQuestions, ['QuestionCode' => $d->on_ques_id, 
                                        'LanguageCode' => $languageCode, 
                                        'QuestionOrder' => $d->on_order, 
                                        'Question' => $d->on_question, 
                                        'QuestionPattern' => $d->on_pattern,
                                        'QuestionSeconds' => $mq_seconds,
                                        'AnswerOne' => $d->on_ans_1, 
                                        'AnswerTwo' => $d->on_ans_2,  
                                        'AnswerThree' => $d->on_ans_3, 
                                        'AnswerFour' => $d->on_ans_4,
                                        'CorrectAnswer' => $d->on_correct_ans,
                                        'Explanation' => $d->on_explain
                                            ]);
                
                $questionStatus = true;
            }


            if($questionStatus)
            {
                return response()->json(['Status' => true, 'Message' => 'success', 'OnlineExamCode' =>$modelCode, 
                                          'OnlineExamDate' => $modelDateOnly." ".$modelTimeOnly,
                                          'CurrentDate' => date("Y-m-d H:i:s"),
                                          'Questions' => $modelQuestions], 200);
            }
            else
            {
              return response()->json(['Status' => false, 'Message' => 'No Exams Available'], 200);
            }
          }
          else
          {
            return response()->json(['Status' => false, 'Message' => 'No Exams Available'], 200);
          }
            
          
          
      }
      else {
        return response()->json(['status' => false, 'Message' => 'Authentication Failure'], 401);
      }

  }

  public function storeResult(Request $request)
  {
      $authToken       = $request->bearerToken();
      $correctAnswer   = trim($request->correctAnswer);
      $wrongAnswer     = trim($request->wrongAnswer);
      $notAnswer       = trim($request->notAnswer);
      $timeTaken       = trim($request->timeTaken);
      $examCode        = trim($request->onlineExamCode);
      $languageCode    = trim($request->languageCode);
      $allAnswer       = $request->allAnswer;
       
      $authStatus     = true;
      $resultStatus   = false;
      $tempDateeTime  = date("Y-m-d H:i:s");
              
      list($userStatus, $userCode, $userName, $userMobile, $userEmail) = GetMobileCommon::getUserDetails($authToken);
        
      if($userStatus == 0) {
          $authStatus = false;
      }

      if($authStatus)
      {
          $modelResult = OnlineExamResult::where('user_code', $userCode)->where('on_code', $examCode)->where('ln_code', $languageCode)->first();

          $encryptedResult = base64_encode( serialize( $allAnswer ) );

          if(!$modelResult)
          {
              $transId = rand(10000,99999);

              $tempDatea = ['trans_id' => $transId, 'user_code' => $userCode, 
                            'on_code' => $examCode, 'ln_code' => $languageCode,
                            'correct_answer' => $correctAnswer,  'wrong_answer' => $wrongAnswer, 
                            'not_answer' => $notAnswer, 'all_answer' => $encryptedResult, 'time_taken' => $timeTaken,
                            'start_time' => $tempDateeTime,
                            'created_at' => $tempDateeTime, 'updated_at' => $tempDateeTime
                            ];

                                       
              $queryStatus = OnlineExamResult::insert($tempDatea);     

              if($queryStatus > 0 ) {
                  return response()->json(['Status' => true, 'Message' => 'Result is updated...'], 200);
              } 
              else {
                return response()->json(['Status' => false, 'Message' => 'Error!'], 200);
              }
          }
          else {
            return response()->json(['Status' => false, 'Message' => 'Result is Already updated...'], 200);
          }
            
        }
        else {
             return response()->json(['Status' => false, 'Message' => 'Auth Token Error...'], 401);
        }
  
  }


  public function storeIndividualResult(Request $request)
  {
      $authToken     = $request->bearerToken();
      $examCode      = trim($request->onlineExamCode);
      $languageCode  = trim($request->languageCode);
      $questionCode  = trim($request->questionCode);
      $userAnswer    = trim($request->userAnswer);
      $userResult    = trim($request->userResult);
      $userTime      = trim($request->userTime);
         
      $authStatus     = true;
      $resultStatus   = false;
      $tempDatea      = [];
      $tempDateeTime  = date("Y-m-d H:i:s");
              
      list($userStatus, $userCode, $userName, $userMobile, $userEmail) = GetMobileCommon::getUserDetails($authToken);
        
      if($userStatus == 0) {
          $authStatus = false;
      }

      if($authStatus)
      {
          $dailyResult = OnlineExamOneResult::where('user_code', $userCode)->where('on_code', $examCode)->where('on_ques_id', $questionCode)->first();

          if(!$dailyResult)
          {
              array_push($tempDatea, ['trans_id' => rand(10000,99999), 'user_code' => $userCode, 'on_code' => $examCode, 
                                'ln_code' => $languageCode, 'on_ques_id' => $questionCode, 'user_answer' => $userAnswer, 
                                'user_time' => $userTime, 'user_status' => '1', 
                                'created_at' => $tempDateeTime, 'updated_at' => $tempDateeTime, 
                                'user_result' => $userResult]);


              $queryStatus = OnlineExamOneResult::insert($tempDatea);     

              if($queryStatus > 0 ) {
                  return response()->json(['Status' => true, 'Message' => 'Result is updated...'], 200);
              } 
              else {
                return response()->json(['Status' => false, 'Message' => 'Error!'], 200);
              }
          }
          else {
            return response()->json(['Status' => false, 'Message' => 'Result is Already updated...'], 200);
          }
            
        }
        else {
             return response()->json(['Status' => false, 'Message' => 'Auth Token Error...'], 401);
        }
  
  }
    
}