<?php

use Illuminate\Http\Request;

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
/**
 * this is just a demo route to check application is running or not
 */
Route::get('ping', function() {
    return 'working....';
});
Route::post('user/request/updatepassword', 'UserController@requestUpdatepassword');
Route::post('reset-password/user/{usertype}/{token}', 'UserController@passwordUpdate');
Route::post('/login', 'UserController@postLogin');
