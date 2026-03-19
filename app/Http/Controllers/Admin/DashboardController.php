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

            if ($now->between($appointmentDateTime, $appointmentEnd)) {
                $appointment->dynamic_status = 'Ongoing';
                $currentAppointment = $appointment;
            }
            elseif ($appointmentDateTime->greaterThan($now) && !$nextAppointment) {
                $appointment->dynamic_status = 'Upcoming';
                $nextAppointment = $appointment;
            } 
            else {
                $appointment->dynamic_status = 'Approved';
            }
        }

        // If no ongoing appointment and no next appointment today
        if (!$currentAppointment && !$nextAppointment) {

            // Get the next appointment from future dates
            $nextAppointment = Appointment::where('status', 'Approved')
                ->whereDate('date', '>', $now->toDateString())
                ->orderBy('date')
                ->orderBy('time')
                ->first();
        }

        // Upcoming appointments list
        $upcomingAppointments = Appointment::where('status', 'Approved')
            ->where(function ($query) use ($now) {

                // Future dates
                $query->whereDate('date', '>', $now->toDateString())

                // Today but future time
                ->orWhere(function ($q) use ($now) {
                    $q->whereDate('date', $now->toDateString())
                      ->whereTime('time', '>', $now->toTimeString());
                });

            })
            ->orderBy('date')
            ->orderBy('time')
            ->get();

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