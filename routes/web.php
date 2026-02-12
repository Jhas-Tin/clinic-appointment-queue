<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

// Admin Controllers
use App\Http\Controllers\Admin\AppointmentController as AdminAppointmentController;
use App\Http\Controllers\AdminController;

// User Controllers
use App\Http\Controllers\User\DashboardController as UserDashboardController;
use App\Http\Controllers\User\AppointmentController as UserAppointmentController;

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () { 
    return view('auth.login'); 
});

Route::get('/register', [AuthController::class, 'registerForm']);
Route::post('/register', [AuthController::class, 'register']);

Route::get('/login', [AuthController::class, 'loginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

/*
|--------------------------------------------------------------------------
| User Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->name('user.')->group(function () {

    // User Dashboard
    Route::get('/dashboard', [UserDashboardController::class, 'index'])->name('dashboard');

    // User Appointments
    Route::get('/appointments', [UserAppointmentController::class, 'index'])->name('appointments');
    Route::post('/appointments', [UserAppointmentController::class, 'store'])->name('appointments.store');

    // Payments and Profile Pages
    Route::get('/payments', function () { 
        return view('user.payments'); 
    })->name('payments');

    Route::get('/profile', function () { 
        return view('user.profile'); 
    })->name('profile');
});

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/

Route::prefix('admin')->name('admin.')->middleware('auth:admin')->group(function () {

    // Admin Dashboard
    Route::get('/dashboard', function () { 
        return view('admin.dashboard'); 
    })->name('dashboard');

    // Admin Appointments
    Route::get('/appointments', [AdminAppointmentController::class, 'index'])->name('appointments');
    Route::post('/appointments/{appointment}/approve', [AdminAppointmentController::class, 'approve'])->name('appointments.approve');
    Route::post('/appointments/{appointment}/cancel', [AdminAppointmentController::class, 'cancel'])->name('appointments.cancel');
    Route::get('/appointments/{appointment}', [AdminAppointmentController::class, 'show'])->name('appointments.show');

    // Admin Profile
    Route::get('/profile', [AdminController::class, 'profile'])->name('profile');
    Route::post('/profile', [AdminController::class, 'updateProfile'])->name('profile.update');
});
