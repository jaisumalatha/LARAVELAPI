<?php

namespace App\Http\Controllers\Guzzleapi;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helper\Helper;

class PractiseQuestionGuzzleController extends Controller
{
    //
  public function getGeneralQuestions_guzzle(Request $request)
	{
        $authToken = 'TkhKRzFNaW41WWNETUs5amhKaTVpcG5xQW15VzdK60d098c5a4587';
        $examCategoryCode    = trim('EXCT01');
        $examSubCategoryCode = trim('EX04');
        $subjectCode         = trim('SB03');
        $languageCode        = trim('LN01');

      $URI='http://13.235.243.15/api/practise-general-questions';
      $headers = ['Content-Type' => 'application/json', 'Authorization' => 'Bearer ' . $authToken];
      $params['headers']       = $headers;
      $paramsList = ['languageCode'=>$languageCode,'examCategoryCode'=>$examCategoryCode,'examSubCategoryCode'=>$examSubCategoryCode,'subjectCode'=>$subjectCode];
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

  public function getLanguageQuestions_guzzle(Request $request)
  {
    $authToken = 'TkhKRzFNaW41WWNETUs5amhKaTVpcG5xQW15VzdK60d098c5a4587';
    $examCategoryCode    = trim('EXCT01');
    $examSubCategoryCode = trim('EX04');
    $subjectCode         = trim('SB03');
    $languageCode        = trim('LN01');

  $URI='http://13.235.243.15/api/practise-language-questions';
  $headers = ['Content-Type' => 'application/json', 'Authorization' => 'Bearer ' . $authToken];
  $params['headers']       = $headers;
  $paramsList = ['languageCode'=>$languageCode,'examCategoryCode'=>$examCategoryCode,'examSubCategoryCode'=>$examSubCategoryCode,'subjectCode'=>$subjectCode];
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

  public function getCurrentQuestions_guzzle(Request $request)
  {
    $authToken = 'TkhKRzFNaW41WWNETUs5amhKaTVpcG5xQW15VzdK60d098c5a4587';
    $currentCode   = trim('LN01');
      $languageCode  = trim('CA15503');

    $URI='http://13.235.243.15/api/practise-current-questions';
    $headers = ['Content-Type' => 'application/json', 'Authorization' => 'Bearer ' . $authToken];
    $params['headers']       = $headers;
    $paramsList = ['languageCode'=>$languageCode,'currentCode'=>$currentCode];
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