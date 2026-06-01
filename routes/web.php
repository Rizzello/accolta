<?php

use App\Http\Controllers\SwaggerFormViewController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/forms/{uuid}/swagger', SwaggerFormViewController::class);
