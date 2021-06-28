<?php

namespace App\Http\Controllers\Guzzleapi;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helper\Helper;


class DashboardGuzzleController extends Controller
{    
    public function DashboardItems_guzzle(Request $request)
	{   
        $authToken = 'eVNiZ2dpZXhJTlhtUW5qa2twTzF3alhUOGp4QUlB60d1f3810b831';
        $URI='http://13.235.243.15/api/dashboard';
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

    public function getShareContent(Request $request)
    {   
        $authToken = $request->bearerToken();
        if(empty($authToken))
        {
            return response()->json(['status' => false, 'Message' => 'Authentication Failure'], 401);
        }

        $userDetail = UserDetail::where('auth_token', $authToken)->first();

        if(empty($userDetail))
        {
            return response()->json(['status' => false, 'Message' => 'Authentication Failure'], 401);
        }

        $data = array();

        $data['shareContentAndroid'] = "Hi Aspirant,
                                            Download  VIDHVAA mobile app today and you would be happy later that you made the right decision towards achieving your career goals.
                                            Download VIDHVAA app & Stay ahead of the race
                                            https://play.google.com/store/apps/details?id=com.nbmedu.liveexam
                                            Install the app and take a free preview of its content and tools provided to triumph over the competitive exams, we value your reviews to build a platform you enjoy using";
                                            
        $data['shareContentIos']     = "Hi Aspirant,
                                            Download  VIDHVAA mobile app today and you would be happy later that you made the right decision towards achieving your career goals.
                                            Download VIDHVAA app & Stay ahead of the race
                                            https://play.google.com/store/apps/details?id=com.nbmedu.liveexam
                                            Install the app and take a free preview of its content and tools provided to triumph over the competitive exams, we value your reviews to build a platform you enjoy using";



        // {"status":true,"message":"success","data":{"shareContentAndroid":"Share this application.","shareContentIos":"Share this application."}}
        
        return response()->json(['Status' => true, 'Message' => 'success', 'data' => $data], 200);        
    }
}