<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\IncidentController;
use App\Http\Controllers\MonitorController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/signin', [AuthController::class, 'signin'])->middleware('throttle:5,1');
Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:10,1');

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', fn (Request $request) => $request->user());
    Route::post('/signout', [AuthController::class, 'signout']);

    Route::get('/dashboard/stats', [DashboardController::class, 'stats']);

    Route::apiResource('monitors', MonitorController::class);
    Route::get('/monitors/{monitor}/checks', [MonitorController::class, 'checks']);
    Route::post('/monitors/{monitor}/check', [MonitorController::class, 'check']);

    Route::get('/incidents', [IncidentController::class, 'index']);
    Route::get('/incidents/{incident}', [IncidentController::class, 'show']);
});
