<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;



class DataController extends Controller
{
    public function postRequest()
    {
        $client = new \GuzzleHttp\Client();
        $response = $client->request('POST', 'http://localhost/laravelauth/public/api/studentsadd', [
            'form_params' => [
                'name' => 'krunalkkh[',
                'course'=>'dfasdf',
            ]
        ]);
        $response = $response->getBody()->getContents();
        echo '<pre>';
        print_r($response);
    }
    public function getRequest()
    {
        $client = new \GuzzleHttp\Client();
        $request = $client->get('http://localhost/laravelauth/public/api/students');
        $response = $request->getBody()->getContents();
        echo '<pre>';
        print_r($response);
        exit;
    }

}
