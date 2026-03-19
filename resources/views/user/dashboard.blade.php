@extends('layouts.user')

@section('title', 'Dashboard')

@section('content')
<div class="space-y-6">

    <!-- WELCOME SECTION -->
    <div class="bg-gradient-to-r from-blue-600 to-blue-700 rounded-xl shadow-lg p-6 text-white">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold">Welcome back!</h2>
                <p class="text-blue-100 mt-1">{{ now()->format('l, F d, Y') }}</p>
            </div>
            <div class="w-16 h-16 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                <i class="fa fa-user-circle text-3xl"></i>
            </div>
        </div>
    </div>

    <!-- STATS CARDS -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Total Appointments Card -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-100">
            <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-5 py-3">
                <h3 class="text-sm font-semibold text-white flex items-center">
                    <i class="fa fa-calendar-check mr-2"></i>
                    Total Consultations
                </h3>
            </div>
            <div class="p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-3xl font-bold text-gray-800">{{ $appointments->count() }}</p>
                        <p class="text-xs text-gray-500 mt-1">All time appointments</p>
                    </div>
                    <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                        <i class="fa fa-calendar text-blue-600 text-xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pending Appointments Card -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-100">
            <div class="bg-gradient-to-r from-yellow-500 to-yellow-600 px-5 py-3">
                <h3 class="text-sm font-semibold text-white flex items-center">
                    <i class="fa fa-hourglass-half mr-2"></i>
                    Pending
                </h3>
            </div>
            <div class="p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-3xl font-bold text-yellow-600">{{ $appointments->where('status','Pending')->count() }}</p>
                        <p class="text-xs text-gray-500 mt-1">Awaiting approval</p>
                    </div>
                    <div class="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center">
                        <i class="fa fa-clock text-yellow-600 text-xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Approved Appointments Card -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-100">
            <div class="bg-gradient-to-r from-green-600 to-green-700 px-5 py-3">
                <h3 class="text-sm font-semibold text-white flex items-center">
                    <i class="fa fa-check-circle mr-2"></i>
                    Approved
                </h3>
            </div>
            <div class="p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-3xl font-bold text-green-600">{{ $appointments->where('status','Approved')->count() }}</p>
                        <p class="text-xs text-gray-500 mt-1">Confirmed appointments</p>
                    </div>
                    <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                        <i class="fa fa-thumbs-up text-green-600 text-xl"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- MAIN CONTENT GRID -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        
        <!-- Calendar Card -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-100">
            <div class="bg-gradient-to-r from-indigo-600 to-indigo-700 px-6 py-4">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-white flex items-center">
                        <i class="fa fa-calendar mr-2"></i>
                        Appointments Calendar
                    </h3>
                    <span class="text-xs bg-white bg-opacity-20 text-white px-3 py-1 rounded-full">
                        {{ now()->format('F Y') }}
                    </span>
                </div>
            </div>

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

            <div class="p-6">
                <!-- Days of week -->
                <div class="grid grid-cols-7 gap-2 mb-2">
                    @foreach(['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'] as $day)
                        <div class="text-center text-xs font-semibold text-gray-500 py-2">{{ $day }}</div>
                    @endforeach
                </div>

                <!-- Calendar Grid -->
                <div class="grid grid-cols-7 gap-2">
                    {{-- empty cells before first day --}}
                    @for($i = 1; $i < $startDay; $i++)
                        <div class="aspect-square"></div>
                    @endfor

                    {{-- days of the month --}}
                    @for($day = 1; $day <= $daysInMonth; $day++)
                        @php
                            $dateKey = \Carbon\Carbon::createFromDate($year, $month, $day)->format('Y-m-d');
                            $hasAppointment = $appointmentsByDate->has($dateKey);
                            $dayAppointments = $hasAppointment ? $appointmentsByDate[$dateKey] : collect();
                            $isToday = $day == now()->day;
                        @endphp
                        
                        <div class="relative group">
                            <div class="aspect-square flex flex-col items-center justify-center rounded-lg border-2 transition-all duration-200
                                        {{ $isToday ? 'border-indigo-500 bg-indigo-50' : 'border-gray-100 hover:border-indigo-200 hover:bg-indigo-50/50' }}
                                        {{ $hasAppointment ? 'cursor-pointer' : '' }}">
                                <span class="text-sm font-medium {{ $isToday ? 'text-indigo-700' : 'text-gray-700' }}">{{ $day }}</span>
                                
                                @if($hasAppointment)
                                    <span class="mt-0.5 w-4 h-4 bg-indigo-600 text-white text-[8px] font-semibold rounded-full flex items-center justify-center">
                                        {{ $dayAppointments->count() }}
                                    </span>
                                @endif
                            </div>

                            @if($hasAppointment)
                                {{-- Tooltip --}}
                                <div class="absolute bottom-full mb-2 left-1/2 transform -translate-x-1/2 w-48 bg-gray-800 text-white text-xs rounded-lg p-2 opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none z-50 shadow-xl">
                                    <p class="font-semibold mb-1 border-b border-gray-600 pb-1">{{ now()->format('F') }} {{ $day }}</p>
                                    @foreach($dayAppointments as $appt)
                                        <div class="flex justify-between items-center py-1 border-b border-gray-700 last:border-b-0">
                                            <div>
                                                <p class="font-medium">Dr. {{ $appt->doctor_name }}</p>
                                                <p class="text-gray-300 text-[10px]">{{ \Carbon\Carbon::parse($appt->time)->format('h:i A') }}</p>
                                            </div>
                                            <span class="text-[8px] px-1.5 py-0.5 rounded-full
                                                {{ $appt->status == 'Approved' ? 'bg-green-500 text-white' : '' }}
                                                {{ $appt->status == 'Pending' ? 'bg-yellow-500 text-white' : '' }}
                                                {{ $appt->status == 'Cancelled' ? 'bg-red-500 text-white' : '' }}">
                                                {{ $appt->status }}
                                            </span>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @endfor
                </div>

                <!-- Legend -->
                <div class="flex items-center justify-end gap-4 mt-6 pt-4 border-t border-gray-100">
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 bg-indigo-600 rounded-full"></span>
                        <span class="text-xs text-gray-600">Has appointments</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 border-2 border-indigo-500 bg-indigo-50 rounded-full"></span>
                        <span class="text-xs text-gray-600">Today</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Upcoming Appointments Card -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-100">
            <div class="bg-gradient-to-r from-purple-600 to-purple-700 px-6 py-4">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-white flex items-center">
                        <i class="fa fa-calendar-alt mr-2"></i>
                        Upcoming Consultations
                    </h3>
                    <span class="text-xs bg-white bg-opacity-20 text-white px-3 py-1 rounded-full">
                        Next appointments
                    </span>
                </div>
            </div>
            
            <div class="p-6">
                <!-- Scrollable container -->
                <div class="space-y-3 max-h-96 overflow-y-auto pr-2">
                    @forelse($appointments->sortBy('date')->take(10) as $appointment)
                        <div class="group bg-gradient-to-r from-gray-50 to-white rounded-xl p-4 border border-gray-100 hover:shadow-md transition-all duration-200 hover:border-purple-200">
                            <div class="flex items-start justify-between">
                                <div class="flex items-start gap-3">
                                    <div class="w-10 h-10 bg-gradient-to-br from-purple-100 to-purple-200 rounded-full flex items-center justify-center flex-shrink-0">
                                        <i class="fa fa-user-md text-purple-600"></i>
                                    </div>
                                    <div>
                                        <p class="font-semibold text-gray-900">Dr. {{ $appointment->doctor_name }}</p>
                                        <div class="flex items-center gap-3 mt-1">
                                            <span class="text-xs text-gray-500 flex items-center">
                                                <i class="fa fa-calendar-alt mr-1 text-gray-400"></i>
                                                {{ \Carbon\Carbon::parse($appointment->date)->format('M d, Y') }}
                                            </span>
                                            <span class="text-xs text-gray-500 flex items-center">
                                                <i class="fa fa-clock mr-1 text-gray-400"></i>
                                                {{ \Carbon\Carbon::parse($appointment->time)->format('h:i A') }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                
                                @php
                                    $statusColors = [
                                        'Approved' => 'bg-green-100 text-green-700 border-green-200',
                                        'Pending' => 'bg-yellow-100 text-yellow-700 border-yellow-200',
                                        'Cancelled' => 'bg-red-100 text-red-700 border-red-200',
                                    ];
                                    $statusIcons = [
                                        'Approved' => 'fa-check-circle',
                                        'Pending' => 'fa-hourglass-half',
                                        'Cancelled' => 'fa-times-circle',
                                    ];
                                    $color = $statusColors[$appointment->status] ?? 'bg-gray-100 text-gray-700';
                                    $icon = $statusIcons[$appointment->status] ?? 'fa-circle';
                                @endphp
                                
                                <span class="px-3 py-1.5 rounded-full text-xs font-semibold flex items-center border {{ $color }}">
                                    <i class="fa {{ $icon }} mr-1.5"></i>
                                    {{ $appointment->status }}
                                </span>
                            </div>
                            
                            @if($appointment->status == 'Approved')
                                <div class="mt-3 flex items-center gap-2 text-xs text-purple-600 bg-purple-50 p-2 rounded-lg">
                                    <i class="fa fa-info-circle"></i>
                                    <span>Consultation has been accepted</span>
                                </div>
                            @elseif($appointment->status == 'Pending')
                                <div class="mt-3 flex items-center gap-2 text-xs text-yellow-600 bg-yellow-50 p-2 rounded-lg">
                                    <i class="fa fa-clock"></i>
                                    <span>Waiting for doctor's approval</span>
                                </div>
                            @endif
                        </div>
                    @empty
                        <div class="flex flex-col items-center justify-center py-12 text-gray-400">
                            <i class="fa fa-calendar-times text-5xl mb-3"></i>
                            <p class="text-lg font-medium">No appointments found</p>
                            <p class="text-sm">Book your first consultation to get started</p>
                            <a href="{{ route('user.appointments') }}" class="mt-4 px-4 py-2 bg-gradient-to-r from-purple-600 to-purple-700 text-white rounded-lg text-sm hover:from-purple-700 hover:to-purple-800 transition">
                                Book Now
                            </a>
                        </div>
                    @endforelse
                </div>
            </div>
            
            @if($appointments->count() > 0)
            <div class="bg-gray-50 px-6 py-3 border-t border-gray-200">
                <div class="flex items-center justify-between text-xs text-gray-500">
                    <span>Showing <span class="font-medium">{{ min(10, $appointments->count()) }}</span> of <span class="font-medium">{{ $appointments->count() }}</span> appointments</span>
                    <a href="{{ route('user.appointments') }}" class="text-purple-600 hover:text-purple-700 font-medium flex items-center gap-1">
                        View all <i class="fa fa-arrow-right text-xs"></i>
                    </a>
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- Quick Actions -->
    {{-- <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <a href="{{ route('user.appointments') }}" class="group bg-gradient-to-r from-blue-500 to-blue-600 rounded-xl shadow-lg p-5 text-white hover:from-blue-600 hover:to-blue-700 transition-all transform hover:scale-105">
            <div class="flex items-center justify-between">
                <div>
                    <i class="fa fa-calendar-plus text-2xl mb-2"></i>
                    <h4 class="font-semibold">Book Appointment</h4>
                    <p class="text-xs text-blue-100 mt-1">Schedule a new consultation</p>
                </div>
                <i class="fa fa-arrow-right text-xl group-hover:translate-x-1 transition-transform"></i>
            </div>
        </a>
            
    </div> --}}

</div>

<style>
    /* Custom scrollbar for upcoming appointments */
    .overflow-y-auto::-webkit-scrollbar {
        width: 4px;
    }
    
    .overflow-y-auto::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 4px;
    }
    
    .overflow-y-auto::-webkit-scrollbar-thumb {
        background: #cbd5e0;
        border-radius: 4px;
    }
    
    .overflow-y-auto::-webkit-scrollbar-thumb:hover {
        background: #a0aec0;
    }
</style>
@endsection