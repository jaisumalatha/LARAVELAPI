<?php

namespace App\Http\Controllers\Guzzleapi;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helper\Helper;

class AudioGuzzleController extends Controller
{
    public function Audio_guzzle(Request $request)
	{
      $authToken = 'bFdwbHEwQXlGeVh3QnRra2N1RVJuUHkxcHJsNloz60d07cfdb4d2d';
      $examCategoryCode    = trim($request->examCategoryCode);
      $examSubCategoryCode = trim($request->examSubCategoryCode);
      $languageCode        = trim($request->languageCode);
      $audioCode           = trim($request->audioCode);

      $URI='http://13.235.243.15/api/course-chapter-audio';
      $paramsList = ['examCategoryCode' => $examCategoryCode,'examSubCategoryCode' => $examSubCategoryCode,'languageCode' => $languageCode,'audioCode' => $audioCode ];
      $params[\GuzzleHttp\RequestOptions::JSON] 	 = $paramsList;
      $headers = ['Content-Type' => 'application/json', 'Authorization' => 'Bearer ' . $authToken];
      $params['headers']       = $headers;
      $request = Helper::PostApi(
          $URI, 
          $params
      );
      return $request;
  }

  public function LanguageAudio_guzzle(Request $request)
  {
    $authToken = 'bFdwbHEwQXlGeVh3QnRra2N1RVJuUHkxcHJsNloz60d07cfdb4d2d';
    $languageCode        = trim($request->languageCode);
    $audioCode           = trim($request->audioCode);

    $URI='http://13.235.243.15/api/course-chapter-language-audio';
    $headers = ['Content-Type' => 'application/json', 'Authorization' => 'Bearer ' . $authToken];
    $params['headers']       = $headers;
    $paramsList = ['languageCode'=>$languageCode,'audioCode'=>$audioCode];
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

  
}