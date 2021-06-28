<?php

namespace App\Http\Controllers\Guzzleapi;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helper\Helper;


class CourseChapterGuzzleController extends Controller
{
    //
  public function Chapters_guzzle(Request $request)
	{
      $authToken = 'dHR3UFVOQkZ5aEJhOTZ5MUxFdEM3M3dDTHdFVVZl60d1b71f70ec3';
      $examCategoryCode    = trim('EXCT01');
      $examSubCategoryCode = trim('EX04');
      $subjectCode         = trim('SB03');
      $languageCode        = trim('LN02');

      $URI='http://13.235.243.15/api/course-chapter';
      $paramsList = ['examCategoryCode'=>$examCategoryCode,'examSubCategoryCode'=>$examSubCategoryCode,'subjectCode'=>$subjectCode,'languageCode'=>$languageCode];
      $params[\GuzzleHttp\RequestOptions::JSON] 	 = $paramsList;
      $headers = ['Content-Type' => 'application/json', 'Authorization' => 'Bearer ' . $authToken];
      $params['headers']       = $headers;
      $response = Helper::PostApi($URI,  $paramsList);
      
    echo $response;

  }

  public function LanguageChapters_guzzle(Request $request)
  {
    $authToken = 'dHR3UFVOQkZ5aEJhOTZ5MUxFdEM3M3dDTHdFVVZl60d1b71f70ec3';
    $examCategoryCode    = trim('EXCT01');
  $examSubCategoryCode = trim('EX04');
  $subjectCode         = trim('SB08');
  $languageCode        = trim('LN02');
  $languagePart        = trim('C');

  $URI='http://13.235.243.15/api/course-language-chapter';
  $paramsList = ['examCategoryCode'=>$examCategoryCode,'examSubCategoryCode'=>$examSubCategoryCode,'languagePart'=>$languagePart,'subjectCode'=>$subjectCode,'languageCode'=>$languageCode];
  $params[\GuzzleHttp\RequestOptions::JSON] 	 = $paramsList;
  $headers = ['Content-Type' => 'application/json', 'Authorization' => 'Bearer ' . $authToken];
  $params['headers']       = $headers;
  $responseCode = 0;
  $responseJSON = [];
try
{
  $response = Helper::PostApi($URI,  $params);
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
}