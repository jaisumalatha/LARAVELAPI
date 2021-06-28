<?php

namespace App\Http\Controllers\Guzzleapi;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helper\Helper;


class ApiLeaderGuzzleController extends Controller
{    

    public function getNewUserLeaderBoard_guzzle(Request $request)
    {
        $authToken = 'YmxuMjlEZmxzVGJIQldYWUczdXJBTU81dXY1c3hl60d4bc75521c7';
        $headers = ['Content-Type' => 'application/json', 'Authorization' => 'Bearer ' . $authToken];
		$params['headers']       = $headers;
        $request =  Helper::GetApi('http://13.235.243.15/api/leader/boards',$params);
        echo $request;
    }
    public function getUserLeaderBoardDetails_guzzle(Request $request, $examType)
    {
        $authToken = 'YmxuMjlEZmxzVGJIQldYWUczdXJBTU81dXY1c3hl60d4bc75521c7';
        $headers = ['Content-Type' => 'application/json', 'Authorization' => 'Bearer ' . $authToken];
		$params['headers']       = $headers;
        $request =  Helper::GetApi('http://13.235.243.15/leader/board/'.$examType,$params);
        echo $request;
    }
    public function getUserHistory_guzzle(Request $request)
    {
        $authToken = 'YmxuMjlEZmxzVGJIQldYWUczdXJBTU81dXY1c3hl60d4bc75521c7';
        $headers = ['Content-Type' => 'application/json', 'Authorization' => 'Bearer ' . $authToken];
		$params['headers']       = $headers;
        $request =  Helper::GetApi('http://13.235.243.15/api/history/detail',$params);
        echo $request;
    }
    public function getUserHistoryDetails_guzzle(Request $request, $examType)
    {
        $authToken = 'YmxuMjlEZmxzVGJIQldYWUczdXJBTU81dXY1c3hl60d4bc75521c7';
        $headers = ['Content-Type' => 'application/json', 'Authorization' => 'Bearer ' . $authToken];
		$params['headers']       = $headers;
        $request =  Helper::GetApi('http://13.235.243.15/api/history/'.$examType,$params);
        echo $request;
    }
        
    public function getUserHistoryResult_guzzle(Request $request, $examType)
    {
        $authToken = 'YmxuMjlEZmxzVGJIQldYWUczdXJBTU81dXY1c3hl60d4bc75521c7';
        $URI='http://13.235.243.15/api/exam/answer/'.$examType;
        $examCode="DY56668";
        $planCode="PLN05";
        $paramsList = ['examCode'=>$examCode,'planCode'=>$planCode];
        $params[\GuzzleHttp\RequestOptions::JSON] 	 = $paramsList;
        $headers = ['Content-Type' => 'application/json', 'Authorization' => 'Bearer ' . $authToken];
        $params['headers']       = $headers;
        $responseCode = 0;
        $responseJSON = [];
        try
        {
            $response = Helper::PutApi($URI,$params);
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