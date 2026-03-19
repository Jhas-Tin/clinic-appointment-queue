@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="space-y-6">

    <!-- STATS CARDS ROW -->
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
                        <p class="text-xs text-gray-500 mt-1">All time consultations</p>
                    </div>
                    <div class="w-14 h-14 bg-blue-100 rounded-full flex items-center justify-center">
                        <i class="fa fa-calendar text-blue-600 text-2xl"></i>
                    </div>
                </div>
                <div class="mt-3 text-xs text-gray-500 flex items-center">
                    <i class="fa fa-arrow-up text-green-500 mr-1"></i>
                    <span>{{ $todayAppointments->count() }} today</span>
                </div>
            </div>
        </div>

        <!-- Today's Consultations Card -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-100">
            <div class="bg-gradient-to-r from-green-600 to-green-700 px-5 py-3">
                <h3 class="text-sm font-semibold text-white flex items-center">
                    <i class="fa fa-clock mr-2"></i>
                    Today's Consultations
                </h3>
            </div>
            <div class="p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-3xl font-bold text-gray-800">{{ $todayAppointments->count() }}</p>
                        <p class="text-xs text-gray-500 mt-1">{{ now()->format('l, F d, Y') }}</p>
                    </div>
                    <div class="w-14 h-14 bg-green-100 rounded-full flex items-center justify-center">
                        <i class="fa fa-calendar-day text-green-600 text-2xl"></i>
                    </div>
                </div>
                <div class="mt-3 flex gap-2">
                    @php
                        $ongoingCount = $todayAppointments->where('dynamic_status', 'Ongoing')->count();
                        $upcomingCount = $todayAppointments->where('dynamic_status', 'Upcoming')->count();
                    @endphp
                    <span class="text-xs bg-blue-100 text-blue-700 px-2 py-1 rounded-full flex items-center">
                        <span class="w-1.5 h-1.5 bg-blue-500 rounded-full mr-1"></span>
                        {{ $ongoingCount }} Ongoing
                    </span>
                    <span class="text-xs bg-yellow-100 text-yellow-700 px-2 py-1 rounded-full flex items-center">
                        <span class="w-1.5 h-1.5 bg-yellow-500 rounded-full mr-1"></span>
                        {{ $upcomingCount }} Upcoming
                    </span>
                </div>
            </div>
        </div>

        <!-- Next Appointment Card -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-100">
            <div class="bg-gradient-to-r from-purple-600 to-purple-700 px-5 py-3">
                <h3 class="text-sm font-semibold text-white flex items-center">
                    <i class="fa fa-arrow-right mr-2"></i>
                    Next Appointment
                </h3>
            </div>
            <div class="p-5">
                @if($nextAppointment)
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center">
                            <span class="text-purple-600 font-bold text-lg">{{ substr($nextAppointment->patient_name, 0, 1) }}</span>
                        </div>
                        <div class="flex-1">
                            <p class="font-semibold text-gray-900">{{ $nextAppointment->patient_name }}</p>
                            <p class="text-xs text-gray-500">{{ $nextAppointment->doctor_name }}</p>
                            <div class="flex items-center gap-2 mt-1">
                                <span class="text-xs bg-purple-100 text-purple-700 px-2 py-0.5 rounded-full">
                                    <i class="fa fa-calendar-alt mr-1"></i>{{ \Carbon\Carbon::parse($nextAppointment->date)->format('M d') }}
                                </span>
                                <span class="text-xs bg-purple-100 text-purple-700 px-2 py-0.5 rounded-full">
                                    <i class="fa fa-clock mr-1"></i>{{ \Carbon\Carbon::parse($nextAppointment->time)->format('h:i A') }}
                                </span>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="flex flex-col items-center justify-center py-4 text-gray-400">
                        <i class="fa fa-calendar-times text-3xl mb-2"></i>
                        <p class="text-sm">No upcoming appointments</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- MAIN CONTENT GRID -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Today's Consultations List (Left Column) -->
        <div class="lg:col-span-2 bg-white rounded-xl shadow-lg overflow-hidden border border-gray-100">
            <div class="bg-gradient-to-r from-gray-700 to-gray-800 px-6 py-4">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-white flex items-center">
                        <i class="fa fa-calendar-day mr-2"></i>
                        Today's Consultations
                    </h3>
                    <span class="text-xs bg-white bg-opacity-20 text-white px-3 py-1 rounded-full">
                        {{ $todayAppointments->count() }} total
                    </span>
                </div>
            </div>
            
            <div class="p-6">
                <!-- Ongoing Section -->
                @if($todayAppointments->where('dynamic_status', 'Ongoing')->count() > 0)
                    <div class="mb-6">
                        <h4 class="text-sm font-semibold text-blue-600 mb-3 flex items-center">
                            <span class="w-2 h-2 bg-blue-500 rounded-full mr-2"></span>
                            Ongoing Now
                        </h4>
                        <div class="space-y-3">
                            @foreach($todayAppointments->where('dynamic_status', 'Ongoing') as $appointment)
                                <div class="bg-gradient-to-r from-blue-50 to-white rounded-xl p-4 border border-blue-100">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-3">
                                            <img src="{{ $appointment->user?->avatar ?? 'https://via.placeholder.com/100' }}" 
                                                 class="w-12 h-12 rounded-full object-cover border-2 border-blue-200">
                                            <div>
                                                <p class="font-semibold text-gray-900">{{ $appointment->patient_name }}</p>
                                                <p class="text-sm text-gray-600">{{ $appointment->doctor_name }}</p>
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <span class="inline-flex items-center px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-xs font-semibold">
                                                <span class="w-2 h-2 bg-blue-500 rounded-full mr-1 animate-pulse"></span>
                                                {{ \Carbon\Carbon::parse($appointment->time)->format('h:i A') }}
                                            </span>
                                            <p class="text-xs text-gray-500 mt-1">Ongoing</p>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Upcoming Section -->
                @if($todayAppointments->where('dynamic_status', 'Upcoming')->count() > 0)
                    <div>
                        <h4 class="text-sm font-semibold text-yellow-600 mb-3 flex items-center">
                            <span class="w-2 h-2 bg-yellow-500 rounded-full mr-2"></span>
                            Upcoming Today
                        </h4>
                        <div class="space-y-3">
                            @foreach($todayAppointments->where('dynamic_status', 'Upcoming') as $appointment)
                                <div class="bg-white rounded-xl p-4 border border-gray-100 hover:shadow-md transition">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-3">
                                            <img src="{{ $appointment->user?->avatar ?? 'https://via.placeholder.com/100' }}" 
                                                 class="w-10 h-10 rounded-full object-cover">
                                            <div>
                                                <p class="font-semibold text-gray-900">{{ $appointment->patient_name }}</p>
                                                <p class="text-sm text-gray-600">{{ $appointment->doctor_name }}</p>
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <span class="font-mono font-semibold text-gray-700">
                                                {{ \Carbon\Carbon::parse($appointment->time)->format('h:i A') }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if($todayAppointments->count() == 0)
                    <div class="flex flex-col items-center justify-center py-12 text-gray-400">
                        <i class="fa fa-calendar-times text-5xl mb-3"></i>
                        <p class="text-lg font-medium">No consultations today</p>
                        <p class="text-sm">Schedule a consultation to get started</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Upcoming Appointments (Right Column) -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-100">
            <div class="bg-gradient-to-r from-purple-600 to-purple-700 px-6 py-4">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-white flex items-center">
                        <i class="fa fa-calendar-alt mr-2"></i>
                        Upcoming Appointments
                    </h3>
                    <span class="text-xs bg-white bg-opacity-20 text-white px-3 py-1 rounded-full">
                        Next 7 days
                    </span>
                </div>
            </div>
            
            <div class="p-6 max-h-[500px] overflow-y-auto">
                @if($upcomingAppointments && $upcomingAppointments->count() > 0)
                    <div class="space-y-4">
                        @foreach($upcomingAppointments->take(5) as $appointment)
                            <div class="border-l-4 {{ $appointment->dynamic_status == 'Upcoming' ? 'border-green-500' : 'border-blue-500' }} pl-4 py-2 hover:bg-gray-50 transition rounded-r-lg">
                                <div class="flex items-start justify-between">
                                    <div>
                                        <p class="font-semibold text-gray-900">{{ $appointment->patient_name }}</p>
                                        <p class="text-sm text-gray-600">{{ $appointment->doctor_name }}</p>
                                        <div class="flex items-center gap-3 mt-2">
                                            <span class="text-xs bg-gray-100 text-gray-700 px-2 py-1 rounded-full">
                                                <i class="fa fa-calendar-alt mr-1"></i>{{ \Carbon\Carbon::parse($appointment->date)->format('M d, Y') }}
                                            </span>
                                            <span class="text-xs bg-gray-100 text-gray-700 px-2 py-1 rounded-full">
                                                <i class="fa fa-clock mr-1"></i>{{ \Carbon\Carbon::parse($appointment->time)->format('h:i A') }}
                                            </span>
                                        </div>
                                    </div>
                                    @php
                                        $badgeColor = match($appointment->dynamic_status) {
                                            'Upcoming' => 'bg-green-100 text-green-700',
                                            'Approved' => 'bg-blue-100 text-blue-700',
                                            'Pending' => 'bg-yellow-100 text-yellow-700',
                                            default => 'bg-gray-100 text-gray-700',
                                        };
                                    @endphp
                                    <span class="text-xs px-2 py-1 rounded-full {{ $badgeColor }}">
                                        {{ $appointment->dynamic_status }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    
                    @if($upcomingAppointments->count() > 5)
                        <div class="mt-4 text-center">
                            <a href="{{ route('admin.appointments') }}" class="text-sm text-purple-600 hover:text-purple-700 font-medium">
                                View all {{ $upcomingAppointments->count() }} appointments <i class="fa fa-arrow-right ml-1"></i>
                            </a>
                        </div>
                    @endif
                @else
                    <div class="flex flex-col items-center justify-center py-12 text-gray-400">
                        <i class="fa fa-calendar-plus text-5xl mb-3"></i>
                        <p class="text-lg font-medium">No upcoming appointments</p>
                        <p class="text-sm">Schedule a new appointment</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- CALENDAR SECTION -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-100">
        <div class="bg-gradient-to-r from-indigo-600 to-indigo-700 px-6 py-4">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-white flex items-center">
                    <i class="fa fa-calendar mr-2"></i>
                    Monthly Calendar
                </h3>
                <span class="text-xs bg-white bg-opacity-20 text-white px-3 py-1 rounded-full">
                    {{ now()->format('F Y') }}
                </span>
            </div>
        </div>
        
        <div class="p-6">
            <!-- Calendar Grid -->
            <div class="grid grid-cols-7 gap-2">
                <!-- Weekday Headers -->
                @foreach(['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'] as $day)
                    <div class="text-center text-xs font-semibold text-gray-600 py-2">{{ $day }}</div>
                @endforeach

                <!-- Calendar Dates -->
                @php
                    $firstDay = now()->startOfMonth();
                    $lastDay = now()->endOfMonth();
                    $daysInMonth = now()->daysInMonth;
                    $startingOffset = $firstDay->dayOfWeek;
                @endphp

                {{-- Empty cells for days before month starts --}}
                @for($i = 0; $i < $startingOffset; $i++)
                    <div class="aspect-square bg-gray-50 rounded-lg"></div>
                @endfor

                {{-- Actual days of the month --}}
                @for($day = 1; $day <= $daysInMonth; $day++)
                    @php
                        $dateStr = now()->format('Y-m-') . str_pad($day, 2, '0', STR_PAD_LEFT);
                        $appointmentsForDate = $appointments->where('date', $dateStr);
                        $appointmentsCount = $appointmentsForDate->count();
                        $isToday = $day == now()->day;
                    @endphp

                    <div class="aspect-square relative group">
                        <div class="w-full h-full flex flex-col items-center justify-center rounded-lg border-2 transition-all duration-200
                                    {{ $isToday ? 'border-indigo-500 bg-indigo-50' : 'border-gray-100 hover:border-indigo-200 hover:bg-indigo-50/50' }}
                                    {{ $appointmentsCount > 0 ? 'cursor-pointer' : '' }}">
                            <span class="text-sm font-medium {{ $isToday ? 'text-indigo-700' : 'text-gray-700' }}">{{ $day }}</span>
                            
                            @if($appointmentsCount > 0)
                                <span class="mt-1 w-5 h-5 bg-indigo-600 text-white text-[10px] font-semibold rounded-full flex items-center justify-center">
                                    {{ $appointmentsCount }}
                                </span>
                            @endif

                            <!-- Tooltip -->
                            @if($appointmentsCount > 0)
                                <div class="absolute bottom-full mb-2 left-1/2 -translate-x-1/2 w-48 bg-gray-800 text-white text-xs rounded-lg p-2 opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none z-50 shadow-xl">
                                    <p class="font-semibold mb-1 border-b border-gray-600 pb-1">{{ now()->format('F') }} {{ $day }}</p>
                                    @foreach($appointmentsForDate as $appt)
                                        <div class="flex justify-between items-center py-1">
                                            <span class="truncate max-w-[100px]">{{ $appt->patient_name }}</span>
                                            <span class="text-gray-300 text-[10px]">{{ \Carbon\Carbon::parse($appt->time)->format('h:i A') }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                @endfor
            </div>

            <!-- Legend -->
            <div class="flex items-center justify-end gap-4 mt-6 pt-4 border-t border-gray-100">
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 bg-indigo-600 rounded-full"></span>
                    <span class="text-xs text-gray-600">Has Consultation</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 border-2 border-indigo-500 bg-indigo-50 rounded-full"></span>
                    <span class="text-xs text-gray-600">Today</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection