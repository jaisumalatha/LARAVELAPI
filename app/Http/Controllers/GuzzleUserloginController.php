<?php

namespace App\Http\Controllers\Login;
use App\Http\Controllers\Controller;

use App\Models\UserDetail;
use App\Models\UserPaymentDetail;
use App\Models\UserPersonalDetail;
use App\Models\UserAuthenticationDetail;
use App\Models\UserPlanDetail;
use App\Models\PlanDetail;

use Illuminate\Http\Request;
use GuzzleHttp\Client;

use Razorpay\Api\Api;
use App\Libraries\SendSms;

use DB;

class GuzzleUserloginController extends Controller
{

    public function getRequest()
    {
        $client = new \GuzzleHttp\Client();
        $request = $client->get('http://13.235.243.15/api/user-registration');
        $response = $request->getBody()->getContents();
        echo '<pre>';
        print_r($response);
        exit;
    }

   
}