<?php

use App\Http\Controllers\BlogPostController;
use App\Http\Controllers\TaxReturnController;
use App\Http\Controllers\TestimonialController;
use App\Http\Controllers\WebController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', [WebController::class, 'home'])->name('home');
Route::get('/tax-services', [WebController::class, 'taxService'])->name('taxService');
Route::get('/about', [WebController::class, 'aboutUs'])->name('aboutUs');
Route::get('/contact', [WebController::class, 'contact'])->name('contact');
Route::post('/message', [WebController::class, 'storeMessage'])->name('store-message');

Route::get('/blog', [BlogPostController::class, 'index'])->name('blog.index');
Route::get('/blog/{blogPost}', [BlogPostController::class, 'show'])->name('blog.show');
Route::get('/blog/category/{blogPost}', [BlogPostController::class, 'category'])->name('blog.category');

// Blog management routes (authenticated)
Route::middleware(['auth', 'verified', 'role:accountant,admin'])->group(function () {
    Route::get('/blogs/create', [BlogPostController::class, 'create'])->name('blogs.create');
    Route::post('/blogs', [BlogPostController::class, 'store'])->name('blogs.store');
    Route::get('/blogs/{post}/edit', [BlogPostController::class, 'edit'])->name('blogs.edit');
    Route::put('/blogs/{post}', [BlogPostController::class, 'update'])->name('blogs.update');
    Route::delete('/blogs/{post}', [BlogPostController::class, 'destroy'])->name('blogs.destroy');
});

Route::get('/book', function () {
    return Inertia::render('Booking');
})->name('book');

Route::get('/file-taxes', [TaxReturnController::class, 'create'])->middleware(['auth', 'verified'])->name('file-taxes');

Route::get('/file-taxes-for-client', [TaxReturnController::class, 'createForClient'])
    ->middleware(['auth', 'verified', 'role:accountant,admin'])
    ->name('file-taxes-for-client');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');

    Route::get('/tax-returns', [TaxReturnController::class, 'index'])->name('tax-information.index');

    Route::get('/tax-returns/{taxReturn}', [TaxReturnController::class, 'show'])->name('tax-information.show');

    Route::post('/store-tax', [TaxReturnController::class, 'store'])->name('tax-information.store');

    // Testimonial routes
    Route::get('/testimonials/create', [TestimonialController::class, 'create'])->name('testimonials.create');
    Route::post('/testimonials', [TestimonialController::class, 'store'])->name('testimonials.store');

    // Accountant-only routes
    Route::middleware('role:accountant,admin')->group(function () {
        Route::get('/tax-returns/{taxReturn}/edit', [TaxReturnController::class, 'edit'])->name('tax-returns.edit');
        Route::patch('/tax-returns/{taxReturn}', [TaxReturnController::class, 'update'])->name('tax-returns.update');
        Route::post('/tax-returns/{taxReturn}/assign', [TaxReturnController::class, 'assign'])->name('tax-returns.assign');
        Route::patch('/tax-returns/{taxReturn}/status', [TaxReturnController::class, 'updateStatus'])->name('tax-returns.update-status');
        Route::patch('/tax-returns/{taxReturn}/income', [TaxReturnController::class, 'updateIncome'])->name('tax-returns.update-income');
        Route::post('/tax-returns/{taxReturn}/deductions', [TaxReturnController::class, 'addDeduction'])->name('tax-returns.add-deduction');
        Route::post('/tax-returns/{taxReturn}/upload-documents', [TaxReturnController::class, 'uploadDocuments'])->name('tax-returns.upload-documents');
    });

    // Admin-only routes
    Route::middleware('role:admin')->group(function () {
        Route::get('bookings', function () {
            return Inertia::render('BookingList');
        })->name('bookings');

        Route::get('availability', function () {
            return Inertia::render('Availability');
        })->name('availability');
    });

});

require __DIR__.'/settings.php';
