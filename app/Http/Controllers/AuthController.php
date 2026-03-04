<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Admin;
use App\Models\Doctor; // ✅ added
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function registerForm()
    {
        return view('auth.register');
    }


    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return redirect('/login')->with('success', 'Account created successfully!');
    }


    public function loginForm()
    {
        return view('auth.login');
    }


    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // ✅ ADMIN LOGIN
        if (Auth::guard('admin')->attempt($credentials)) {
            $request->session()->regenerate();
            return redirect('/admin/dashboard');
        }

        // ✅ DOCTOR LOGIN (added only)
        if (Auth::guard('doctor')->attempt($credentials)) {
            $request->session()->regenerate();
            return redirect('/doctor/dashboard');
        }

        // ✅ USER LOGIN
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return redirect('/dashboard');
        }

        return back()->withErrors([
            'email' => 'Invalid credentials!',
        ]);
    }


    public function logout(Request $request)
    {
        // admin logout
        if (Auth::guard('admin')->check()) {
            Auth::guard('admin')->logout();
        }

        // doctor logout
        elseif (Auth::guard('doctor')->check()) {
            Auth::guard('doctor')->logout();
        }

        // user logout
        else {
            Auth::logout();
        }

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}