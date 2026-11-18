<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ShuttleApiController;

Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/shuttles', [ShuttleApiController::class, 'index']);      // GET All
    Route::post('/shuttles', [ShuttleApiController::class, 'store']);     // Create
    Route::get('/shuttles/{id}', [ShuttleApiController::class, 'show']);   // Get By ID
    Route::put('/shuttles/{id}', [ShuttleApiController::class, 'update']); // Update
    Route::delete('/shuttles/{id}', [ShuttleApiController::class, 'destroy']); // Delete
});
