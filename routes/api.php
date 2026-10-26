<?php

use App\Http\Controllers\Api\SafariBookingController;
use App\Http\Controllers\SafariBookingRequestController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->name('api.v1.')->group(function (): void {
    Route::get('/booking-options', [SafariBookingController::class, 'options'])->name('booking-options');
    Route::get('/availability', [SafariBookingRequestController::class, 'availability'])->name('availability');
    Route::post('/booking-requests', [SafariBookingController::class, 'store'])
        ->middleware('throttle:15,1')
        ->name('booking-requests.store');
    Route::get('/booking-requests/{confirmationToken}', [SafariBookingController::class, 'show'])
        ->middleware('throttle:60,1')
        ->name('booking-requests.show');
    Route::put('/booking-requests/{confirmationToken}/slots', [SafariBookingController::class, 'saveSlots'])
        ->middleware('throttle:15,1')
        ->name('booking-requests.slots.update');
    Route::post('/booking-requests/{confirmationToken}/submit', [SafariBookingController::class, 'submit'])
        ->middleware('throttle:15,1')
        ->name('booking-requests.submit');
});
