<?php

use App\Http\Controllers\Api\BookingController;
use App\Http\Controllers\Api\FavoriteController;
use App\Http\Controllers\Api\RentalController;
use App\Http\Controllers\Api\ReviewController;
use Illuminate\Support\Facades\Route;

Route::get('/rentals', [RentalController::class, 'index']);
Route::get('/rentals/{slug}', [RentalController::class, 'show'])
    ->where('slug', '[A-Za-z0-9\-]+');

Route::get('/reviews', [ReviewController::class, 'index']);
Route::get('/reviews/{review}', [ReviewController::class, 'show'])->whereNumber('review');

Route::middleware('auth')->group(function (): void {
    Route::post('/rentals', [RentalController::class, 'store']);
    Route::put('/rentals/{rental}', [RentalController::class, 'update'])->whereNumber('rental');
    Route::delete('/rentals/{rental}', [RentalController::class, 'destroy'])->whereNumber('rental');

    Route::get('/bookings', [BookingController::class, 'index']);
    Route::post('/bookings', [BookingController::class, 'store']);
    Route::get('/bookings/{booking}', [BookingController::class, 'show'])->whereNumber('booking');
    Route::patch('/bookings/{booking}/cancel', [BookingController::class, 'cancel'])->whereNumber('booking');

    Route::post('/reviews', [ReviewController::class, 'store']);
    Route::put('/reviews/{review}', [ReviewController::class, 'update'])->whereNumber('review');
    Route::delete('/reviews/{review}', [ReviewController::class, 'destroy'])->whereNumber('review');

    Route::get('/favorites', [FavoriteController::class, 'index']);
    Route::post('/favorites', [FavoriteController::class, 'store']);
    Route::delete('/favorites/{rental}', [FavoriteController::class, 'destroy'])->whereNumber('rental');
});
