@extends('layouts.user')

@section('title', 'Dashboard')

@section('content')
<div class="space-y-6">

    <!-- Upcoming Appointments -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        
        <!-- Calendar -->
        <div class="bg-white rounded-2xl p-6 shadow">
            <h3 class="font-semibold text-gray-800 mb-4">Appointments Calendar</h3>

            @php
                $month = now()->month;
                $year = now()->year;
                $firstDay = \Carbon\Carbon::createFromDate($year, $month, 1);
                $daysInMonth = $firstDay->daysInMonth;
                $startDay = $firstDay->dayOfWeekIso; // Monday = 1
                $appointmentsByDate = $appointments->groupBy(function($item) {
                    return \Carbon\Carbon::parse($item->date)->format('Y-m-d');
                });
            @endphp

            <!-- Days of week -->
            <div class="grid grid-cols-7 gap-2 text-center text-gray-500 font-semibold">
                <div>Mo</div>
                <div>Tu</div>
                <div>We</div>
                <div>Th</div>
                <div>Fr</div>
                <div>Sa</div>
                <div>Su</div>
            </div>

            <!-- Dates -->
            <div class="grid grid-cols-7 gap-2 mt-2 text-center">
                {{-- empty cells before first day --}}
                @for($i = 1; $i < $startDay; $i++)
                    <div></div>
                @endfor

                {{-- days of the month --}}
                @for($day = 1; $day <= $daysInMonth; $day++)
                    @php
                        $dateKey = \Carbon\Carbon::createFromDate($year, $month, $day)->format('Y-m-d');
                        $hasAppointment = $appointmentsByDate->has($dateKey);
                        $dayAppointments = $hasAppointment ? $appointmentsByDate[$dateKey] : collect();
                    @endphp
                    <div class="relative group">
                        <div class="p-2 rounded-full {{ $hasAppointment ? 'bg-blue-200 font-bold' : '' }}">
                            {{ $day }}
                        </div>

                        @if($hasAppointment)
                            <span class="absolute top-0 right-0 w-2 h-2 bg-blue-600 rounded-full"></span>
                            
                            {{-- Tooltip --}}
                            <div class="absolute left-1/2 transform -translate-x-1/2 -translate-y-full bg-white border shadow-lg rounded-lg p-2 text-xs text-gray-800 z-10 opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none">
                                @foreach($dayAppointments as $appt)
                                    <div class="border-b last:border-b-0 pb-1 mb-1 last:mb-0">
                                        <p>Dr. {{ $appt->doctor_name }}</p>
                                        <p class="text-gray-500 text-xs">{{ \Carbon\Carbon::parse($appt->time)->format('h:i A') }}</p>
                                        <p class="text-gray-500 text-xs">{{ $appt->status }}</p>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endfor
            </div>
        </div>

        <!-- Upcoming Appointments List -->
        <div class="bg-white rounded-2xl p-6 shadow space-y-4">
            <h3 class="font-semibold text-gray-800 mb-2">Upcoming Appointments</h3>
            @forelse($appointments as $appointment)
                <div class="bg-gray-50 p-4 rounded-xl flex justify-between items-center shadow-sm">
                    <div>
                        <p class="font-medium text-gray-800">Dr. {{ $appointment->doctor_name }}</p>
                        <p class="text-gray-500 text-sm">{{ \Carbon\Carbon::parse($appointment->date)->format('d M, Y') }} - {{ \Carbon\Carbon::parse($appointment->time)->format('h:i A') }}</p>
                    </div>
                    <span class="px-3 py-1 text-xs rounded-full
                        {{ $appointment->status == 'Approved' ? 'bg-blue-100 text-blue-600' : '' }}
                        {{ $appointment->status == 'Pending' ? 'bg-yellow-100 text-yellow-600' : '' }}
                        {{ $appointment->status == 'Cancelled' ? 'bg-red-100 text-red-600' : '' }}">
                        {{ $appointment->status }}
                    </span>
                </div>
            @empty
                <p class="text-center text-gray-500">No upcoming appointments</p>
            @endforelse
        </div>
    </div>

    <!-- Clinic Queue Status -->
    <div class="bg-white rounded-2xl p-6 shadow">
        <h3 class="font-semibold text-gray-800 mb-4">Current Clinic Queue</h3>
        @php
            $queue = $queues ?? collect(); // Assuming $queues contains the current queue
        @endphp
        @if($queue->isNotEmpty())
            <ul class="divide-y divide-gray-200">
                @foreach($queue as $q)
                    <li class="py-3 flex justify-between items-center">
                        <div>
                            <p class="font-medium text-gray-800">{{ $q->patient_name }}</p>
                            <p class="text-gray-500 text-sm">Dr. {{ $q->doctor_name }} - {{ $q->appointment_type }}</p>
                        </div>
                        <span class="px-3 py-1 text-xs rounded-full {{ $q->status == 'Waiting' ? 'bg-yellow-100 text-yellow-600' : 'bg-green-100 text-green-600' }}">
                            {{ $q->status }}
                        </span>
                    </li>
                @endforeach
            </ul>
        @else
            <p class="text-center text-gray-500">No patients in the queue</p>
        @endif
    </div>

</div>
@endsection
