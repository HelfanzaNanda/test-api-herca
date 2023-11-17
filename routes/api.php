<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function() {
    Route::post('login', 'AuthController@login');
});

Route::middleware('auth:api')->group(function() {
    Route::get('logout', 'AuthController@logout');
    Route::prefix('sales')->controller('SaleController')->group(function(){
        Route::get('datatables', 'datatables');
        Route::get('credit/{sale_id}', 'credit');
    });
    Route::prefix('users')->controller('UserController')->group(function(){
        Route::get('datatables', 'datatables');
    });
    Route::prefix('credit')->controller('CreditController')->group(function(){
        Route::get('datatables', 'datatables');
    });
    Route::prefix('payment')->controller('PaymentController')->group(function(){
        Route::post('snap/generate', 'snapGenerate');
        Route::post('pay', 'pay');
    });
    Route::prefix('payment/detail')->controller('PaymentDetailController')->group(function(){
        Route::post('snap/generate', 'snapGenerate');
        Route::post('pay', 'pay');
        Route::get('datatables', 'datatables');

    });
    Route::prefix('revenue')->controller('RevenueController')->group(function(){
        Route::get('datatables', 'datatables');
    });
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
