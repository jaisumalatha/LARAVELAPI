<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GuzzlePostController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('articles', 'ArticleController@index');
Route::get('articles/{id}', 'ArticleController@show');
Route::post('articles', 'ArticleController@store');
Route::put('articles/{id}', 'ArticleController@update');
Route::delete('articles/{id}', 'ArticleController@delete');

Route::post('studentsadd', 'App\Http\Controllers\ApiController@createStudent');
Route::get('students', 'App\Http\Controllers\ApiController@getAllStudents');
Route::get('students/{id}', 'App\Http\Controllers\ApiController@getStudent');
Route::put('studentsupdate/{id}', 'App\Http\Controllers\ApiController@updateStudent');
Route::delete('studentsdelete/{id}','App\Http\Controllers\ApiController@deleteStudent');

Route::post('formvalue', 'App\Http\Controllers\ApiController@getGuzzleRequest');

Route::post('store', 'App\Http\Controllers\GuzzlePostController@store');
Route::get('index', 'App\Http\Controllers\GuzzlePostController@index');