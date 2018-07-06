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

/*
|--------------------------------------------------------------------------
| Login API
|--------------------------------------------------------------------------
|
*/
Route::post('register', 'RegisterController@registerNewUser');
Route::post('login', 'LoginController@checkLogin');

/*
|--------------------------------------------------------------------------
| Expense Manager API
|--------------------------------------------------------------------------
|
*/
Route::group(['middleware' => ['api']], function () {
    Route::post('add', 'ExpenseController@addExpense');
});

/*
|--------------------------------------------------------------------------
| Test API
|--------------------------------------------------------------------------
|
*/
Route::post('test', 'RegisterController@test');
