<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GuzzlePostController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

//Route::middleware(['auth:sanctum', 'verified'])->get('/dashboard', function () {
//    return view('dashboard');
//})->name('dashboard');
//Route::get('dashboard','UserquesViewController@index');
//Route::get('dashboard', [UserquesViewController::class, 'index']);
Route::get('/dashboard', 'App\Http\Controllers\UserquesViewController@index');
//Route::get('dashboard','UserquesViewController@index');
Route::post('store-form', 'App\Http\Controllers\UserquesViewController@store');
Route::get('post','App\Http\Controllers\DataController@postRequest');
Route::get('get','App\Http\Controllers\DataController@getRequest');