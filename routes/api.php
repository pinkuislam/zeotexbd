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
    Route::post('login-request', 'AuthController@loginRequest');
    Route::post('login', 'AuthController@login');

    Route::group(['middleware' => ['auth:sanctum']], function() {
        Route::get('user', 'AuthController@me');
        Route::post('user-fcm', 'AuthController@userFcmUpdate');
        Route::post('logout', 'AuthController@logout');
        Route::post('change-password', 'AuthController@changePassword');
        Route::get('dashboard', 'DashboardController@index')->name('dashboard');

        Route::group(['namespace' => 'Basic', 'prefix' => 'basic', 'as' => 'basic.'], function() {
            Route::get('units', 'BasicDataController@getUnit');
            Route::get('unit/{id}', 'BasicDataController@singleUnit');
            Route::get('colors', 'BasicDataController@getColor');
            Route::get('color/{id}', 'BasicDataController@singleColor');
            Route::get('products', 'BasicDataController@getProduct');
            Route::get('product/{id}', 'BasicDataController@singleProduct');
            Route::get('banks', 'BasicDataController@getBank');
            Route::get('bank/{id}', 'BasicDataController@singleBank');
            Route::get('shipping-methods', 'BasicDataController@getShippingMethod');
            Route::get('shipping-method/{id}', 'BasicDataController@singleShippingMethod');
        });
        Route::group(['namespace' => 'User', 'prefix' => 'user', 'as' => 'user.'], function () {
            Route::resource('customers', 'CustomerController');
            Route::put('customer/{id}/status', 'CustomerController@status')->name('customers.status');
            Route::get('customers/history/{mobile}','CustomerController@customerHistory')->name('customer.history');
            Route::post('customer/{id}/add', 'CustomerController@addToMyCustomer')->name('customers.add-to-my');
            Route::resource('delivery-agents', 'DeliveryAgentController');
            Route::put('delivery-agent/{id}/status', 'DeliveryAgentController@statusChange')->name('delivery-agents.status');
        });
        Route::group(['namespace' => 'Sale'], function() {
            Route::resource('orders/{order}/images', 'OrderImageController');
            Route::get('orders/{order}/snap', 'OrderController@snap')->name('orders.snap');
            Route::get('orders/get-monthly', 'OrderController@monthly')->name('orders.monthly');
            Route::get('my-ledger', 'OrderController@myLedger')->name('my.ledger');
            Route::resource('orders', 'OrderController');
            Route::resource('sales', 'SaleController');
        });
    });
});
