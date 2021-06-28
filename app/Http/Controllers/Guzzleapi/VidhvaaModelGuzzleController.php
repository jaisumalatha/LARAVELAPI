<?php
namespace App\Http\Controllers\Guzzleapi;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helper\Helper;

class VidhvaaModelGuzzleController extends Controller
{
    //user registration function
    public function Questions_guzzle(Request $request)
	{
      $examCode       = trim($request->examCode);
      $languageCode   = trim($request->languageCode);
      $authStatus          = true;
      $questions           = [];
      $questionStatus      = false;
      $authToken = 'ZUpSR0lCSExtaG9jUUx6R2g5UjV6NlVhSmd5QWxi60cd80ea67786';
      $paramsList = array('examCode' => $examCode,'languageCode' => $languageCode,'authStatus' => $authStatus,'questions' => $questions ,'questionStatus' => $questionStatus);
      $headers = ['Content-Type' => 'application/json', 'Authorization' => 'Bearer ' . $authToken];
      $params[\GuzzleHttp\RequestOptions::JSON] 	 = $paramsList;
      $params['headers']       = $headers;
      $request =  Helper::PostFormApi('http://13.235.243.15/api/vidhvaa-model-question',$params);
      echo $request; 
  }


    public function user_otp_guzzle(Request $request)
    {
		$userOtp        = trim('5515');
        $firebaseToken  = "sdafasdfsafads";
		$authToken      = trim('T2RuUDNJTjVEZzlyd0hFU2JhZjRsdFlGbmNpQmVh60cda88f3e77e');
		$URI            = "http://13.235.243.15/api/user-registration-otp";
        $paramsList = ['userOtp' => $userOtp, 'firebaseToken' => $firebaseToken];
		$headers = ['Content-Type' => 'application/json', 'Authorization' => 'Bearer ' . $authToken];
		$params[\GuzzleHttp\RequestOptions::JSON] 	 = $paramsList;
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

    public function resend_otp_guzzle(Request $request)
    {
        $date_time = date("18-06-2021");
        $auth_token = 'ZUpSR0lCSExtaG9jUUx6R2g5UjV6NlVhSmd5QWxi60cd80ea67786';
        $system_otp = rand(1000,9999);
		$paramsList = array('date_time' => $date_time , 'auth_token' => $auth_token , 'system_otp' => $system_otp );
        $request =  Helper::PostFormApi('http://13.235.243.15/api/user-registration-resend-otp',$paramsList);
        echo $request; 
    }

    public function updateState_guzzle(Request $request)
	{
        $userState = 'AN';
        $authStatus  = true;
        $dateTime   = date("2021-06-18");
        $languages  = [];
        $authToken = 'ZUpSR0lCSExtaG9jUUx6R2g5UjV6NlVhSmd5QWxi60cd80ea67786';
		$paramsList = array('userState' => $userState,'authStatus' => $authStatus,'dateTime' => $dateTime,'languages' => $languages );
		$headers = ['Content-Type' => 'application/json', 'Authorization' => 'Bearer ' . $authToken];
		$params[\GuzzleHttp\RequestOptions::JSON] 	 = $paramsList;
		$params['headers']       = $headers;
        $request =  Helper::PostFormApi('http://13.235.243.15/api/user-state-update',$params);
        echo $request; 
    }

}