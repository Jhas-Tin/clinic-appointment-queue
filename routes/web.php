<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\AppointmentController;
use App\Http\Controllers\AdminController; // <-- added this

/*
|-------------------------------------------------------------------------- 
| User & Admin Shared Login Routes
|-------------------------------------------------------------------------- 
*/

// Home - show login page (shared)
Route::get('/', function () {
    return view('auth.login');
});

// Register (users only)
Route::get('/register', [AuthController::class, 'registerForm']);
Route::post('/register', [AuthController::class, 'register']);

// Login (shared)
Route::get('/login', [AuthController::class, 'loginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);

// Logout (shared)
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');


/*
|-------------------------------------------------------------------------- 
| User Dashboard
|-------------------------------------------------------------------------- 
*/
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});


/*
|-------------------------------------------------------------------------- 
| Admin Routes
|-------------------------------------------------------------------------- 
*/
Route::prefix('admin')->name('admin.')->middleware(['auth:admin'])->group(function () {

    // Admin Dashboard
    Route::get('/dashboard', function () {
        return view('admin.dashboard');
    })->name('dashboard');

    // Appointments
    Route::get('/appointments', [AppointmentController::class, 'index'])->name('appointments');

    // Approve / Cancel appointments
    Route::post('/appointments/{appointment}/approve', [AppointmentController::class, 'approve'])->name('appointments.approve');
    Route::post('/appointments/{appointment}/cancel', [AppointmentController::class, 'cancel'])->name('appointments.cancel');

    // Show single appointment
    Route::get('/appointments/{appointment}', [AppointmentController::class, 'show'])->name('appointments.show');

    // Create new appointment (optional)
    Route::get('/appointments/create', [AppointmentController::class, 'create'])->name('appointments.create');
    Route::post('/appointments', [AppointmentController::class, 'store'])->name('appointments.store');

    // ----- Profile routes added -----
    Route::get('/profile', [AdminController::class, 'profile'])->name('profile');
    Route::post('/profile', [AdminController::class, 'updateProfile'])->name('profile.update');

});
