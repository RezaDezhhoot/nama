<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'v1'] , function () {
    Route::get('users/profile' , \App\Http\Controllers\Api\V1\UserController::class)->middleware(['auth:sanctum']);
    Route::get('info' , \App\Http\Controllers\Api\V1\InfoController::class)->middleware(['auth:sanctum','has_item','has_role']);
    Route::get('units' , \App\Http\Controllers\Api\V1\UnitController::class)->middleware(['auth:sanctum','has_item','has_role']);
    Route::apiResource('dashboard-items' , \App\Http\Controllers\Api\V1\DashboardItemController::class)->only(['index','show']);
    Route::get('banners' , \App\Http\Controllers\Api\V1\BannerController::class)->middleware(['has_item']);
    Route::apiResource('request-plans' , \App\Http\Controllers\Api\V1\RequestPlanController::class)->middleware(['auth:sanctum','has_item'])->only(['index','show']);
    Route::controller(\App\Http\Controllers\Api\V1\RequestController::class)->middleware(['auth:sanctum','has_item','has_role'])->prefix('requests')->group(function () {
        Route::get('' , 'index');
        Route::get('{request}' , 'show');
        Route::post('' , 'create');
        Route::post('{request}/confirm' , 'confirm');
        Route::post('{request}/admin-submit' , 'adminStore');
        Route::patch('{request}' , 'update');
        Route::get('{request}/comments' , 'getComments');
    });
    Route::controller(\App\Http\Controllers\Api\V1\ReportController::class)->middleware(['auth:sanctum','has_item','has_role'])->prefix('reports')->group(function () {
        Route::get('' , 'index');
        Route::get('{report}' , 'show');
        Route::post('{request}' , 'create');
        Route::post('{report}/confirm' , 'confirm');
        Route::post('{report}/admin-submit' , 'adminStore');
        Route::patch('{report}' , 'update');
        Route::get('{report}/comments' , 'getComments');
    });
    Route::controller(\App\Http\Controllers\Api\V1\WrittenController::class)->middleware(['auth:sanctum','has_item','has_role'])->prefix('written-requests')->group(function () {
        Route::get('' , 'index');
        Route::get('{id}' , 'show');
        Route::post('' , 'store');
        Route::post('{id}/admin-submit' , 'adminStore');
        Route::patch('{id}' , 'update');
    });
    Route::group(['middleware' => ["auth:sanctum",'has_item','has_role']] , function () {
        Route::get('rings/export' , [\App\Http\Controllers\Api\V1\RingController::class,'export']);
        Route::apiResource("rings" , \App\Http\Controllers\Api\V1\RingController::class);
        Route::delete('rings/{ring}/{member}' , [\App\Http\Controllers\Api\V1\RingController::class,'destroyMember']);
    });
});
