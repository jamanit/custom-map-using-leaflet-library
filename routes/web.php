<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\mapController;

Route::get('/', function () {
    return view('welcome');
});

Route::resource('map', mapController::class);
