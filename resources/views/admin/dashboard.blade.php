@extends('layouts.app')

@section('content')
<div class="p-8">

    <!-- TOP SECTION (3 Cards) -->
    <div class="grid grid-cols-3 gap-6 mb-6">

        <!-- Total Appointments Card -->
        <div class="bg-white rounded-2xl p-5 shadow-sm flex flex-col h-full items-center justify-center">
            <h3 class="text-sm font-medium text-gray-700 mb-2">Total Appointments</h3>
            <div class="flex items-center gap-3">
                <div class="p-3 bg-blue-100 rounded-full">
                    <i class="fa fa-calendar-check text-blue-600 text-xl"></i>
                </div>
                <div class="text-4xl font-bold text-blue-600">
                    {{ $appointments->count() }}
                </div>
            </div>
            <p class="text-xs text-gray-500 mt-2 text-center">Total number of appointments in the system</p>
        </div>

        <!-- Today Appointments -->
        <div class="bg-white rounded-2xl p-5 shadow-sm flex flex-col h-full">
            <h3 class="text-sm font-medium text-blue-700 mb-3">Today Appointments</h3>
            <div class="mb-2">
                <div class="grid grid-cols-[80px_1fr_80px] gap-4 text-xs font-medium text-gray-600 pb-2">
                    <span>Patient</span><span>Doctor</span><span class="text-right">Time</span>
                </div>
            </div>
            <!-- Added max-height + scroll -->
            <div class="space-y-2 flex-1 overflow-y-auto max-h-96">
                @if($todayAppointments->where('dynamic_status','Ongoing')->count())
                    @foreach($todayAppointments->where('dynamic_status','Ongoing') as $appointment)
                        @php
                            $rowClasses = 'bg-blue-50 rounded-lg';
                            $timeClasses = 'text-blue-600 font-semibold';
                        @endphp
                        <div class="grid grid-cols-[80px_1fr_80px] gap-4 items-center py-2 {{ $rowClasses }}">
                            <img src="{{ $appointment->user?->avatar ?? 'https://via.placeholder.com/100' }}" class="w-10 h-10 rounded-full object-cover" />
                            <div>
                                <p class="text-sm font-medium text-gray-800">{{ $appointment->patient_name }}</p>
                                <p class="text-xs text-gray-500">{{ $appointment->doctor_name }}</p>
                            </div>
                            <div class="text-right text-sm font-medium {{ $timeClasses }}">
                                {{ \Carbon\Carbon::parse($appointment->time)->format('h:i A') }}
                                <span class="ml-1 text-xs px-2 py-0.5 bg-blue-100 text-blue-600 rounded-full">Ongoing</span>
                            </div>
                        </div>
                    @endforeach
                @else
                    <p class="text-xs text-gray-500 text-center">No ongoing appointments for today</p>
                @endif
            </div>
        </div>

        <!-- Next Appointment Details -->
        <div class="bg-white rounded-2xl p-5 shadow-sm flex flex-col h-full">
            <h3 class="text-sm font-medium text-blue-700 mb-3">Next Appointment Details</h3>

            @if($upcomingAppointments && $upcomingAppointments->count())
                <!-- Added max-height + scroll -->
                <div class="flex-1 overflow-y-auto space-y-4 max-h-96">
                    @foreach($upcomingAppointments as $nextAppointment)
                        <div class="border border-gray-100 rounded-lg p-3 hover:shadow-sm transition">
                            <div class="flex items-start gap-3 mb-2">
                                <div class="flex-1">
                                    <h4 class="font-semibold text-gray-800">{{ $nextAppointment->patient_name }}</h4>
                                    <p class="text-xs text-gray-500">{{ $nextAppointment->doctor_name }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-[10px] font-medium text-gray-600">Appointment ID</p>
                                    <p class="text-xs text-gray-800">#APT-{{ $nextAppointment->id }}</p>
                                </div>
                            </div>

                            <div class="grid grid-cols-3 gap-x-4 text-xs">
                                <div>
                                    <p class="text-gray-500 mb-0.5">Date</p>
                                    <p class="text-gray-800">{{ \Carbon\Carbon::parse($nextAppointment->date)->format('d M Y') }}</p>
                                </div>
                                <div>
                                    <p class="text-gray-500 mb-0.5">Time</p>
                                    <p class="text-gray-800">{{ \Carbon\Carbon::parse($nextAppointment->time)->format('h:i A') }}</p>
                                </div>
                                <div>
                                    <p class="text-gray-500 mb-0.5">Status</p>
                                    @php
                                        $statusColor = match($nextAppointment->dynamic_status) {
                                            'Ongoing' => 'bg-blue-100 text-blue-600',
                                            'Approved', 'Upcoming' => 'bg-green-100 text-green-600',
                                            'Pending' => 'bg-yellow-100 text-yellow-600',
                                            'Cancelled' => 'bg-red-100 text-red-600',
                                            'Completed' => 'bg-gray-100 text-gray-600',
                                            default => 'bg-gray-100 text-gray-600',
                                        };
                                    @endphp
                                    <span class="px-3 py-1 text-xs rounded-full {{ $statusColor }}">
                                        {{ $nextAppointment->dynamic_status }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-xs text-gray-500 text-center mt-4">No upcoming appointments</p>
            @endif
        </div>

    </div>

    <!-- LOWER SECTION (Calendar) -->
    <div class="grid grid-cols-1 gap-6 mt-6">
        <div class="bg-white rounded-2xl p-5 shadow-sm flex flex-col h-full">
            <div class="flex items-center justify-between mb-3">
                <h3 class="text-sm font-medium text-blue-700">Calendar</h3>
                <span class="text-xs text-gray-500">{{ now()->format('F - Y') }}</span>
            </div>
            <div class="grid grid-cols-7 grid-rows-6 gap-1 flex-1 text-xs text-center">
                <!-- Weekday Headers -->
                @foreach(['Sa','Su','Mo','Tu','We','Th','Fr'] as $day)
                    <div class="font-medium text-gray-600 py-1">{{ $day }}</div>
                @endforeach

                <!-- Dates -->
                @for($i = 1; $i <= 30; $i++)
                    @php
                        $dateStr = now()->format('Y-m-') . str_pad($i, 2, '0', STR_PAD_LEFT);
                        $appointmentsForDate = $appointments->where('date', $dateStr);
                        $appointmentsCount = $appointmentsForDate->count();
                    @endphp

                    <div class="relative border border-gray-200 flex flex-col items-center justify-center h-20 p-1
                                @if($i == now()->day) bg-blue-50 rounded-lg @endif
                                group">
                        <span class="text-sm font-medium @if($i == now()->day) text-blue-600 @endif">{{ $i }}</span>

                        @if($appointmentsCount > 0)
                            <span class="mt-1 w-5 h-5 bg-blue-600 text-white text-[10px] font-semibold rounded-full flex items-center justify-center">
                                {{ $appointmentsCount }}
                            </span>

                            <!-- Tooltip -->
                            <div class="absolute bottom-full mb-1 left-1/2 -translate-x-1/2 w-36 bg-gray-800 text-white text-xs rounded-md p-2 opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none z-50">
                                @foreach($appointmentsForDate as $appt)
                                    <div class="mb-1 last:mb-0">
                                        <span class="font-semibold">{{ $appt->patient_name }}</span> 
                                        <span class="text-gray-300">({{ \Carbon\Carbon::parse($appt->time)->format('h:i A') }})</span>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endfor
            </div>
        </div>
    </div>

</div>

<!-- Optional Chart Script -->
<script>
    const ctx = document.getElementById('patientsChart')?.getContext('2d');
    if(ctx) {
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Total Appointments'],
                datasets: [{
                    data: [{{ $todayAppointments->count() }}],
                    backgroundColor: ['#3b82f6'],
                    borderWidth: 0
                }]
            },
            options: {
                cutout: '70%',
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });
    }
</script>
@endsection