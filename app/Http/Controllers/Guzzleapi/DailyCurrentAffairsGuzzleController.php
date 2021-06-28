<?php

namespace App\Http\Controllers\Guzzleapi;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helper\Helper;


class DailyCurrentAffairsGuzzleController extends Controller
{
    //
  public function getDates_guzzle(Request $request)
	{
      $authToken = 'dWRIZlA4ZzFwNEd2eUdIMlllWm5Sc2dmdVFwSXB160d1a97165e99';
      $headers = ['Content-Type' => 'application/json', 'Authorization' => 'Bearer ' . $authToken];
      $params['headers']       = $headers;
      $request =  Helper::GetApi('http://13.235.243.15/api/daily-current-affairs-dates',$params);
      echo $request;

  }


  public function getCurrentAffairs_guzzle(Request $request)
  {
    $authToken = 'dWRIZlA4ZzFwNEd2eUdIMlllWm5Sc2dmdVFwSXB160d1a97165e99';
    $dailyDate    = trim(22-06-2021);
    $headers = ['Content-Type' => 'application/json', 'Authorization' => 'Bearer ' . $authToken];
    $params['headers']       = $headers;
    $params=['dailyDate'=> $dailyDate];
    $request =  Helper::GetApi('http://13.235.243.15/api/daily-current-affairs-material',$params);
    echo $request;
  }
}