<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::group(['prefix' => 'auth'], function(){
    Route::post('login', 'App\Http\Controllers\AuthController@login');
    Route::post('logout', 'App\Http\Controllers\AuthController@logout')->middleware('user.verify');
    Route::post('profile', 'App\Http\Controllers\AuthController@profile')->middleware('user.verify');
});

Route::group(['prefix' => 'chat'], function(){
    Route::post('history', 'App\Http\Controllers\ChatController@history')->middleware('user.verify');
    Route::post('send', 'App\Http\Controllers\ChatController@send')->middleware('user.verify');
});

Route::group(['prefix' => 'customer'], function(){
    Route::post('get', 'App\Http\Controllers\CustomerController@get')->middleware('user.verify');
    Route::post('delete', 'App\Http\Controllers\CustomerController@delete')->middleware('user.verify');
});

Route::group(['prefix' => 'report'], function(){
    Route::post('send', 'App\Http\Controllers\ReportController@send')->middleware('user.verify');
});

Route::group(['prefix' => 'feedback'], function(){
    Route::post('send', 'App\Http\Controllers\ReportController@feedback')->middleware('user.verify');
});