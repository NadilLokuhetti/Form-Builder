<?php
// routes/api.php

use App\Http\Controllers\FormController;
use App\Http\Controllers\FormSubmissionController;
use Illuminate\Support\Facades\Route;

Route::apiResource('forms', FormController::class);
Route::get('forms/{form}/submissions', [FormSubmissionController::class, 'index']);
Route::post('forms/{form}/submissions', [FormSubmissionController::class, 'store']);
Route::get('submissions/{submission}', [FormSubmissionController::class, 'show']);