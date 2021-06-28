<?php

namespace App\Http\Controllers\Guzzleapi;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Helper\Helper;


class PaymentAuthGuzzleController extends Controller
{
    //
    public function changeToken_guzzle(Request $request)
    {
        $authToken = 'bFdwbHEwQXlGeVh3QnRra2N1RVJuUHkxcHJsNloz60d07cfdb4d2d';    
        $URI='http://13.235.243.15/api/change-auth-token';
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

    public function getPaymentDetails_guzzle(Request $request)
    {
        $authToken = 'bFdwbHEwQXlGeVh3QnRra2N1RVJuUHkxcHJsNloz60d07cfdb4d2d';    
        $URI='http://13.235.243.15/api/get-payment-details-guzzle';
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
}