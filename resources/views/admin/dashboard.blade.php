<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title>Admin Dashboard</title>
<script src="https://cdn.tailwindcss.com"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body class="bg-[#f5f7fb] font-sans text-gray-700">

<div class="flex min-h-screen">

    <!-- SIDEBAR -->
    <aside class="w-64 bg-white shadow-lg rounded-r-3xl p-6">
        <div class="text-center">
            <img src="{{ Auth::guard('admin')->user()->avatar ?? 'https://via.placeholder.com/100' }}" class="w-24 h-24 mx-auto rounded-full shadow">
            <h2 class="mt-4 font-bold text-lg text-blue-600">{{ Auth::guard('admin')->user()->name }}</h2>
            <p class="text-xs text-gray-500">{{ Auth::guard('admin')->user()->email }}</p>
        </div>

        <nav class="mt-10 space-y-3 text-sm">
            <a class="flex items-center gap-3 p-3 rounded-xl bg-blue-50 text-blue-600 font-semibold" href="{{ route('admin.dashboard') }}">
                <i class="fa fa-chart-pie"></i> Dashboard
            </a>
            <a href="{{ route('admin.appointments') }}" class="flex items-center gap-3 p-3 rounded-xl hover:bg-gray-100">
                <i class="fa fa-calendar-check"></i> Appointments
            </a>
            <!-- <a class="flex items-center gap-3 p-3 rounded-xl hover:bg-gray-100" href="#">
                <i class="fa fa-credit-card"></i> Payment
            </a> -->
            <!-- <a class="flex items-center gap-3 p-3 rounded-xl hover:bg-gray-100" href="{{ route('admin.profile') }}">
                <i class="fa fa-user"></i> Profile
            </a>
            <a class="flex items-center gap-3 p-3 rounded-xl hover:bg-gray-100" href="#">
                <i class="fa fa-cog"></i> Settings
            </a> -->
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full flex items-center gap-3 p-3 rounded-xl text-red-500 hover:bg-red-50">
                    <i class="fa fa-sign-out"></i> Logout
                </button>
            </form>
        </nav>
    </aside>

    <!-- MAIN -->
    <main class="flex-1 p-8">

        <!-- TOP BAR -->
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-2xl font-bold text-gray-800">Dashboard</h1>
            <div class="flex items-center gap-5">
                <i class="fa-regular fa-envelope text-gray-500 text-xl"></i>
                <i class="fa-regular fa-bell text-gray-500 text-xl"></i>
                <div class="relative">
                    <input type="text" placeholder="Search"
                        class="pl-10 pr-4 py-2 rounded-xl bg-white shadow-sm border focus:outline-none">
                    <i class="fa fa-search absolute left-3 top-3 text-gray-400"></i>
                </div>
            </div>
        </div>

        <!-- MIDDLE SECTION (3 Cards) -->
        <div class="grid grid-cols-3 gap-6 mb-6">

            <!-- Patients Summary Chart -->
            <div class="bg-white rounded-2xl p-5 shadow-sm flex flex-col h-full">
                <h3 class="text-sm font-medium text-gray-700 mb-4">Patients Summary</h3>
                <div class="flex-1 flex items-center justify-center">
                    <canvas id="patientsChart" class="max-h-52"></canvas>
                </div>
                <div class="flex gap-6 justify-center mt-4">
                    <div class="flex items-center gap-2"><div class="w-3 h-3 bg-amber-400 rounded-sm"></div><span class="text-xs text-gray-600">New Patients</span></div>
                    <div class="flex items-center gap-2"><div class="w-3 h-3 bg-purple-200 rounded-sm"></div><span class="text-xs text-gray-600">Old Patients</span></div>
                    <div class="flex items-center gap-2"><div class="w-3 h-3 bg-blue-900 rounded-sm"></div><span class="text-xs text-gray-600">Total Patients</span></div>
                </div>
            </div>

            <!-- Today Appointments -->
            <div class="bg-white rounded-2xl p-5 shadow-sm flex flex-col h-full">
                <h3 class="text-sm font-medium text-blue-700 mb-3">Today Appointments</h3>
                <div class="mb-2">
                    <div class="grid grid-cols-[80px_1fr_80px] gap-4 text-xs font-medium text-gray-600 pb-2">
                        <span>Patient</span><span>Doctor</span><span class="text-right">Time</span>
                    </div>
                </div>
                <div class="space-y-2 flex-1 overflow-auto">
                    @forelse($todayAppointments as $appointment)
                        <div class="grid grid-cols-[80px_1fr_80px] gap-4 items-center py-2
                            {{ $appointment->dynamic_status == 'Ongoing' ? 'bg-blue-50 rounded-lg' : '' }}">
                            <img src="{{ $appointment->user?->avatar ?? 'https://via.placeholder.com/100' }}" class="w-10 h-10 rounded-full object-cover" />
                            <div>
                                <p class="text-sm font-medium text-gray-800">{{ $appointment->patient_name }}</p>
                                <p class="text-xs text-gray-500">{{ $appointment->doctor_name }}</p>
                            </div>
                            <div class="text-right text-sm font-medium
                                {{ $appointment->dynamic_status == 'Ongoing' ? 'text-blue-600 font-semibold' : 'text-gray-600' }}">
                                {{ \Carbon\Carbon::parse($appointment->time)->format('h:i A') }}
                                @if($appointment->dynamic_status == 'Ongoing')
                                    <span class="ml-1 text-xs px-2 py-0.5 bg-blue-100 text-blue-600 rounded-full">Ongoing</span>
                                @endif
                            </div>
                        </div>
                    @empty
                        <p class="text-xs text-gray-500 text-center">No appointments for today</p>
                    @endforelse
                </div>
                <!-- <button class="w-full mt-3 text-blue-600 text-sm font-medium hover:underline">See All</button> -->
            </div>

            <!-- Next Patient Details -->
            <div class="bg-white rounded-2xl p-5 shadow-sm flex flex-col h-full">
                <h3 class="text-sm font-medium text-blue-700 mb-3">Next Appointment Details</h3>
                @if($nextAppointment)
                    <div class="flex items-start gap-3 mb-4">
                        <img src="{{ $nextAppointment->user?->avatar ?? 'https://via.placeholder.com/100' }}" class="w-12 h-12 rounded-full object-cover" />
                        <div class="flex-1">
                            <h4 class="font-semibold text-gray-800">{{ $nextAppointment->patient_name }}</h4>
                            <p class="text-xs text-gray-500">{{ $nextAppointment->doctor_name }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-[10px] font-medium text-gray-600">Appointment ID</p>
                            <p class="text-xs text-gray-800">#APT-{{ $nextAppointment->id }}</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-3 gap-x-4 gap-y-3 mb-4 text-xs">
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
                            <span class="px-3 py-1 text-xs rounded-full
                                {{ $nextAppointment->dynamic_status == 'Approved' ? 'bg-green-100 text-green-600' : '' }}
                                {{ $nextAppointment->dynamic_status == 'Pending' ? 'bg-yellow-100 text-yellow-600' : '' }}
                                {{ $nextAppointment->dynamic_status == 'Cancelled' ? 'bg-red-100 text-red-600' : '' }}
                                {{ $nextAppointment->dynamic_status == 'Ongoing' ? 'bg-blue-100 text-blue-600' : '' }}">
                                {{ $nextAppointment->dynamic_status }}
                            </span>
                        </div>
                    </div>
                @else
                    <p class="text-xs text-gray-500">No upcoming appointments</p>
                @endif
            </div>

        </div>

        <!-- LOWER SECTION (3 Cards) -->
        <div class="grid grid-cols-3 gap-6">

            <!-- Patients Review -->
            <div class="bg-white rounded-2xl p-5 shadow-sm flex flex-col h-full">
                <h3 class="text-sm font-medium text-gray-700 mb-4">Patients Review</h3>
                <div class="space-y-3 text-xs flex-1">
                    <div>
                        <div class="flex justify-between mb-1"><span>Excellent</span><span>75%</span></div>
                        <div class="w-full bg-gray-200 rounded-full h-2"><div class="bg-blue-700 h-2 rounded-full" style="width:75%"></div></div>
                    </div>
                    <div>
                        <div class="flex justify-between mb-1"><span>Great</span><span>50%</span></div>
                        <div class="w-full bg-gray-200 rounded-full h-2"><div class="bg-green-500 h-2 rounded-full" style="width:50%"></div></div>
                    </div>
                    <div>
                        <div class="flex justify-between mb-1"><span>Good</span><span>35%</span></div>
                        <div class="w-full bg-gray-200 rounded-full h-2"><div class="bg-orange-400 h-2 rounded-full" style="width:35%"></div></div>
                    </div>
                    <div>
                        <div class="flex justify-between mb-1"><span>Average</span><span>20%</span></div>
                        <div class="w-full bg-gray-200 rounded-full h-2"><div class="bg-cyan-400 h-2 rounded-full" style="width:20%"></div></div>
                    </div>
                </div>
            </div>

            <!-- Appointment Requests -->
            <div class="bg-white rounded-2xl p-5 shadow-sm flex flex-col h-full">
                <h3 class="text-sm font-medium text-blue-700 mb-3">Appointment Requests</h3>
                <div class="space-y-3 flex-1 overflow-auto">
                    @foreach($appointments->take(5) as $appointment)
                        <div class="flex items-center gap-3">
                            <img src="{{ $appointment->user?->avatar ?? 'https://via.placeholder.com/100' }}" class="w-12 h-12 rounded-full object-cover">
                            <div class="flex-1">
                                <h4 class="font-medium text-gray-800 text-sm">{{ $appointment->patient_name }}</h4>
                                <p class="text-xs text-gray-500">{{ $appointment->status }}</p>
                            </div>
                            <div class="flex gap-1.5">
                                <form method="POST" action="{{ route('admin.appointments.approve', $appointment) }}">
                                    @csrf
                                    <button class="w-8 h-8 flex items-center justify-center bg-blue-100 hover:bg-blue-200 rounded-md">✔</button>
                                </form>
                                <form method="POST" action="{{ route('admin.appointments.cancel', $appointment) }}">
                                    @csrf
                                    <button class="w-8 h-8 flex items-center justify-center bg-red-100 hover:bg-red-200 rounded-md">✖</button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
                <!-- <button class="w-full mt-3 text-blue-600 text-sm font-medium hover:underline">See All</button> -->
            </div>

            <!-- Calendar -->
            <div class="bg-white rounded-2xl p-5 shadow-sm flex flex-col h-full">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-sm font-medium text-blue-700">Calendar</h3>
                    <span class="text-xs text-gray-500">{{ now()->format('F - Y') }}</span>
                </div>
                <div class="grid grid-cols-7 gap-1 flex-1 text-xs text-center">
                    <div class="font-medium text-gray-600">Sa</div>
                    <div class="font-medium text-gray-600">Su</div>
                    <div class="font-medium text-gray-600">Mo</div>
                    <div class="font-medium text-gray-600">Tu</div>
                    <div class="font-medium text-gray-600">We</div>
                    <div class="font-medium text-gray-600">Th</div>
                    <div class="font-medium text-gray-600">Fr</div>
                    @for($i=1; $i<=30; $i++)
                        <div @if($i==now()->day) class="bg-blue-600 text-white font-semibold rounded-lg" @endif>{{ $i }}</div>
                    @endfor
                </div>
            </div>

        </div>

    </main>
</div>

</body>
</html>
