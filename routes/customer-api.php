<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for customer. These
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

Route::post('create', 'CustomerController@store');
Route::post('login', 'CustomerController@login');

Route::group(['middleware' => ['jwt.auth']], function () {
	Route::get('fetch/personalinfo', 'CustomerController@getPersonalinfo');
	Route::post('update/personalinfo', 'CustomerController@savePersonalInfo');
    Route::get('fetch/demographyinfo', 'CustomerController@getDemographicinfo');
	Route::post('update/demographyinfo', 'CustomerController@saveDemographicinfo');
	Route::get('fetch/professionalinfo', 'CustomerController@getProfessionalinfo');
	Route::post('update/professionalinfo', 'CustomerController@saveProfessionalinfo');
	Route::get('fetch/paymentinfo', 'CustomerController@getPaymentinfo');
	Route::post('update/paymentinfo', 'CustomerController@savePaymentinfo');
	Route::post('save/propertyinfo', 'CustomerController@createProperty');
	Route::post('update/propertyinfo', 'CustomerController@updateProperty');
	Route::post('delete/propertyinfo', 'CustomerController@deleteProperty');
	Route::post('fetch/allproperty', 'CustomerController@getallProperty');
	Route::post('fetch/propertyinfo', 'CustomerController@getPropertyinfo');
	Route::post('update/accountsettings', 'CustomerController@saveAccountsettings');
	Route::post('fetch/accountsettings', 'CustomerController@getAccountsettings');
});
