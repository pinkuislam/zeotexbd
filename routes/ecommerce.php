<?php


use App\Http\Middleware\HandleInertiaEcommerceRequests;
use Illuminate\Support\Facades\Route;

Route::group(['namespace' => 'App\Http\Controllers\Web', 'as' => 'ecommerce.', 'middleware' => [HandleInertiaEcommerceRequests::class]], function () {
    Route::get('/', 'HomeController')->name('home');
    Route::get('pages/{slug}', 'PageController@index')->name('page');
    Route::post('products/review/store/{id}', 'ProductController@review')->name('products.review');
    Route::post('products/image-destroy', 'ProductController@destroyImage')->name('products.image-destroy');
    Route::resource('products', 'ProductController');
    Route::resource('cart', 'CartController');
    Route::resource('checkout', 'CheckoutController');
    Route::resource('order', 'OrderController');
    Route::resource('tracking', 'TrackingController');

    Route::get('checkout-complete', 'CheckoutController@completeOrder')->name('checkout.complete');

    Route::get('search', 'SearchController@index')->name('search');
    Route::get('category/{slug}', 'CategoryController')->name('category');

    Route::get('variants', 'ProductController@variantProduct')->name('product.variant');
    Route::get('filter', 'CategoryController@filterProducts')->name('category.filter');
});
