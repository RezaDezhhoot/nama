<?php

use Illuminate\Support\Facades\Route;

Route::get('' , function () {
    dd(request()->all());
});
