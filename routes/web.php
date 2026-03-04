<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

use App\Http\Controllers\Admin\AppointmentController as AdminAppointmentController;
use App\Http\Controllers\AdminController;

use App\Http\Controllers\User\DashboardController as UserDashboardController;
use App\Http\Controllers\User\AppointmentController as UserAppointmentController;
use App\Http\Controllers\Doctor\DashboardController as DoctorDashboardController;



Route::get('/', function () { 
    return view('auth.login'); 
});

Route::get('/register', [AuthController::class, 'registerForm']);
Route::post('/register', [AuthController::class, 'register']);

Route::get('/login', [AuthController::class, 'loginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');


Route::middleware('auth')->name('user.')->group(function () {

    Route::get('/dashboard', [UserDashboardController::class, 'index'])->name('dashboard');

    Route::get('/appointments', [UserAppointmentController::class, 'index'])->name('appointments');
    Route::post('/appointments', [UserAppointmentController::class, 'store'])->name('appointments.store');

    Route::get('/payments', function () { 
        return view('user.payments'); 
    })->name('payments');

    Route::get('/profile', function () { 
        return view('user.profile'); 
    })->name('profile');
});

Route::prefix('admin')->name('admin.')->middleware('auth:admin')->group(function () {

    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');

    Route::get('/appointments', [AdminAppointmentController::class, 'index'])->name('appointments');
    Route::post('/appointments/{appointment}/approve', [AdminAppointmentController::class, 'approve'])->name('appointments.approve');
    Route::post('/appointments/{appointment}/cancel', [AdminAppointmentController::class, 'cancel'])->name('appointments.cancel');
    Route::get('/appointments/{appointment}', [AdminAppointmentController::class, 'show'])->name('appointments.show');

    Route::delete('/appointments/{appointment}', [AdminAppointmentController::class, 'destroy'])->name('appointments.destroy');

    Route::get('/profile', [AdminController::class, 'profile'])->name('profile');
    Route::post('/profile', [AdminController::class, 'updateProfile'])->name('profile.update');
});

Route::prefix('doctor')
    ->name('doctor.')
    ->middleware('auth:doctor')
    ->group(function () {

        Route::get('/dashboard',
            [DoctorDashboardController::class, 'index']
        )->name('dashboard');

    });

Route::prefix('doctor')->name('doctor.')->middleware('auth:doctor')->group(function () {

    // Dashboard
    Route::get('/dashboard', [DoctorDashboardController::class, 'index'])->name('dashboard');

    // Profile
    Route::get('/profile', [DoctorDashboardController::class, 'profile'])->name('profile');
    Route::post('/profile', [DoctorDashboardController::class, 'updateProfile'])->name('profile.update');

    // Doctor appointments actions
    Route::post('/appointments/{id}/approve', [DoctorDashboardController::class, 'approve'])->name('appointments.approve');
    Route::post('/appointments/{id}/cancel', [DoctorDashboardController::class, 'cancel'])->name('appointments.cancel');
    Route::delete('/appointments/{id}', [DoctorDashboardController::class, 'destroy'])->name('appointments.destroy');

    // Doctor availability
Route::get('/availability', [DoctorDashboardController::class,'availability'])->name('availability');
Route::post('/availability', [DoctorDashboardController::class,'updateAvailability'])->name('availability.update');

});