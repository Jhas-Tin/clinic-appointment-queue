<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Admin;
use App\Models\Appointment;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    /**
     * Show admin login form
     *
     * @return \Illuminate\View\View
     */
    public function loginForm()
    {
        return view('admin.login');
    }

    /**
     * Handle admin login
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::guard('admin')->attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->route('admin.dashboard');
        }

        return back()->withErrors([
            'email' => 'Invalid credentials!',
        ]);
    }

    /**
     * Show admin dashboard
     *
     * @return \Illuminate\View\View
     */
    public function dashboard()
    {
        // Example chart data
        $totalPatients = 100;
        $oldPatients = 40;
        $newPatients = 60;

        // Example: Today's appointments
        $todayAppointments = Appointment::whereDate('appointment_date', now())->get();

        return view('admin.dashboard', compact('totalPatients', 'oldPatients', 'newPatients', 'todayAppointments'));
    }

    /**
     * Show all appointments
     *
     * @return \Illuminate\View\View
     */
    public function appointments()
    {
        $appointments = Appointment::latest()->get();
        return view('admin.appointments', compact('appointments'));
    }

    /**
     * Show single appointment
     *
     * @param \App\Models\Appointment $appointment
     * @return \Illuminate\View\View
     */
    public function showAppointment(Appointment $appointment)
    {
        return view('admin.show-appointment', compact('appointment'));
    }

    /**
     * Approve an appointment
     *
     * @param \App\Models\Appointment $appointment
     * @return \Illuminate\Http\RedirectResponse
     */
    public function approveAppointment(Appointment $appointment)
    {
        $appointment->status = 'approved';
        $appointment->save();

        return redirect()->route('admin.appointments')->with('success', 'Appointment approved');
    }

    /**
     * Cancel an appointment
     *
     * @param \App\Models\Appointment $appointment
     * @return \Illuminate\Http\RedirectResponse
     */
    public function cancelAppointment(Appointment $appointment)
    {
        $appointment->status = 'cancelled';
        $appointment->save();

        return redirect()->route('admin.appointments')->with('success', 'Appointment cancelled');
    }

    /**
     * Show admin profile page
     *
     * @return \Illuminate\View\View
     */
    public function profile()
    {
        /** @var Admin $admin */
        $admin = Auth::guard('admin')->user();
        return view('admin.profile', compact('admin'));
    }

    /**
     * Update admin profile
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateProfile(Request $request)
    {
        /** @var Admin $admin */
        $admin = Auth::guard('admin')->user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:admins,email,' . $admin->id,
            'password' => 'nullable|confirmed|min:6',
        ]);

        $admin->name = $request->name;
        $admin->email = $request->email;

        if ($request->password) {
            $admin->password = Hash::make($request->password);
        }

        $admin->save();

        return redirect()->route('admin.profile')->with('success', 'Profile updated successfully');
    }

    /**
     * Admin logout
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout(Request $request)
    {
        Auth::guard('admin')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/admin/login');
    }
}
