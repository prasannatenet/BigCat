<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminLoginController;
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
