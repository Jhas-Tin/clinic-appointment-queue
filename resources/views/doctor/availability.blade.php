@extends('layouts.doctor')

@section('title', 'My Availability')

@section('content')
<div class="space-y-6">

    <!-- AVAILABILITY STATUS CARDS -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <!-- Current Status Card (Like Discord) -->
        <div class="bg-white p-6 rounded-2xl shadow hover:shadow-lg transition">
            <p class="text-sm text-gray-500">My Current Status</p>
            <div class="flex items-center gap-3 mt-2">
                @if(($doctor->availability_status ?? '') == 'Available')
                    <span class="w-4 h-4 bg-green-500 rounded-full"></span>
                    <h2 class="text-2xl font-bold text-green-600">Active</h2>
                @elseif(($doctor->availability_status ?? '') == 'Unavailable')
                    <span class="w-4 h-4 bg-yellow-500 rounded-full"></span>
                    <h2 class="text-2xl font-bold text-yellow-600">Away</h2>
                @else
                    <span class="w-4 h-4 bg-gray-400 rounded-full"></span>
                    <h2 class="text-2xl font-bold text-gray-600">Not Set</h2>
                @endif
            </div>
            <p class="text-xs text-gray-400 mt-2">Click below to change your status</p>
        </div>

        <!-- Today's Schedule -->
        <div class="bg-white p-6 rounded-2xl shadow hover:shadow-lg transition">
            <p class="text-sm text-gray-500">Today's Schedule</p>
            @php
                $today = \Carbon\Carbon::now()->format('l');
                $todaySchedule = $weeklySchedule[$today] ?? null;
            @endphp
            @if($todaySchedule)
                <h2 class="text-2xl font-bold text-gray-700 mt-2">
                    {{ \Carbon\Carbon::parse($todaySchedule->start_time)->format('h:i A') }} - 
                    {{ \Carbon\Carbon::parse($todaySchedule->end_time)->format('h:i A') }}
                </h2>
                <p class="text-sm {{ $todaySchedule->availability_status == 'Available' ? 'text-green-600' : 'text-red-600' }}">
                    {{ $todaySchedule->availability_status == 'Available' ? 'Scheduled: Online' : 'Scheduled: Offline' }}
                </p>
            @else
                <h2 class="text-2xl font-bold text-gray-700 mt-2">Day Off</h2>
                <p class="text-sm text-gray-500">No scheduled hours today</p>
            @endif
        </div>

        <!-- Start Time -->
        <div class="bg-white p-6 rounded-2xl shadow hover:shadow-lg transition">
            <p class="text-sm text-gray-500">Typical Start Time</p>
            @php
                $firstAvailable = collect($weeklySchedule)->firstWhere('availability_status', 'Available');
            @endphp
            <h2 class="text-2xl font-bold text-gray-700 mt-2">
                {{ $firstAvailable ? \Carbon\Carbon::parse($firstAvailable->start_time)->format('h:i A') : '--:-- --' }}
            </h2>
            <p class="text-xs text-gray-400 mt-1">Based on your weekly schedule</p>
        </div>

        <!-- End Time -->
        <div class="bg-white p-6 rounded-2xl shadow hover:shadow-lg transition">
            <p class="text-sm text-gray-500">Typical End Time</p>
            <h2 class="text-2xl font-bold text-gray-700 mt-2">
                {{ $firstAvailable ? \Carbon\Carbon::parse($firstAvailable->end_time)->format('h:i A') : '--:-- --' }}
            </h2>
            <p class="text-xs text-gray-400 mt-1">Based on your weekly schedule</p>
        </div>
    </div>

    <!-- WEEKLY SCHEDULE (View Only) -->
    <div class="bg-white rounded-2xl shadow p-6">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-xl font-semibold">My Weekly Schedule</h3>
            <span class="px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-xs font-medium">
                <i class="fa fa-lock mr-1"></i> Managed by Admin
            </span>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
            @foreach($days as $day)
                @php
                    $schedule = $weeklySchedule[$day] ?? null;
                @endphp
                <div class="border rounded-xl p-4 {{ $schedule && $schedule->availability_status == 'Available' ? 'bg-green-50 border-green-200' : 'bg-gray-50 border-gray-200' }}">
                    <div class="flex justify-between items-start">
                        <p class="font-semibold text-gray-800">{{ $day }}</p>
                        @if($schedule && $schedule->availability_status == 'Available')
                            <span class="px-2 py-1 bg-green-100 text-green-700 rounded-full text-xs font-medium">
                                <span class="w-2 h-2 bg-green-500 rounded-full inline-block mr-1"></span>
                                Online
                            </span>
                        @elseif($schedule)
                            <span class="px-2 py-1 bg-red-100 text-red-700 rounded-full text-xs font-medium">
                                <span class="w-2 h-2 bg-red-500 rounded-full inline-block mr-1"></span>
                                Offline
                            </span>
                        @else
                            <span class="px-2 py-1 bg-gray-100 text-gray-700 rounded-full text-xs font-medium">
                                Not set
                            </span>
                        @endif
                    </div>
                    @if($schedule)
                        <p class="text-sm text-gray-600 mt-2">
                            <i class="far fa-clock mr-1 text-gray-400"></i>
                            {{ \Carbon\Carbon::parse($schedule->start_time)->format('h:i A') }} - 
                            {{ \Carbon\Carbon::parse($schedule->end_time)->format('h:i A') }}
                        </p>
                    @else
                        <p class="text-sm text-gray-400 mt-2">
                            <i class="far fa-clock mr-1"></i> No schedule
                        </p>
                    @endif
                </div>
            @endforeach
        </div>
    </div>

    <!-- STATUS UPDATE FORM (Like Discord) -->
    <div class="bg-white rounded-2xl shadow p-6">
        <h3 class="text-xl font-semibold mb-4">Set Your Online Status</h3>

        @if(session('success'))
            <div class="bg-green-100 text-green-700 p-3 rounded mb-4">
                <i class="fa fa-check-circle mr-1"></i> {{ session('success') }}
            </div>
        @endif

        <form method="POST" action="{{ route('doctor.availability.update-status') }}" class="space-y-5">
            @csrf

            <div class="flex flex-col lg:flex-row lg:items-end gap-5">
                <!-- Status Toggle (Like Discord) -->
                <div class="flex-1">
                    <label class="block mb-3 font-medium text-gray-700">Select your current status:</label>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <label class="flex items-center gap-4 p-4 border rounded-xl cursor-pointer hover:bg-gray-50 transition {{ $doctor->availability_status == 'Available' ? 'border-green-500 bg-green-50' : '' }}">
                            <input type="radio" name="availability_status" value="Available" 
                                {{ $doctor->availability_status == 'Available' ? 'checked' : '' }} 
                                class="form-radio h-5 w-5 text-green-600" required>
                            <div>
                                <div class="flex items-center gap-2">
                                    <span class="w-3 h-3 bg-green-500 rounded-full"></span>
                                    <span class="font-semibold text-green-700">Online</span>
                                </div>
                                <p class="text-sm text-gray-500 mt-1">Available for consultations</p>
                            </div>
                        </label>
                        
                        <label class="flex items-center gap-4 p-4 border rounded-xl cursor-pointer hover:bg-gray-50 transition {{ $doctor->availability_status == 'Unavailable' ? 'border-red-500 bg-red-50' : '' }}">
                            <input type="radio" name="availability_status" value="Unavailable" 
                                {{ $doctor->availability_status == 'Unavailable' ? 'checked' : '' }} 
                                class="form-radio h-5 w-5 text-red-600" required>
                            <div>
                                <div class="flex items-center gap-2">
                                    <span class="w-3 h-3 bg-yellow-500 rounded-full"></span>
                                    <span class="font-semibold text-yellow-700">Away</span>
                                </div>
                                <p class="text-sm text-gray-500 mt-1">Not available</p>
                            </div>
                        </label>
                    </div>
                </div>
                
                <div class="lg:w-auto">
                    <button type="submit" class="w-full lg:w-auto bg-blue-600 text-white px-8 py-3 rounded-xl hover:bg-blue-700 transition font-semibold flex items-center justify-center gap-2">
                        <i class="fa fa-save"></i> Update Status
                    </button>
                </div>
            </div>
        </form>

        <!-- Info Note -->
        <div class="mt-6 p-4 bg-blue-50 rounded-xl flex items-start gap-3">
            <i class="fa fa-info-circle text-blue-500 mt-1"></i>
            <div class="text-sm text-blue-800">
                <p class="font-medium mb-1">About your schedule</p>
                <p>Your weekly schedule is managed by the administrator. You can only change your online/offline status here. 
                   This lets patients know if you're currently available for consultations.</p>
            </div>
        </div>
    </div>

</div>
@endsection