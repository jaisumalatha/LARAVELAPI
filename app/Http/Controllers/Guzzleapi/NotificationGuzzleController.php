<?php

namespace App\Http\Controllers\Guzzleapi;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helper\Helper;

class NotificationGuzzleController extends Controller
{
    public function Notification_guzzle(Request $request)
    {
        $authToken = 'eVNiZ2dpZXhJTlhtUW5qa2twTzF3alhUOGp4QUlB60d1f3810b831';
        $URI='http://13.235.243.15/api/user-notification';
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