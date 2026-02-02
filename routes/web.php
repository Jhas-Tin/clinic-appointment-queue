<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

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
Route::post('/logout', [AuthController::class, 'logout']);

/*
|--------------------------------------------------------------------------
| Dashboards
|--------------------------------------------------------------------------
*/

// User dashboard (protected)
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware('auth');

// Admin dashboard (protected)
Route::get('/admin/dashboard', function () {
    return view('admin.dashboard');
})->middleware('auth:admin');
