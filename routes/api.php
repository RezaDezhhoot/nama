<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'v1'] , function () {
    Route::get('users/profile' , \App\Http\Controllers\Api\V1\UserController::class)->middleware(['auth:sanctum']);
    Route::get('info' , \App\Http\Controllers\Api\V1\InfoController::class)->middleware(['auth:sanctum','has_item']);
    Route::apiResource('dashboard-items' , \App\Http\Controllers\Api\V1\DashboardItemController::class)->only(['index','show']);
    Route::get('banners' , \App\Http\Controllers\Api\V1\BannerController::class);
    Route::apiResource('request-plans' , \App\Http\Controllers\Api\V1\RequestPlanController::class)->middleware(['auth:sanctum','has_item'])->only(['index','show']);
    Route::controller(\App\Http\Controllers\Api\V1\RequestController::class)->middleware(['auth:sanctum','has_item'])->prefix('requests')->group(function () {
        Route::get('' , 'index');
        Route::get('{request}' , 'show');
        Route::post('' , 'create');
        Route::post('{request}/confirm' , 'confirm');
        Route::post('{request}/admin-submit' , 'adminStore');
        Route::patch('{request}' , 'update');
    });
    Route::controller(\App\Http\Controllers\Api\V1\ReportController::class)->middleware(['auth:sanctum','has_item'])->prefix('reports')->group(function () {
        Route::get('' , 'index');
        Route::get('{report}' , 'show');
        Route::post('{request}' , 'create');
        Route::post('{report}/confirm' , 'confirm');
        Route::patch('{report}' , 'update');
    });
    Route::controller(\App\Http\Controllers\Api\V1\WrittenController::class)->middleware(['auth:sanctum','has_item'])->prefix('written-requests')->group(function () {
        Route::get('' , 'index');
        Route::get('{id}' , 'show');
        Route::post('' , 'store');
        Route::patch('{id}' , 'update');
    });
});
