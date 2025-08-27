<?php

use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'admin' , 'as' => 'admin.'] , function () {
    Route::group(['middleware' => ['auth','role:admin']] , function () {
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
            Route::get('roles/{action}/{id}' , \App\Livewire\Users\StoreUser::class)->name('roles.store');
            Route::get('permissions/{id}' , \App\Livewire\Users\StorePermissions::class)->name('permissions.store');
        });
        Route::group(['prefix' => 'feed' , 'as' => 'feed.','middleware' => 'is_admin'] , function () {
            Route::get('users/{withRoles?}' , \App\Http\Controllers\Feed\UserFeedController::class)->name('users');
            Route::get('units/{parent?}/{type?}/{main_unit?}' , \App\Http\Controllers\Feed\UnitFeedController::class)->name('units');
            Route::get('plans/{type?}/{ignore?}' , \App\Http\Controllers\Feed\PlanFeedController::class)->name('plans');
            Route::get('cities' , \App\Http\Controllers\Feed\CityFeedController::class)->name('cities');
            Route::get('roles' , \App\Http\Controllers\Feed\RoleFeedController::class)->name('roles');
            Route::get('batches/{type}/{subType?}' , \App\Http\Controllers\Feed\AccountingBatchFeedController::class)->name('batches');
            Route::get('regions/{city?}' , \App\Http\Controllers\Feed\RegionFeedController::class)->name('regions');
            Route::get('neighborhoods/{region?}' , \App\Http\Controllers\Feed\NeighborhoodFeedController::class)->name('neighborhoods');
            Route::get('areas/{neighborhood?}' , \App\Http\Controllers\Feed\AreaFeedController::class)->name('areas');
        });
        Route::group(['prefix' => 'requests' , 'as' => 'requests.' , 'middleware' => 'is_operator'] , function () {
            Route::get('{type}' , \App\Livewire\Requests\IndexRequest::class)->name('index');
            Route::get('{type}/{action}/{id}' , \App\Livewire\Requests\StoreRequest::class)->name('store');
        });
        Route::group(['prefix' => 'written-requests' , 'as' => 'written-requests.' , 'middleware' => 'is_operator'] , function () {
            Route::get('' , \App\Livewire\WrittenRequests\IndexRequest::class)->name('index');
            Route::get('{action}/{id}' , \App\Livewire\WrittenRequests\StoreRequest::class)->name('store');
        });
        Route::group(['prefix' => 'reports' , 'as' => 'reports.' , 'middleware' => 'is_operator'] , function () {
            Route::get('{type}' , \App\Livewire\Reports\IndexReport::class)->name('index');
            Route::get('{type}/{action}/{id}' , \App\Livewire\Reports\StoreReport::class)->name('store');
        });
        Route::group(['prefix' => 'rings' , 'as' => 'rings.' , 'middleware' => 'is_admin'] , function () {
            Route::get('' , \App\Livewire\Ring\IndexRing::class)->name('index');
            Route::get('/{action}/{id}' , \App\Livewire\Ring\StoreRing::class)->name('store');
        });
        Route::group(['prefix' => 'cities' , 'as' => 'cities.' , 'middleware' => 'is_admin'] , function () {
            Route::get('' , \App\Livewire\Cities\IndexCity::class)->name('index');
            Route::get('{action}/{id}' , \App\Livewire\Cities\StoreCity::class)->name('store');
        });
        Route::group(['prefix' => 'units' , 'as' => 'units.' , 'middleware' => 'is_admin'] , function () {
            Route::get('' , \App\Livewire\Units\IndexUnit::class)->name('index');
            Route::get('{action}/{id?}' , \App\Livewire\Units\StoreUnit::class)->name('store');
        });
        Route::group(['prefix' => 'forms' , 'as' => 'forms.' , 'middleware' => 'is_admin'] , function () {
            Route::get('' , \App\Livewire\Forms\IndexForm::class)->name('index');
            Route::get('{action}/{id?}' , \App\Livewire\Forms\StoreForm::class)->name('store');
        });
        Route::group(['prefix' => 'form-reports' , 'as' => 'form-reports.' , 'middleware' => 'is_admin'] , function () {
            Route::get('' , \App\Livewire\FormReports\IndexFormReport::class)->name('index');
            Route::get('{action}/{id}' , \App\Livewire\FormReports\StoreFormReport::class)->name('store');
        });
        Route::group(['prefix' => 'log-activities' , 'as' => 'log-activities.' , 'middleware' => 'is_admin'] , function () {
            Route::get('' , \App\Livewire\Logs\Activities\IndexLogs::class)->name('index');
            Route::get('roles' , \App\Livewire\Logs\Activities\IndexOtherRolesLogs::class)->name('roles');
        });
        Route::group(['prefix' => 'accounting' , 'as' => 'accounting.' , 'middleware' => 'is_admin'] , function () {
            Route::get('records' , \App\Livewire\Accounting\IndexRecord::class)->name('records');
        });
    });
    Route::group(['prefix' => 'auth' , 'as' => 'auth.'],function (){
        Route::middleware('guest')->get('/auth', \App\Livewire\Auth\Auth::class)->name('login');
        Route::middleware('auth')->get('/logout', \App\Livewire\Auth\Logout::class)->name('logout');
    });
});
