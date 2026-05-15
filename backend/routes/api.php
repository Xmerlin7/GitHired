<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\JobListingController;

Route::apiResource('jobs', JobListingController::class);
