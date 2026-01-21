<?php

use App\Http\Controllers\BookingController;
use App\Http\Controllers\CustomerAuthController;
use App\Http\Controllers\CustomerDashboardController;
use App\Http\Controllers\UnitKerjaAuthController;
use App\Http\Controllers\UnitKerjaDashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('booking.index');
});

// Public Booking Routes
Route::prefix('booking')->name('booking.')->group(function () {
    Route::get('/', [BookingController::class, 'index'])->name('index');
    Route::get('/rooms', [BookingController::class, 'rooms'])->name('rooms');
    
    // Protected booking routes - require authentication
    Route::middleware('auth:customer')->group(function () {
        Route::get('/create', [BookingController::class, 'create'])->name('create');
        Route::post('/store', [BookingController::class, 'store'])->name('store');
    });
    
    Route::get('/success/{reservation}', [BookingController::class, 'success'])->name('success');
    Route::get('/check', [BookingController::class, 'check'])->name('check');
    
    // Payment routes
    Route::get('/payment/{reservation}', [BookingController::class, 'payment'])->name('payment');
    Route::post('/payment/{reservation}/upload', [BookingController::class, 'uploadPayment'])->name('payment.upload');
    Route::get('/payment/{reservation}/success', [BookingController::class, 'paymentSuccess'])->name('payment.success');
});

// Building & Room Detail Routes
Route::get('/building/{building:code}', [BookingController::class, 'buildingDetail'])->name('building.detail');
Route::get('/room/{building:code}/{roomType}', [BookingController::class, 'roomDetail'])->name('room.detail');

// Customer Auth Routes
Route::prefix('customer')->name('customer.')->group(function () {
    // Guest routes
    Route::middleware('guest:customer')->group(function () {
        Route::get('/login', [CustomerAuthController::class, 'showLogin'])->name('login');
        Route::post('/login', [CustomerAuthController::class, 'login'])->name('login.submit');
        Route::get('/register', [CustomerAuthController::class, 'showRegister'])->name('register');
        Route::post('/register', [CustomerAuthController::class, 'register'])->name('register.submit');
    });

    // Authenticated routes
    Route::middleware('auth:customer')->group(function () {
        Route::get('/dashboard', [CustomerDashboardController::class, 'index'])->name('dashboard');
        Route::get('/reservation/{reservation}', [CustomerDashboardController::class, 'reservation'])->name('reservation');
        Route::post('/logout', [CustomerAuthController::class, 'logout'])->name('logout');
    });
});

// Unit Kerja Routes
Route::prefix('unit-kerja')->name('unit-kerja.')->group(function () {
    // Guest routes
    Route::middleware('guest:unit_kerja')->group(function () {
        Route::get('/login', [UnitKerjaAuthController::class, 'showLogin'])->name('login');
        Route::post('/login', [UnitKerjaAuthController::class, 'login'])->name('login.submit');
    });

    // Authenticated routes
    Route::middleware('auth:unit_kerja')->group(function () {
        Route::get('/dashboard', [UnitKerjaDashboardController::class, 'index'])->name('dashboard');
        Route::get('/rooms', [UnitKerjaDashboardController::class, 'rooms'])->name('rooms');
        Route::get('/booking/{room}', [UnitKerjaDashboardController::class, 'createBooking'])->name('booking.create');
        Route::post('/booking/store', [UnitKerjaDashboardController::class, 'storeBooking'])->name('booking.store');
        Route::get('/reservation/{reservation}', [UnitKerjaDashboardController::class, 'reservation'])->name('reservation');
        Route::get('/billings', [UnitKerjaDashboardController::class, 'billings'])->name('billings');
        
        // Bulk Booking
        Route::get('/bulk-booking', [UnitKerjaDashboardController::class, 'bulkBookingSearch'])->name('bulk.search');
        Route::get('/bulk-booking/select', [UnitKerjaDashboardController::class, 'bulkBookingSelect'])->name('bulk.select');
        Route::post('/bulk-booking/store', [UnitKerjaDashboardController::class, 'storeBulkBooking'])->name('bulk.store');

        Route::post('/logout', [UnitKerjaAuthController::class, 'logout'])->name('logout');
    });
});
