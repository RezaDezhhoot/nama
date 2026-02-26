<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'v2'] , function () {
    Route::get('info' , \App\Http\Controllers\Api\V2\InfoController::class)->middleware(['auth:sanctum','has_item','has_role']);
});
Route::group(['prefix' => 'v1','as' => 'api.'] , function () {
    Route::group(['prefix' => "auth",'as' => "auth."] , function () {
        Route::post("send-request" , [\App\Http\Controllers\Api\V1\AuthController::class,'sendRequest']);
        Route::withoutMiddleware('api')->get("verify" , [\App\Http\Controllers\Api\V1\AuthController::class,'verify'])->name('verify');
        Route::post('ouath-logout',[\App\Http\Controllers\Api\V1\AuthController::class,'logout']);
    });

    Route::group(['prefix' => "inquiry"] , function () {
        Route::get("units" , \App\Http\Controllers\Api\V1\Inquiry\UnitController::class);
        Route::get("requests" , \App\Http\Controllers\Api\V1\Inquiry\RequestController::class);
        Route::get("reports" , \App\Http\Controllers\Api\V1\Inquiry\ReportController::class);
        Route::get("role/{code}" , [\App\Http\Controllers\Api\V1\Inquiry\RoleController::class,'show']);
    });
    Route::get('users/profile' , \App\Http\Controllers\Api\V1\UserController::class)->middleware(['auth:sanctum']);
    Route::get('info' , \App\Http\Controllers\Api\V1\InfoController::class)->middleware(['auth:sanctum','has_item','has_role']);
    Route::get('units' , \App\Http\Controllers\Api\V1\UnitController::class)->middleware(['auth:sanctum','has_item','has_role']);
    Route::apiResource('dashboard-items' , \App\Http\Controllers\Api\V1\DashboardItemController::class)->only(['index','show']);
    Route::get('banners' , \App\Http\Controllers\Api\V1\BannerController::class);
    Route::group(['prefix' => 'request-plans','middleware' => ['auth:sanctum','has_item']] , function () {
        Route::get('',[\App\Http\Controllers\Api\V1\RequestPlanController::class,'index']);
        Route::get('list',[\App\Http\Controllers\Api\V1\RequestPlanController::class,'list']);
        Route::get('{id}',[\App\Http\Controllers\Api\V1\RequestPlanController::class,'show']);
    });
    Route::controller(\App\Http\Controllers\Api\V1\RequestController::class)->middleware(['auth:sanctum','has_item','has_role'])->prefix('requests')->group(function () {
        Route::get('' , 'index');
        Route::get('{request}' , 'show');
        Route::post('' , 'create');
        Route::post('{request}/confirm' , 'confirm');
        Route::post('{request}/admin-submit' , 'adminStore');
        Route::patch('{request}' , 'update');
        Route::get('{request}/comments' , 'getComments');
        Route::delete('remove-file/{file}' , 'deleteFile');
    });
    Route::controller(\App\Http\Controllers\Api\V1\FormController::class)->middleware(['auth:sanctum','has_item','has_role'])->prefix('forms')->group(function () {
        Route::get('' , 'index');
        Route::get('show/{form}' , 'show');
        Route::post('submit/{form}' , 'submit');
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
    Route::post('client-log',\App\Http\Controllers\Api\V1\ClientLogController::class)->middleware('throttle:100,1');
    Route::prefix('locations')->controller(\App\Http\Controllers\Api\V1\LocationController::class)
        ->group(function (){
            Route::get('regions','regions');
        });
});
