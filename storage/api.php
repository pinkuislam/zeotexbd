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


Route::group(['namespace' => 'App\Http\Controllers\Api'], function () {
    Route::get('/', function () { echo 'worked!'; });
    Route::post('login', 'AuthController@login');

    Route::group(['middleware' => ['auth:sanctum']], function() {
        Route::get('user', 'AuthController@me');
        Route::post('logout', 'AuthController@logout');
        Route::get('customer', 'MainController@customer');
        Route::get('product', 'MainController@product');
        Route::get('trade-offer', 'MainController@tradeOffer');
        Route::get('unit', 'MainController@unit');
        Route::get('promotional-item', 'MainController@promotionalItems');
        Route::get('question', 'MainController@question');
        Route::get('question-ans/{id}', 'MainController@questionAns');
        Route::get('dealer', 'MainController@dealer');
        Route::get('district', 'MainController@district');
        Route::get('thana', 'MainController@thana');
        Route::get('zone', 'MainController@zone');
        Route::post('customer', 'MainController@customerStore');
        Route::post('dealer', 'MainController@dealerStore');
        Route::get('field_force/dealers', 'MainController@getDealers');
        Route::get('field_force/zones', 'MainController@getZones');
        Route::post('field_force/store_customer', 'MainController@saveCustomer');
    });
});
