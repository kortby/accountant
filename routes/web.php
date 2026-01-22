<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canRegister' => Features::enabled(Features::registration()),
    ]);
})->name('home');




Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');

    Route::get('bookings', function () {
        return Inertia::render('BookingList');
    })->name('bookings');

    Route::get('availability', function () {
        return Inertia::render('Availability');
    })->name('availability');
    
})->middleware(['auth', 'verified']);

require __DIR__.'/settings.php';
