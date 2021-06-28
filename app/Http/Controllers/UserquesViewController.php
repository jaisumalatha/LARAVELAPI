<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\user_answere;
use GuzzleHttp\Client;

class UserquesViewController extends Controller {
   public function index() {
      $users = DB::table('user_question')->limit(3)->get();
      return view('userques_view',['users'=>$users]);
   }
   public function store(Request $data)
{
    $ins = $data->all();
    unset($ins['_token']);
    $store = DB::table("user_answeres")->insert([$ins]);
    //return Redirect('Users/home')->with('message',"success");
}


  
}
