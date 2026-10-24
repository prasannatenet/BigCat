<?php

use App\Http\Controllers\SafariBookingRequestController;
use Illuminate\Support\Facades\Route;

Route::get('/', [SafariBookingRequestController::class, 'index'])->name('home');
Route::post('/safari-booking-details', [SafariBookingRequestController::class, 'startBooking'])->name('safari-booking.details');
Route::get('/safari-booking-slots', [SafariBookingRequestController::class, 'slots'])->name('safari-booking.slots');
Route::get('/safari-availability', [SafariBookingRequestController::class, 'availability'])->name('safari.availability');
Route::post('/safari-booking-requests', [SafariBookingRequestController::class, 'store'])->name('safari-booking-requests.store');
Route::get('/safari-booking-check/{confirmationToken}', [SafariBookingRequestController::class, 'checking'])
    ->name('safari-booking.checking');
Route::get('/safari-booking-confirmation/{confirmationToken}', [SafariBookingRequestController::class, 'confirmation'])
    ->name('safari-booking.confirmation');
Route::post('/safari-booking-confirmation/{confirmationToken}/submit', [SafariBookingRequestController::class, 'submitBookingRequest'])
    ->name('safari-booking.confirmation.submit');

use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminLoginController;
use App\Http\Controllers\Admin\BookingRequestController;
use App\Http\Controllers\Admin\CouponController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\MailSettingsController;
use App\Http\Controllers\Admin\NationalParkController;
use App\Http\Controllers\Admin\SlotController;
use App\Http\Controllers\Admin\VehicleController;

Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/login', [AdminLoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AdminLoginController::class, 'login'])->name('login.submit');
    Route::post('/logout', [AdminLoginController::class, 'logout'])->name('logout');

    Route::get('/dashboard', [AdminDashboardController::class, 'index'])
        ->name('dashboard')
        ->middleware('admin');

    Route::resource('vehicles', VehicleController::class)
        ->middleware('admin');

    Route::resource('national-parks', NationalParkController::class)
        ->middleware('admin');

    Route::resource('coupons', CouponController::class)
        ->only(['index', 'create', 'store'])
        ->middleware('admin');

    Route::get('/email-settings', [MailSettingsController::class, 'index'])
        ->name('email-settings.index')
        ->middleware('admin');
    Route::post('/email-settings', [MailSettingsController::class, 'update'])
        ->name('email-settings.update')
        ->middleware('admin');

    Route::get('/customers', [CustomerController::class, 'index'])
        ->name('customers.index')
        ->middleware('admin');

    Route::get('/booking-requests', [BookingRequestController::class, 'index'])
        ->name('booking-requests.index')
        ->middleware('admin');
    Route::get('/booking-requests/{bookingRequest}', [BookingRequestController::class, 'show'])
        ->name('booking-requests.show')
        ->middleware('admin');

    Route::get('/slots', [SlotController::class, 'index'])
        ->name('slots.index')
        ->middleware('admin');
    Route::post('/slots', [SlotController::class, 'store'])
        ->name('slots.store')
        ->middleware('admin');
    Route::put('/slots/parks/{nationalPark}/availability', [SlotController::class, 'updateParkAvailability'])
        ->name('slots.park-availability.update')
        ->middleware('admin');
    Route::patch('/slots/{parkZone}', [SlotController::class, 'update'])
        ->name('slots.update')
        ->middleware('admin');
    Route::delete('/slots/{parkZone}', [SlotController::class, 'destroy'])
        ->name('slots.destroy')
        ->middleware('admin');
});
