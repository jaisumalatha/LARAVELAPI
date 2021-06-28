<?php

namespace App\Http\Controllers\Guzzleapi;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helper\Helper;


class ApiUserGuzzleController extends Controller
{    

    public function uploadUserProfileImage_guzzle(Request $request)
    {
      $authToken = 'YmxuMjlEZmxzVGJIQldYWUczdXJBTU81dXY1c3hl60d4bc75521c7';
      $URI='http://13.235.243.15/api/image/upload';
      $paramsList = [];
      $params[\GuzzleHttp\RequestOptions::JSON] 	 = $paramsList;
      $headers = ['Content-Type' => 'application/json', 'Authorization' => 'Bearer ' . $authToken];
      $params['headers']       = $headers;
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
        
    public function updateUserFCM_guzzle(Request $request)
    {
        $authToken = 'YmxuMjlEZmxzVGJIQldYWUczdXJBTU81dXY1c3hl60d4bc75521c7';
        $URI='http://13.235.243.15/api/fcm/upload';
        $fcmToken = trim('sdafasdfsafads');
        $paramsList = ['firebaseToken'=>$fcmToken];
        $params[\GuzzleHttp\RequestOptions::JSON] 	 = $paramsList;
        $headers = ['Content-Type' => 'application/json', 'Authorization' => 'Bearer ' . $authToken];
        $params['headers']       = $headers;
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