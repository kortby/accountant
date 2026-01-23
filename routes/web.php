<?php

use App\Http\Controllers\BlogPostController;
use App\Http\Controllers\TaxReturnController;
use App\Http\Controllers\WebController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;


Route::get('/', [WebController::class, 'home'])->name('home');
Route::get('/tax-services', [WebController::class, 'taxService'])->name('taxService');
Route::get('/about', [WebController::class, 'aboutUs'])->name('aboutUs');
Route::get('/contact', [WebController::class, 'contact'])->name('contact');
Route::post('/message', [WebController::class, 'storeMessage'])->name('store-message');

Route::get('/blog/{blogPost}', [BlogPostController::class, 'show'])->name('blog.show');
Route::get('/blog/category/{blogPost}', [BlogPostController::class, 'category'])->name('blog.category');


Route::get('/book', function () {
        return Inertia::render('Booking');
    })->name('book');

Route::get('/file-taxes', function () {
        return Inertia::render('TaxReturnForm');
    })->middleware(['auth', 'verified'])->name('file-taxes');




Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');

    Route::get('/tax-returns', [TaxReturnController::class, 'index'])->name('tax-information.index');

    Route::get('/tax-returns/{taxReturn}', [TaxReturnController::class, 'show'])->name('tax-information.show');

    Route::post('/store-tax', [TaxReturnController::class, 'store'])->name('tax-information.store');

    Route::get('bookings', function () {
        return Inertia::render('BookingList');
    })->name('bookings');

    Route::get('availability', function () {
        return Inertia::render('Availability');
    })->name('availability');
    
})->middleware(['auth', 'verified']);

require __DIR__.'/settings.php';
