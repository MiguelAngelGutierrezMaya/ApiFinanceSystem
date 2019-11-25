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

/**
 * Auth
 */
Route::post('login', 'API_Auth\LoginController@login');
Route::post('logout', 'API\UserController@logout');
Route::post('forgot', 'API_Auth\ForgotPasswordController@sendResetLinkEmail');
Route::post('reset/showForm', 'API_Auth\ResetPasswordController@showResetForm');
Route::post('reset', 'API_Auth\ResetPasswordController@reset');

/**
 * User
 */
Route::get('user/getCollectors', 'API\UserController@getCollectors');
Route::get('user/clearCache', 'API\UserController@cacheClear');

/**
 * Financings
 */
Route::get('financings', 'API\FinancingController@index');
Route::get('financings/pending', 'API\FinancingController@getPendingFinancings');
Route::get('financing/getFinancingById', 'API\FinancingController@getFinancingById');
Route::post('financing/refinance', 'API\FinancingController@refinance');
Route::post('financing/postpone', 'API\FinancingController@postpone');

/**
 * Payments
 */
Route::get('payments', 'API\PaymentController@index');
Route::get('payments/getPaymentById', 'API\PaymentController@getPaymentById');
Route::post('payment/register', 'API\PaymentController@store');

/**
 * Financing Aditional Chargue
 */
Route::get('financingAditionalChargue', 'API\FinancingAditionalChargueController@index');
Route::post('financingAditionalChargue/register', 'API\FinancingAditionalChargueController@store');
