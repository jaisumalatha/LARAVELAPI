<?php

namespace App\Http\Controllers\Guzzleapi;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helper\Helper;

class LeaderBoardGuzzleController extends Controller
{
    //
  public function ExamDetails_guzzle(Request $request)
	{
        $authToken = 'cDdXWHd5bTR5cElaTlhKUFB6N045N0ZSY2VIZ1J060d22514bc61d';
        $URI='http://13.235.243.15/api/leader-board';
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


  public function Rank_guzzle(Request $request)
  {      
      
      $authToken = 'dWo5NEs0cURjYjlDUFR2SldzNk1ZeG1wUGp6NkFI60d222cab5f3f';
      $examCode      = "ME10104";
      $examType      = "MODEL_EXAM";
     
        $URI='http://13.235.243.15/api/leader-board-rank';
        $paramsList = ['examCode'=>$examCode,'examType'=>$examType];
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