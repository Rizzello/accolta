<?php

use App\Http\Controllers\Api\OpenApiFormController;
use App\Http\Controllers\Api\SubmitFormController;
use Illuminate\Support\Facades\Route;

Route::get('/forms/{uuid}/openapi.json', OpenApiFormController::class);

Route::post('/forms/{uuid}/submissions', SubmitFormController::class)
    ->middleware('throttle:form-submissions');
