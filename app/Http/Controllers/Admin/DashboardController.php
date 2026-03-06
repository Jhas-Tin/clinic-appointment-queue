<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $now = Carbon::now('Asia/Manila');

        // Get today's approved appointments
        $todayAppointments = Appointment::whereDate('date', $now->toDateString())
            ->where('status', 'Approved')
            ->orderBy('time')
            ->get();

        $currentAppointment = null;
        $nextAppointment = null;

        foreach ($todayAppointments as $appointment) {

            $appointmentDateTime = Carbon::parse(
                $appointment->date . ' ' . $appointment->time,
                'Asia/Manila'
            );

            $appointmentEnd = $appointmentDateTime->copy()->addHour();

            // Detect ongoing appointment
            if ($now->between($appointmentDateTime, $appointmentEnd)) {
                $appointment->dynamic_status = 'Ongoing';
                $currentAppointment = $appointment;
            }
            // Detect next appointment
            elseif ($appointmentDateTime->greaterThan($now) && !$nextAppointment) {
                $appointment->dynamic_status = 'Upcoming';
                $nextAppointment = $appointment;
            } else {
                $appointment->dynamic_status = 'Approved';
            }
        }

        // If no ongoing appointment yet (example 7:30 but first is 8:00)
        if (!$currentAppointment) {
            $nextAppointment = $todayAppointments->first();
        }

        // --- NEW: get all upcoming appointments after the ongoing ---
        if ($currentAppointment) {
            $upcomingAppointments = $todayAppointments->filter(function($appt) use ($currentAppointment) {
                return Carbon::parse($appt->date . ' ' . $appt->time, 'Asia/Manila')
                       ->greaterThan(Carbon::parse($currentAppointment->date . ' ' . $currentAppointment->time, 'Asia/Manila'));
            });
        } else {
            $upcomingAppointments = $todayAppointments;
        }

        // Recent appointment requests
        $appointments = Appointment::latest()->get();

        return view('admin.dashboard', compact(
            'todayAppointments',
            'currentAppointment',
            'nextAppointment',
            'upcomingAppointments',
            'appointments'
        ));
    }
}