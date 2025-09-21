<?php

use App\Http\Controllers\FieldController;
use App\Http\Controllers\FormController;
use App\Http\Controllers\SubmissionController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Form routes
Route::apiResource('forms', FormController::class);

// Field routes
Route::post('forms/{form}/fields', [FieldController::class, 'store']);
Route::put('fields/{field}', [FieldController::class, 'update']);
Route::delete('fields/{field}', [FieldController::class, 'destroy']);
Route::post('forms/{form}/reorder-fields', [FieldController::class, 'reorder']);

// Submission routes
Route::get('forms/{form}/submissions', [SubmissionController::class, 'index']);
Route::post('forms/{form}/submissions', [SubmissionController::class, 'store']);
Route::get('submissions/{submission}', [SubmissionController::class, 'show']);