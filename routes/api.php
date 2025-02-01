<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'v1'] , function () {
    Route::get('users/profile' , \App\Http\Controllers\Api\V1\UserController::class)->middleware('auth:sanctum');
    Route::get('dashboard-items' , \App\Http\Controllers\Api\V1\DashboardItemController::class);
    Route::get('banners' , \App\Http\Controllers\Api\V1\BannerController::class);
    Route::apiResource('request-plans' , \App\Http\Controllers\Api\V1\RequestPlanController::class)->middleware('auth:sanctum')->only(['index','show']);
    Route::controller(\App\Http\Controllers\Api\V1\RequestController::class)->middleware('auth:sanctum')->prefix('requests')->group(function () {
        Route::get('' , 'index');
        Route::get('{request}' , 'show');
        Route::post('' , 'create');
        Route::post('{request}/confirm' , 'confirm');
        Route::patch('{request}' , 'update');
    });
    Route::controller(\App\Http\Controllers\Api\V1\ReportController::class)->middleware('auth:sanctum')->prefix('reports')->group(function () {
        Route::get('' , 'index');
        Route::get('{report}' , 'show');
        Route::post('{request}' , 'create');
        Route::post('{report}/confirm' , 'confirm');
        Route::patch('{report}' , 'update');
    });
    Route::controller(\App\Http\Controllers\Api\V1\WrittenController::class)->middleware('auth:sanctum')->prefix('written-requests')->group(function () {
        Route::get('' , 'index');
        Route::get('{id}' , 'show');
        Route::post('' , 'store');
        Route::patch('{id}' , 'update');
    });
});
