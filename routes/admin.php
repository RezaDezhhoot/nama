<?php

use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'admin' , 'as' => 'admin.'] , function () {
    Route::group(['middleware' => 'auth'] , function () {
        Route::group(['prefix' => 'dashboard' , 'as' => 'dashboard.'] , function () {
            Route::get('' , \App\Livewire\Dashboards\Dashboard::class)->name('index');
        });
        Route::group(['prefix' => 'plans' , 'as' => 'plans.','middleware' => 'is_admin'] , function () {
            Route::get('' , \App\Livewire\Plans\IndexPlan::class)->name('index');
            Route::get('{action}/{id?}' , \App\Livewire\Plans\StorePlan::class)->name('store');
        });
        Route::group(['prefix' => 'banners' , 'as' => 'banners.','middleware' => 'is_admin'] , function () {
            Route::get('' , \App\Livewire\Banners\IndexBanner::class)->name('index');
            Route::get('{action}/{id?}' , \App\Livewire\Banners\StoreBanner::class)->name('store');
        });
        Route::group(['prefix' => 'dashboard-items' , 'as' => 'dashboard-items.','middleware' => 'is_admin'] , function () {
            Route::get('' , \App\Livewire\DashboardItems\IndexItem::class)->name('index');
            Route::get('{action}/{id?}' , \App\Livewire\DashboardItems\StoreItem::class)->name('store');
        });
        Route::group(['prefix' => 'users' , 'as' => 'users.','middleware' => 'is_admin'] , function () {
            Route::get('roles' , \App\Livewire\Users\AttachRole::class)->name('roles');
        });
        Route::group(['prefix' => 'feed' , 'as' => 'feed.','middleware' => 'is_admin'] , function () {
            Route::get('users' , \App\Http\Controllers\Feed\UserFeedController::class)->name('users');
        });
        Route::group(['prefix' => 'requests' , 'as' => 'requests.' , 'middleware' => 'is_operator'] , function () {
            Route::get('' , \App\Livewire\Requests\IndexRequest::class)->name('index');
            Route::get('{action}/{id}' , \App\Livewire\Requests\StoreRequest::class)->name('store');
        });
        Route::group(['prefix' => 'written-requests' , 'as' => 'written-requests.' , 'middleware' => 'is_operator'] , function () {
            Route::get('' , \App\Livewire\WrittenRequests\IndexRequest::class)->name('index');
            Route::get('{action}/{id}' , \App\Livewire\WrittenRequests\StoreRequest::class)->name('store');
        });
        Route::group(['prefix' => 'reports' , 'as' => 'reports.' , 'middleware' => 'is_operator'] , function () {
            Route::get('' , \App\Livewire\Reports\IndexReport::class)->name('index');
            Route::get('{action}/{id}' , \App\Livewire\Reports\StoreReport::class)->name('store');
        });
    });
    Route::group(['prefix' => 'auth' , 'as' => 'auth.'],function (){
        Route::middleware('guest')->get('/auth', \App\Livewire\Auth\Auth::class)->name('login');
        Route::middleware('auth')->get('/logout', \App\Livewire\Auth\Logout::class)->name('logout');
    });
});
