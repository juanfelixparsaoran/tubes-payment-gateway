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

Route::get('/account/read','AccountController@read');
Route::get('/account/edit/{id}','AccountController@edit');
Route::get('/account/delete/{id}','AccountController@delete');
Route::post('/account/update','AccountController@update');
Route::post('/account/register','AccountController@create');
Route::post('/account/auth','AccountController@auth');

Route::get('/history/read','HistoryController@read');
Route::get('/history/edit/{id}','HistoryController@edit');
Route::get('/history/delete/{id}','HistoryController@delete');
Route::post('/history/update','HistoryController@update');
Route::post('/history/create','HistoryController@create');

Route::post('/pay','PayController@pay');
Route::post('/pay/banktransfer','PayController@payVirtualNumber');

Route::post('/cc/create','CreditCardController@create');

Route::post('/payreq/read','PayControllerr@read');

