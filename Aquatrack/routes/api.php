<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\NotificationsController;
use App\Http\Controllers\ReportController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Reports notifications endpoint
Route::middleware('auth:sanctum')->group(function () {
    // Reports notifications endpoint
    Route::get('/reports/notifications', [NotificationsController::class, 'getReports'])
        ->name('api.reports.notifications');


    // Existing report API routes
    Route::get('/reports/find', [ReportController::class, 'findByTrackingCode'])
        ->name('api.reports.find');
});

// Public API routes (if any)
Route::middleware('api')->group(function () {
    // Add any public API routes here
});


