<?php
namespace App\Http\Controllers\Guzzleapi;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helper\Helper;

class MpinGuzzleController extends Controller
{
    //
    public function sendOTP_guzzle(Request $request)
    {
        $authToken = 'MWdaNUp6OExsbmtnRGluNlZFS3AzTU83MUJQaVhj60d2f67e044e1';
        $headers = ['Content-Type' => 'application/json', 'Authorization' => 'Bearer ' . $authToken];
		$params['headers']       = $headers;
        $request =  Helper::GetApi('http://13.235.243.15/api/send/mpin/otp-guzzle',$params);
        echo $request;
    }

    public function checkOTP_guzzle(Request $request)
    {
        $authToken = 'MWdaNUp6OExsbmtnRGluNlZFS3AzTU83MUJQaVhj60d2f67e044e1';
        $userOtp='9181';
        $URI='http://13.235.243.15/api/check/mpin/otp';
        $paramsList = ['userOtp'=>$userOtp];
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

    public function changeUserMpin_guzzle(Request $request)
    {
        $authToken = 'MWdaNUp6OExsbmtnRGluNlZFS3AzTU83MUJQaVhj60d2f67e044e1';
        $URI='http://13.235.243.15/api/change/mpin';
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

    public function changeUserForgetMpin_guzzle(Request $request)
    {
        $authToken = 'MWdaNUp6OExsbmtnRGluNlZFS3AzTU83MUJQaVhj60d2f67e044e1';
        $URI='http://13.235.243.15/api/change-mpin';
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
      print_r($responseJSON);}
}