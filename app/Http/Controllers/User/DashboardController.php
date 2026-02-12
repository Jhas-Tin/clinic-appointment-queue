<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Appointment;

class DashboardController extends Controller
{
    public function index()
    {
        $appointments = Appointment::where('user_id', Auth::id())
                                   ->whereDate('date', '>=', now())
                                   ->orderBy('date', 'asc')
                                   ->get();

        return view('user.dashboard', compact('appointments'));
    }
}
