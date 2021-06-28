<?php

namespace App\Http\Controllers\Guzzleapi;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helper\Helper;

class DailyTaskGuzzleController extends Controller
{
    //
    public function TaskCode_guzzle(Request $request)
	{
      $authToken = 'UlJuMEkzaEtQdm00VExxS2VSZUtVaFJPampjdHNP60d0759563c90';
      $URI='http://13.235.243.15/api/daily-task-code-guzzle';
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


  public function StreamUrl_guzzle(Request $request)
  {
      $languageCode = trim('LN01');
      $authToken = 'UlJuMEkzaEtQdm00VExxS2VSZUtVaFJPampjdHNP60d0759563c90';
      $URI='http://13.235.243.15/api/online-exam-stream';
      $headers = ['Content-Type' => 'application/json', 'Authorization' => 'Bearer ' . $authToken];
      $params['headers']       = $headers;
      $paramsList = ['languageCode'=>$languageCode];
      $params[\GuzzleHttp\RequestOptions::JSON] 	 = $paramsList;
      $request = Helper::PostApi(
          $URI, 
          $params
      );
      return $request;
  }


  public function TermsAndConditions_guzzle(Request $request)
  {
    $languageCode = trim('LN01');
    $authToken = 'bFdwbHEwQXlGeVh3QnRra2N1RVJuUHkxcHJsNloz60d07cfdb4d2d';
    $URI='http://13.235.243.15/api/online-exam-terms';
    $headers = ['Content-Type' => 'application/json', 'Authorization' => 'Bearer ' . $authToken];
    $params['headers']       = $headers;
    $paramsList = ['languageCode'=>$languageCode];
    $params[\GuzzleHttp\RequestOptions::JSON] 	 = $paramsList;
    $responseCode = 0;
    $responseJSON = [];
    try
    {
        $response = Helper::PostApi($URI,$params);
        if($response->getStatusCode() == 200)
        {
            $responseCode = $response->getStatusCode();
            $responseJSON = json_decode($response->getBody(), true);
        }
    }
    catch(\GuzzleHttp\Exception\GuzzleException $exception)
    {
        print_r($exception);
    }
    print_r($responseJSON);
  }

  public function TaskQuestions_guzzle(Request $request)
  {
    $languageCode = trim('LN01');
    $authToken = 'bFdwbHEwQXlGeVh3QnRra2N1RVJuUHkxcHJsNloz60d07cfdb4d2d';
    $URI='http://13.235.243.15/api/online-exam-questions';
    $headers = ['Content-Type' => 'application/json', 'Authorization' => 'Bearer ' . $authToken];
    $params['headers']       = $headers;
    $paramsList = ['languageCode'=>$languageCode];
    $params[\GuzzleHttp\RequestOptions::JSON] 	 = $paramsList;
    $responseCode = 0;
    $responseJSON = [];
    try
    {
        $response = Helper::PostApi($URI,$params);
        if($response->getStatusCode() == 200)
        {
            $responseCode = $response->getStatusCode();
            $responseJSON = json_decode($response->getBody(), true);
        }
    }
    catch(\GuzzleHttp\Exception\GuzzleException $exception)
    {
        print_r($exception);
    }
    print_r($responseJSON);

  }

  public function storeResult_guzzle(Request $request)
  {
      $authToken = 'bFdwbHEwQXlGeVh3QnRra2N1RVJuUHkxcHJsNloz60d07cfdb4d2d';
      $languageCode     = trim('LN01');
      $correctAnswer       = trim('12');
      $wrongAnswer       = trim('10');
      $notAnswer        = trim('5');
      $timeTaken    = trim('12002');
      $allAnswer       = 'skdfjklsdjfs';
      $modelExamCode        = trim('ON12081');
      $URI='http://13.235.243.15/api/online-exam-result';
      $paramsList = ['languageCode' => $languageCode,'correctAnswer' => $correctAnswer,'wrongAnswer' => $wrongAnswer,'notAnswer' => $notAnswer,'timeTaken' => $timeTaken,'modelExamCode' => $modelExamCode,'allAnswer' => $allAnswer ];
      $params[\GuzzleHttp\RequestOptions::JSON] 	 = $paramsList;
      $headers = ['Content-Type' => 'application/json', 'Authorization' => 'Bearer ' . $authToken];
      $params['headers']       = $headers;
      $request = Helper::PostApi(
          $URI, 
          $params
      );
      return $request;
  }


  public function storeIndividualResult_guzzle(Request $request)
  {
      
      $authToken = 'bFdwbHEwQXlGeVh3QnRra2N1RVJuUHkxcHJsNloz60d07cfdb4d2d';
      $examCode   = trim('ON12081');
      $languageCode     = trim('LN01');
      $questionCode       = trim('12');
      $userAnswer       = trim('10');
      $userResult        = trim('5');
      $userTime    = trim('12002');
      $URI='http://13.235.243.15/api/online-exam-individual-result';
      $paramsList = ['examCode' => $examCode,'languageCode' => $languageCode,'questionCode' => $questionCode,'userAnswer' => $userAnswer,'userResult' => $userResult,'userTime' => $userTime ];
      $params[\GuzzleHttp\RequestOptions::JSON] 	 = $paramsList;
      $headers = ['Content-Type' => 'application/json', 'Authorization' => 'Bearer ' . $authToken];
      $params['headers']       = $headers;
      $request = Helper::PostApi($URI,  $params);
      return $request;
  
  }
    
}