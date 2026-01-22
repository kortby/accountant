<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\BookingController;
use App\Http\Controllers\MessageController;
use Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful;
use Laravel\Sanctum\Sanctum;


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::middleware(EnsureFrontendRequestsAreStateful::class)->group(function () {
    Route::resource('availability', App\Http\Controllers\AvailabilityController::class)->only([
        'index', 'store', 'destroy'
    ]);

    Route::post('/bookings', [BookingController::class, 'store']);
    Route::get('/bookings', [BookingController::class, 'index']);
    Route::get('/bookings/{booking}', [BookingController::class, 'show']);
    Route::put('/bookings/{booking}/status', [BookingController::class, 'updateStatus']);

    Route::get('/dashboard/metrics', [BookingController::class, 'indexMetrics']);
});


// Route::post('/contact', [App\Http\Controllers\ContactController::class, 'store']);
Route::get('/public/available-slots', [App\Http\Controllers\AvailabilityController::class, 'getAvailableSlots']);
