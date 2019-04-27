<?php

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

Route::get('/', [
	'uses' => 'TemperatureController@index',
	'as' => 'Temperature'
]);

Route::get('/orders', [
	'uses' => 'OrderController@index',
	'as' => 'Orders'
]);

Route::get('/order/{order_id}', [
	'uses' => 'OrderController@order',
	'as' => 'Order'
]);

Route::post('/order/save/{order_id}', [
	'uses' => 'OrderController@order_save',
	'as' => 'EditOrder'
]);

Route::get('/products', [
	'uses' => 'ProductsController@index',
	'as' => 'Products'
]);

Route::any('/product/ajax/set_price', [
	'uses' => 'ProductsController@set_price',
	'as' => 'EditOrder'
]);