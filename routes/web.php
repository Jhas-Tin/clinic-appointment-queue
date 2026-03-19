<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\FirebaseAuthController;
use App\Http\Controllers\Admin\AppointmentController as AdminAppointmentController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\InventoryController as AdminInventoryController;
use App\Http\Controllers\Admin\DoctorAvailabilityController as AdminDoctorAvailabilityController;
use App\Http\Controllers\User\DashboardController as UserDashboardController;
use App\Http\Controllers\User\AppointmentController as UserAppointmentController;
use App\Http\Controllers\Doctor\DashboardController as DoctorDashboardController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Public routes
Route::get('/', function () { 
    return view('auth.login'); 
});

Route::get('/register', [AuthController::class, 'registerForm'])->name('register.form');
Route::post('/register', [AuthController::class, 'register'])->name('register');

Route::get('/login', [AuthController::class, 'loginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);

// Firebase authentication route
Route::post('/firebase/login', [FirebaseAuthController::class, 'login'])->name('firebase.login');

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

/*
|--------------------------------------------------------------------------
| User (Patient/Receptionist) Routes
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->name('user.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [UserDashboardController::class, 'index'])->name('dashboard');

    // Appointments
    Route::get('/appointments', [UserAppointmentController::class, 'index'])->name('appointments');
    Route::post('/appointments', [UserAppointmentController::class, 'store'])->name('appointments.store');

    // Payments
    Route::get('/payments', function () { 
        return view('user.payments'); 
    })->name('payments');

    // Profile
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
    // Dashboard
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    // Appointments Management
    Route::get('/appointments', [AdminAppointmentController::class, 'index'])->name('appointments');
    Route::post('/appointments/{appointment}/approve', [AdminAppointmentController::class, 'approve'])->name('appointments.approve');
    Route::post('/appointments/{appointment}/cancel', [AdminAppointmentController::class, 'cancel'])->name('appointments.cancel');
    Route::get('/appointments/{appointment}', [AdminAppointmentController::class, 'show'])->name('appointments.show');
    Route::delete('/appointments/{appointment}', [AdminAppointmentController::class, 'destroy'])->name('appointments.destroy');

    // Inventory Management (Full CRUD)
    Route::resource('inventory', AdminInventoryController::class);

    // Admin Profile
    Route::get('/profile', [AdminController::class, 'profile'])->name('profile');
    Route::post('/profile', [AdminController::class, 'updateProfile'])->name('profile.update');
    
    // Doctor Availability Management
    Route::get('/doctor-availability', [AdminDoctorAvailabilityController::class, 'index'])->name('doctor-availability.index');
    Route::get('/doctor-availability/{id}/edit', [AdminDoctorAvailabilityController::class, 'edit'])->name('doctor-availability.edit');
    Route::post('/doctor-availability/{id}/update', [AdminDoctorAvailabilityController::class, 'update'])->name('doctor-availability.update');
});

/*
|--------------------------------------------------------------------------
| Doctor Routes
|--------------------------------------------------------------------------
*/
Route::prefix('doctor')->name('doctor.')->middleware('auth:doctor')->group(function () {
    // Dashboard
    Route::get('/dashboard', [DoctorDashboardController::class, 'index'])->name('dashboard');

    // Profile Management
    Route::get('/profile', [DoctorDashboardController::class, 'profile'])->name('profile');
    Route::post('/profile', [DoctorDashboardController::class, 'updateProfile'])->name('profile.update');

    // Appointment Actions
    Route::post('/appointments/{id}/approve', [DoctorDashboardController::class, 'approve'])->name('appointments.approve');
    Route::post('/appointments/{id}/cancel', [DoctorDashboardController::class, 'cancel'])->name('appointments.cancel');
    Route::delete('/appointments/{id}', [DoctorDashboardController::class, 'destroy'])->name('appointments.destroy');

    // Availability Management (View Only - Status Update Only)
    Route::get('/availability', [DoctorDashboardController::class, 'availability'])->name('availability');
    Route::post('/availability/update-status', [DoctorDashboardController::class, 'updateStatus'])->name('availability.update-status');
});