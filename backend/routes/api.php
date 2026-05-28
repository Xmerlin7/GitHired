<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\JobListingController;
use App\Http\Controllers\Api\AuthController;


Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);

    });
    // 2. الـ Routes المحمية (لازم Login + تفنيد الرولز)
    Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('jobs', JobListingController::class);

    // الـ Employers فقط هما اللي يقدروا يضيفوا، يعدلوا، أو يمسحوا وظائف
    Route::middleware('role:employer,admin')->group(function () {
        Route::post('/jobs', [JobListingController::class, 'store']);
        Route::put('/jobs/{id}', [JobListingController::class, 'update']);
        Route::delete('/jobs/{id}', [JobListingController::class, 'destroy']);
    });

    Route::middleware('role:candidate')->group(function () {
        // Route::post('/jobs/{id}/apply', [ApplicationController::class, 'store']);
    });

});
