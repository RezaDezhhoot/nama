<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'v1'] , function () {
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
});
