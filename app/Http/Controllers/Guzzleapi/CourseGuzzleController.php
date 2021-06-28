<?php

namespace App\Http\Controllers\Guzzleapi;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helper\Helper;

class CourseGuzzleController extends Controller
{
       public function courseItemsNewJune(Request $request)
    {
        $authToken = 'dm1wQ3NjUVJZZVVaU1k5cE1RTWkxT1owVkd5a2FR60d1fdef88237';
        $examCategoryCode    = trim('EXCT01');
        $examSubCategoryCode = trim('EX04');

        $URI='http://13.235.243.15/api/course-guzzle';
        $paramsList = ['examCategoryCode'=>$examCategoryCode,'examSubCategoryCode'=>$examSubCategoryCode];
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