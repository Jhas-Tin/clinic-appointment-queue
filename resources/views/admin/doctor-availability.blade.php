@extends('layouts.app')

@section('title', 'Doctor Schedule Management')

@section('content')
<div class="space-y-6">

    @if(session('success'))
        <div class="bg-green-50 border-l-4 border-green-500 text-green-700 p-4 rounded-lg shadow-sm flex items-center justify-between">
            <div class="flex items-center">
                <i class="fa fa-check-circle text-green-500 mr-3 text-xl"></i>
                <span>{{ session('success') }}</span>
            </div>
            <button onclick="this.parentElement.remove()" class="text-green-500 hover:text-green-700">
                <i class="fa fa-times"></i>
            </button>
        </div>
    @endif

    @if(isset($doctor))
        <!-- EDIT MODE - Show edit form for specific doctor -->
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center gap-4">
                <a href="{{ route('admin.doctor-availability.index') }}" class="flex items-center gap-2 text-gray-600 hover:text-blue-600 transition bg-white px-4 py-2 rounded-lg shadow-sm border border-gray-200">
                    <i class="fa fa-arrow-left"></i> Back to List
                </a>
                <h2 class="text-2xl font-bold text-gray-800">Edit Schedule: <span class="text-blue-600">{{ $doctor->name }}</span></h2>
            </div>
            <div class="text-sm text-gray-500 bg-blue-50 px-4 py-2 rounded-lg">
                <i class="fa fa-calendar-alt text-blue-500 mr-1"></i> Managing weekly schedule
            </div>
        </div>

        @if($errors->any())
            <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded-lg shadow-sm mb-6">
                <div class="flex items-center mb-2">
                    <i class="fa fa-exclamation-triangle text-red-500 mr-2"></i>
                    <span class="font-semibold">Please fix the following errors:</span>
                </div>
                <ul class="list-disc list-inside text-sm ml-6">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Edit Form Card -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-100 mb-6">
            <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-4">
                <h3 class="text-lg font-semibold text-white flex items-center">
                    <i class="fa fa-edit mr-2"></i>
                    Update Weekly Schedule
                </h3>
                <p class="text-blue-100 text-sm mt-1">Configure the days and times when this doctor is available</p>
            </div>
            
            <div class="p-6">
                <form method="POST" action="{{ route('admin.doctor-availability.update', $doctor->id) }}" class="space-y-6">
                    @csrf

                    <!-- Days of Week -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-3">
                            <i class="fa fa-calendar-check text-blue-500 mr-1"></i>
                            Select Available Days
                        </label>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                            @foreach($days as $day)
                                @php
                                    $schedule = $weeklySchedule[$day] ?? null;
                                    $isChecked = $schedule && $schedule->availability_status == 'Available';
                                @endphp
                                <label class="flex items-center p-4 rounded-xl border-2 cursor-pointer transition-all duration-200
                                    {{ $isChecked ? 'border-blue-500 bg-blue-50' : 'border-gray-200 hover:border-gray-300 bg-gray-50' }}">
                                    <input type="checkbox" name="days[]" value="{{ $day }}" 
                                        {{ $isChecked ? 'checked' : '' }}
                                        class="form-checkbox h-5 w-5 text-blue-600 rounded mr-3">
                                    <div>
                                        <span class="font-medium {{ $isChecked ? 'text-blue-700' : 'text-gray-700' }}">{{ $day }}</span>
                                        <span class="block text-xs {{ $isChecked ? 'text-blue-600' : 'text-gray-500' }}">
                                            {{ $isChecked ? 'Available' : 'Not available' }}
                                        </span>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                        <p class="text-xs text-gray-500 mt-3 flex items-center">
                            <i class="fa fa-info-circle text-blue-400 mr-1"></i>
                            Check the days when this doctor is available for consultations
                        </p>
                    </div>

                    <!-- Time Settings -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <!-- Start Time -->
                        <div>
                            <label class="block mb-2 font-medium text-gray-700">
                                <i class="fa fa-play text-green-500 mr-1"></i>
                                Start Time
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fa fa-clock text-gray-400"></i>
                                </div>
                                <input type="time"
                                       name="start_time"
                                       value="{{ isset($weeklySchedule['Monday']) ? $weeklySchedule['Monday']->start_time : '08:00' }}"
                                       class="pl-10 w-full border-2 border-gray-200 rounded-xl p-3 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition"
                                       required>
                            </div>
                        </div>

                        <!-- End Time -->
                        <div>
                            <label class="block mb-2 font-medium text-gray-700">
                                <i class="fa fa-stop text-red-500 mr-1"></i>
                                End Time
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fa fa-clock text-gray-400"></i>
                                </div>
                                <input type="time"
                                       name="end_time"
                                       value="{{ isset($weeklySchedule['Monday']) ? $weeklySchedule['Monday']->end_time : '17:00' }}"
                                       class="pl-10 w-full border-2 border-gray-200 rounded-xl p-3 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition"
                                       required>
                            </div>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="flex items-center justify-between gap-3 pt-4 border-t border-gray-100">
                        <div class="text-sm text-gray-500">
                            <i class="fa fa-sync-alt mr-1"></i> Changes will be applied immediately
                        </div>
                        <div class="flex items-center gap-3">
                            <a href="{{ route('admin.doctor-availability.index') }}" 
                               class="px-6 py-3 border-2 border-gray-200 text-gray-600 rounded-xl hover:bg-gray-50 transition font-medium">
                                Cancel
                            </a>
                            <button type="submit" 
                                    class="px-8 py-3 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-xl hover:from-blue-700 hover:to-blue-800 transition font-medium shadow-lg shadow-blue-200 flex items-center gap-2">
                                <i class="fa fa-save"></i>
                                Update Weekly Schedule
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Current Schedule Preview -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-100">
            <div class="bg-gradient-to-r from-gray-700 to-gray-800 px-6 py-4">
                <h3 class="text-lg font-semibold text-white flex items-center">
                    <i class="fa fa-eye mr-2"></i>
                    Current Schedule Preview
                </h3>
                <p class="text-gray-300 text-sm mt-1">Overview of the current weekly schedule</p>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                    @foreach($days as $day)
                        @php
                            $schedule = $weeklySchedule[$day] ?? null;
                        @endphp
                        <div class="relative overflow-hidden rounded-xl border-2 {{ $schedule && $schedule->availability_status == 'Available' ? 'border-green-200 bg-gradient-to-br from-green-50 to-white' : 'border-red-200 bg-gradient-to-br from-red-50 to-white' }}">
                            <!-- Corner Accent -->
                            <div class="absolute top-0 right-0 w-16 h-16 overflow-hidden">
                                <div class="absolute transform rotate-45 bg-{{ $schedule && $schedule->availability_status == 'Available' ? 'green' : 'red' }}-100 w-20 h-8 -right-6 top-3"></div>
                            </div>
                            
                            <div class="p-4">
                                <div class="flex items-center justify-between mb-3">
                                    <h4 class="font-bold text-gray-800 text-lg">{{ $day }}</h4>
                                    @if($schedule && $schedule->availability_status == 'Available')
                                        <span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-xs font-semibold flex items-center">
                                            <span class="w-2 h-2 bg-green-500 rounded-full mr-1"></span>
                                            Online
                                        </span>
                                    @elseif($schedule)
                                        <span class="px-3 py-1 bg-red-100 text-red-700 rounded-full text-xs font-semibold flex items-center">
                                            <span class="w-2 h-2 bg-red-500 rounded-full mr-1"></span>
                                            Offline
                                        </span>
                                    @else
                                        <span class="px-3 py-1 bg-gray-100 text-gray-700 rounded-full text-xs font-semibold">
                                            Not Set
                                        </span>
                                    @endif
                                </div>
                                
                                @if($schedule)
                                    <div class="flex items-center text-sm text-gray-600 bg-white bg-opacity-60 rounded-lg p-2">
                                        <i class="far fa-clock text-gray-400 mr-2"></i>
                                        <span class="font-mono font-medium">{{ \Carbon\Carbon::parse($schedule->start_time)->format('h:i A') }}</span>
                                        <span class="mx-2 text-gray-300">—</span>
                                        <span class="font-mono font-medium">{{ \Carbon\Carbon::parse($schedule->end_time)->format('h:i A') }}</span>
                                    </div>
                                @else
                                    <div class="text-sm text-gray-400 italic flex items-center bg-white bg-opacity-60 rounded-lg p-2">
                                        <i class="far fa-clock mr-2"></i>
                                        No schedule set for this day
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

    @else
        <!-- INDEX MODE - Show list of all doctors -->
        <div class="flex justify-between items-center mb-6">
            <div>
               
            </div>
            <div class="text-sm bg-blue-50 text-blue-700 px-4 py-2 rounded-lg shadow-sm flex items-center">
                <i class="fa fa-users mr-2"></i> 
                <span class="font-medium">{{ $doctors->count() }}</span> Total Doctors
            </div>
        </div>

        <!-- Doctors Table -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-100">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Doctor</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Email</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Current Status</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Weekly Schedule</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($doctors as $doctor)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10 bg-gradient-to-br from-blue-100 to-blue-200 rounded-full flex items-center justify-center">
                                        <span class="text-blue-600 font-semibold text-lg">{{ substr($doctor->name, 0, 1) }}</span>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-semibold text-gray-900">{{ $doctor->name }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-600 flex items-center">
                                    <i class="fa fa-envelope text-gray-400 mr-2"></i>
                                    {{ $doctor->email }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($doctor->availability_status == 'Available')
                                    <span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-xs font-semibold flex items-center w-fit">
                                        <span class="w-2 h-2 bg-green-500 rounded-full mr-1"></span>
                                        Online
                                    </span>
                                @elseif($doctor->availability_status == 'Idle')
                                    <span class="px-3 py-1 bg-yellow-100 text-yellow-700 rounded-full text-xs font-semibold flex items-center w-fit">
                                        <span class="w-2 h-2 bg-yellow-500 rounded-full mr-1"></span>
                                        Idle
                                    </span>
                                @else
                                    <span class="px-3 py-1 bg-red-100 text-red-700 rounded-full text-xs font-semibold flex items-center w-fit">
                                        <span class="w-2 h-2 bg-red-500 rounded-full mr-1"></span>
                                        Offline
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-wrap gap-2">
                                    @foreach($days as $day)
                                        @php
                                            $schedule = $doctor->schedules->where('day_of_week', $day)->first();
                                        @endphp
                                        @if($schedule)
                                            <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium
                                                {{ $schedule->availability_status == 'Available' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                                {{ substr($day, 0, 3) }}
                                                <span class="ml-1 opacity-75">({{ \Carbon\Carbon::parse($schedule->start_time)->format('g:i A') }})</span>
                                            </span>
                                        @endif
                                    @endforeach
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <a href="{{ route('admin.doctor-availability.edit', $doctor->id) }}" 
                                   class="inline-flex items-center px-4 py-2 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100 transition font-medium text-sm">
                                    <i class="fa fa-edit mr-2"></i>
                                    Edit Schedule
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center text-gray-400">
                                    <i class="fa fa-user-md text-5xl mb-3"></i>
                                    <p class="text-lg font-medium">No doctors found</p>
                                    <p class="text-sm">Add doctors to manage their schedules</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>
@endsection