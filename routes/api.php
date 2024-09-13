<?php

use App\Http\Controllers\API\AttendanceController;
use App\Http\Controllers\API\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);

Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::get('/get-attendance-today', [AttendanceController::class, 'getAttendanceToday']);
    Route::get('get-schedule', [AttendanceController::class, 'getSchedule']);
    Route::post('/store-attendance', [AttendanceController::class, 'store']);
    Route::get('get-attendance-by-month-year/{month}/{year}', [AttendanceController::class, 'getAttendanceByMonthAndYear']);
    Route::post('/banned', [AttendanceController::class, 'banned']);
    Route::get('/get-image', [AuthController::class, 'getImageUserProfile']);
});

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
