<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

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
            // Clean the time field - extract only the time part
            $cleanTime = $this->cleanTime($appointment->time);
            
            try {
                $appointmentDateTime = Carbon::parse(
                    $appointment->date . ' ' . $cleanTime,
                    'Asia/Manila'
                );

                $appointmentEnd = $appointmentDateTime->copy()->addHour();

                if ($now->between($appointmentDateTime, $appointmentEnd)) {
                    $appointment->dynamic_status = 'Ongoing';
                    $currentAppointment = $appointment;
                } elseif ($appointmentDateTime->greaterThan($now) && !$nextAppointment) {
                    $appointment->dynamic_status = 'Upcoming';
                    $nextAppointment = $appointment;
                } else {
                    $appointment->dynamic_status = 'Approved';
                }
            } catch (\Exception $e) {
                // If parsing fails, set default status
                $appointment->dynamic_status = 'Approved';
                Log::error('Failed to parse appointment time: ' . $e->getMessage());
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
                
            if ($nextAppointment) {
                $nextAppointment->dynamic_status = 'Upcoming';
            }
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
            
        // Clean times for upcoming appointments
        foreach ($upcomingAppointments as $appointment) {
            $appointment->clean_time = $this->cleanTime($appointment->time);
        }

        // Recent appointment requests
        $appointments = Appointment::latest()->take(10)->get();

        // Clean times for recent appointments
        foreach ($appointments as $appointment) {
            $appointment->clean_time = $this->cleanTime($appointment->time);
        }

        return view('admin.dashboard', compact(
            'todayAppointments',
            'currentAppointment',
            'nextAppointment',
            'upcomingAppointments',
            'appointments',
            'now'
        ));
    }
    
    /**
     * Clean time string - extract only time part if it contains date
     */
    private function cleanTime($time)
    {
        if (!$time) {
            return null;
        }
        
        // If time contains a space, it might be a full datetime string
        if (str_contains($time, ' ')) {
            $timeParts = explode(' ', $time);
            // Get the last part (should be the time)
            $cleanTime = end($timeParts);
            
            // Remove any extra characters
            $cleanTime = trim($cleanTime);
            
            // If it's a valid time format (HH:MM:SS or HH:MM), return it
            if (preg_match('/^(\d{1,2}):(\d{2})(:(\d{2}))?$/', $cleanTime)) {
                return $cleanTime;
            }
        }
        
        // If it's already a time string, return as is
        if (preg_match('/^(\d{1,2}):(\d{2})(:(\d{2}))?$/', $time)) {
            return $time;
        }
        
        // Fallback: try to parse with Carbon and extract time
        try {
            $parsed = Carbon::parse($time);
            return $parsed->format('H:i:s');
        } catch (\Exception $e) {
            return $time;
        }
    }
    
    /**
     * Get formatted time for display (12-hour format)
     */
    private function formatTime($time)
    {
        $cleanTime = $this->cleanTime($time);
        
        if (!$cleanTime) {
            return null;
        }
        
        try {
            return Carbon::parse($cleanTime)->format('h:i A');
        } catch (\Exception $e) {
            return $cleanTime;
        }
    }
}