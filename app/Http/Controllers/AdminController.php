<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Admin;
use App\Models\Appointment;
use Carbon\Carbon;

class AdminController extends Controller
{

    public function loginForm()
    {
        return view('admin.login');
    }

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

public function dashboard()
{
    $now = Carbon::now();

    $todayAppointments = Appointment::whereDate('date', $now->toDateString())
        ->where('status', '!=', 'Cancelled')
        ->orderBy('time')
        ->get()
        ->map(function ($appointment) use ($now) {
            $appointment->dynamic_status = $this->getDynamicStatus($appointment);
            return $appointment;
        });

    $nextAppointment = Appointment::where('status', '!=', 'Cancelled')
        ->whereDate('date', '>=', $now->toDateString())
        ->orderBy('date')
        ->orderBy('time')
        ->get()
        ->filter(function ($appointment) use ($now) {
            $startTime = Carbon::parse($appointment->time)->setDateFrom($appointment->date);
            $endTime = $startTime->copy()->addMinutes(30); 

            return $endTime->greaterThanOrEqualTo($now);
        })
        ->map(function ($appointment) use ($now) {
            $appointment->dynamic_status = $this->getDynamicStatus($appointment);
            return $appointment;
        })
        ->first();

    $appointments = Appointment::latest()->get()
        ->map(function($appointment) use ($now) {
            $appointment->dynamic_status = $this->getDynamicStatus($appointment);
            return $appointment;
        });

    return view('admin.dashboard', compact('todayAppointments', 'nextAppointment', 'appointments'));
}

    public function appointments()
    {
        $appointments = Appointment::latest()->get()
            ->map(function($appointment) {
                $appointment->dynamic_status = $this->getDynamicStatus($appointment);
                return $appointment;
            });

        return view('admin.appointments', compact('appointments'));
    }

    public function showAppointment(Appointment $appointment)
    {
        $appointment->dynamic_status = $this->getDynamicStatus($appointment);
        return view('admin.show-appointment', compact('appointment'));
    }

    public function approveAppointment(Appointment $appointment)
    {
        $appointment->update([
            'status' => 'Approved',
        ]);

        return redirect()->route('admin.appointments')
            ->with('success', 'Appointment approved successfully');
    }

    public function cancelAppointment(Appointment $appointment)
    {
        $appointment->update([
            'status' => 'Cancelled',
        ]);

        return redirect()->route('admin.appointments')
            ->with('success', 'Appointment cancelled successfully');
    }

    /* ================= HELPER: DYNAMIC STATUS ================= */
    private function getDynamicStatus(Appointment $appointment)
    {
        $now = Carbon::now();
        $startTime = Carbon::parse($appointment->time)->setDateFrom($appointment->date);
        $endTime = $startTime->copy()->addMinutes(30); // assuming 30min appointment

        if ($appointment->status == 'Cancelled') {
            return 'Cancelled';
        }

        if ($now->between($startTime, $endTime)) {
            return 'Ongoing';
        }

        return $appointment->status; // Approved, Pending, etc.
    }

    /* ================= PROFILE ================= */

    public function profile()
    {
        $admin = Auth::guard('admin')->user();
        return view('admin.profile', compact('admin'));
    }

    public function updateProfile(Request $request)
    {
        /** @var Admin $admin */
        $admin = Auth::guard('admin')->user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:admins,email,' . $admin->id,
            'password' => 'nullable|confirmed|min:6',
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
        ];

        if ($request->password) {
            $data['password'] = Hash::make($request->password);
        }

        $admin->update($data);

        return redirect()->route('admin.profile')
            ->with('success', 'Profile updated successfully');
    }

    /* ================= LOGOUT ================= */

    public function logout(Request $request)
    {
        Auth::guard('admin')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/admin/login');
    }
}
