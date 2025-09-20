<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\FormController;
use App\Http\Controllers\SubmissionController;

Route::apiResource('forms', FormController::class);
Route::get('forms/{form}/preview', [FormController::class, 'preview']);

Route::post('forms/{form}/submit', [SubmissionController::class, 'store']);
Route::get('forms/{form}/submissions', [SubmissionController::class, 'index']);
Route::get('submissions/{submission}', [SubmissionController::class, 'show']);


Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
