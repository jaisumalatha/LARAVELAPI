<?php

namespace App\Http\Controllers\Guzzleapi;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Libraries\GetMobileCommon;

use App\Models\UserDetail;
use App\Models\ModelExam;
use App\Models\ModelExamQuestion;
use App\Models\ModelExamResult;
use App\Models\ModelExamOneResult;

use App\Models\UserPaymentDetail;
use App\Models\UserRegistrationDetail;

class ModelExamGuzzleController extends Controller
{
    //
    public function getExamCode(Request $request)
	{
      $authToken = $request->bearerToken();
     
      $authStatus      = true;
      $dateTime        = date("Y-m-d H:i:s");
      $testStatus      = false;
      $fiveStatus      = false;
      $registerStatus  = false;
      $languages       = [];

      list($userStatus, $userCode, $userName, $userMobile, $userEmail) = GetMobileCommon::getUserDetails($authToken);

      if($userStatus == 0) {
          $authStatus = false;
      }

      if($authStatus)
      {
          
          array_push($languages, ['LanguageCode' => 'LN01', 'LanguageName' => 'Tamil']);
          array_push($languages, ['LanguageCode' => 'LN02', 'LanguageName' => 'English']);

          $modelExam = ModelExam::orderby('mq_date', 'DESC')->limit(1)->get();
            
          $examCode = "0";
          $examDate = "-";
          $examTime = "-";
          
          foreach($modelExam as $d)
          {
              $tempDate = $d->mq_date;
              $tempTime = $d->mq_time;
              $examCode = $d->mq_code;

              $tempDateOne = explode(" ", $tempDate);

              $examDate = $tempDateOne[0]." ".$tempTime;

              $curDate = date("Y-m-d H:i:s");

              $curTime     = strtotime($curDate);
              $curExamTime = strtotime($examDate);

              if($curExamTime >= $curTime)
              {
                  $modelDate = $tempDateOne[0];
                  $examTime = $tempTime;
                  $testStatus = true;
              }
          }

          //Get Data
          if($testStatus)
          {
              $tempExamTime   = $modelDate." ".$examTime;
              $tempCurrTime   = date("Y-m-d H:i:s");
              $tempExamTime_5 = date("Y-m-d H:i:s", strtotime("-10 minutes", strtotime($tempExamTime)));

              $oneExamTime = strtotime($tempExamTime_5);
              $oneCurrTime = strtotime($tempCurrTime);

              
              if($oneExamTime <= $oneCurrTime) {
                  $fiveStatus = true;         //Portal open less than 5 minutes 
              }
             

              $registrationDetails = UserRegistrationDetail::where('user_code', $userCode)->where('exam_code', $examCode)->first();

              if($registrationDetails) {
                  $registerStatus = true;
              }

              $dateTime  = date("Y-m-d H:i:s");

              return response()->json(['Status' => true, 'Message' => 'success', 'ModelExamCode' => $examCode, 
                                          'RegisterStatus' => $registerStatus, 'FiveMinutesStatus' => $fiveStatus, 
                                          'ModelExamDate' => $tempExamTime_5, 'CurrentDate' => $dateTime], 200);
          }
          else 
          {
              return response()->json(['Status' => false, 'Message' => 'No Exam Available'], 200);
          }
      }
      else {
        return response()->json(['status' => false, 'Message' => 'Authentication Failure'], 401);
      }

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


  public function getTermsAndConditions(Request $request)
  {
      $authToken    = $request->bearerToken();
      $languageCode = trim($request->languageCode);
     
      $authStatus       = true;
      $termsConditions  = [];
      $tempDateeTime         = date("Y-m-d H:i:s");

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

      list($userStatus, $userCode, $userName, $userMobile, $userEmail) = GetMobileCommon::getUserDetails($authToken);

      if($userStatus == 0) {
          $authStatus = false;
      }

      if($authStatus)
      {

          $status = 0;
          $modelExam = ModelExam::orderby('mq_date', 'DESC')->limit(1)->get();
          $modelCode = "0";
          $modelDate = "-";
          $mq_time = "-";
          
          foreach($modelExam as $d)
          {
              $dat        = $d->mq_date;
              $tim        = $d->mq_time;
              $modelCode  = $d->mq_code;
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
            $d1 = ModelExamQuestion::where('mq_code', $modelCode)->where('ln_code', $languageCode)->orderByRaw("CAST(mq_order as UNSIGNED) ASC")->get();
          
            $q = 0;
            foreach($d1 as $d)
            {
                $mq_seconds = "18";
                if($d->mq_seconds != null && $d->mq_seconds != "") {
                    $mq_seconds = $d->mq_seconds;
                } 

                array_push($modelQuestions, ['QuestionCode' => $d->mq_ques_id, 
                                        'LanguageCode' => $languageCode, 
                                        'QuestionOrder' => $d->mq_order, 
                                        'Question' => $d->mq_question, 
                                        'QuestionPattern' => $d->mq_pattern,
                                        'QuestionSeconds' => $mq_seconds,
                                        'AnswerOne' => $d->mq_ans_1, 
                                        'AnswerTwo' => $d->mq_ans_2,  
                                        'AnswerThree' => $d->mq_ans_3, 
                                        'AnswerFour' => $d->mq_ans_4,
                                        'CorrectAnswer' => $d->mq_correct_ans,
                                        'Explanation' => $d->mq_explain
                                            ]);
                
                $questionStatus = true;
            }


            if($questionStatus)
            {
                return response()->json(['Status' => true, 'Message' => 'success', 'ModelExamCode' =>$modelCode, 
                                          'ModelExamDate' => $modelDateOnly." ".$modelTimeOnly,
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
      $examCode        = trim($request->modelExamCode);
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
          $modelResult = ModelExamResult::where('user_code', $userCode)->where('mq_code', $examCode)->where('ln_code', $languageCode)->first();

          $encryptedResult = base64_encode( serialize( $allAnswer ) );

          if(!$modelResult)
          {
              $transId = rand(10000,99999);

              $tempDatea = ['trans_id' => $transId, 'user_code' => $userCode, 
                            'mq_code' => $examCode, 'ln_code' => $languageCode,
                            'correct_answer' => $correctAnswer,  'wrong_answer' => $wrongAnswer, 
                            'not_answer' => $notAnswer, 'all_answer' => $encryptedResult, 'time_taken' => $timeTaken,
                            'start_time' => $tempDateeTime,
                            'created_at' => $tempDateeTime, 'updated_at' => $tempDateeTime
                            ];

                                       
              $queryStatus = ModelExamResult::insert($tempDatea);     

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
      $examCode      = trim($request->modelExamCode);
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
          $dailyResult = ModelExamOneResult::where('user_code', $userCode)->where('mq_code', $examCode)->where('mq_ques_id', $questionCode)->first();

          if(!$dailyResult)
          {
              array_push($tempDatea, ['trans_id' => rand(10000,99999), 'user_code' => $userCode, 'mq_code' => $examCode, 
                                'ln_code' => $languageCode, 'mq_ques_id' => $questionCode, 'user_answer' => $userAnswer, 
                                'user_time' => $userTime, 'user_status' => '1', 
                                'created_at' => $tempDateeTime, 'updated_at' => $tempDateeTime, 
                                'user_result' => $userResult]);


              $queryStatus = ModelExamOneResult::insert($tempDatea);     

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