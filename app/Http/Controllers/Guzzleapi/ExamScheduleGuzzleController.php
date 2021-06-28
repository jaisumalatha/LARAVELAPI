<?php

namespace App\Http\Controllers\Guzzleapi;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helper\Helper;


class ExamScheduleGuzzleController extends Controller
{
    //
  public function Schedules_guzzle(Request $request)
	{
        $authToken = 'M3VpeVUwZ0ZQQjR0bW1sWnJ3ZEE3YThpQXlJdTRH60d1c93fcfc31';
        $URI='http://13.235.243.15/api/daily-current-affairs-dates';
        $paramsList = [];
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