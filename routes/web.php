<?php

use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('welcome');
// });

use App\Http\Controllers\PageController; // Wajib ditambahkan di Laravel 11
use App\Http\Controllers\ShuttleController;

Route::get('/', [PageController::class, 'index']);
Route::get('/about', [PageController::class, 'about']);
Route::get('/service', [PageController::class, 'service']);
Route::get('/schedule', [PageController::class, 'schedule']);
Route::get('/schedule-master', [PageController::class, 'schedule_master']);
Route::get('/schedule2', [PageController::class, 'schedule2']);
Route::resource('shuttles', ShuttleController::class);