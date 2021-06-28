<?php

namespace App\Http\Controllers\Guzzleapi;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Libraries\GetMobileCommon;
use App\Models\UserDetail;
use App\Models\MotivationalVideo;


class MotivationalVideosGuzzleController extends Controller
{
    //

    public function getVidhvaaVideos(Request $request)
    {
        $authToken = $request->bearerToken();
        $client = new \GuzzleHttp\Client();
        
        // Create a request
        $request = $client->get('http://13.235.243.15/api/video/vidhvaa');
        // Get the actual response without headers
        $response = $request->getBody();
        return $response;
    }
}