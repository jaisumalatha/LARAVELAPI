<?php

namespace App\Http\Controllers\Guzzleapi;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helper\Helper;


class StateGuzzleController extends Controller
{
    //
  public function States_guzzle(Request $request)
	{
        $params=[];
        $request =  Helper::PostApi('http://13.235.243.15/api/states',$params);
        echo $request;

    }
}