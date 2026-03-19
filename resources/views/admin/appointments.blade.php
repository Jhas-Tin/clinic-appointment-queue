@extends('layouts.app')

@section('title', 'Consultation Management')

@section('content')
<div class="space-y-6">

    <!-- STATS CARDS -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <!-- Total Consultations Card -->
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
                        <p class="text-xs text-gray-500 mt-1">All consultations</p>
                    </div>
                    <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                        <i class="fa fa-calendar text-blue-600 text-xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pending Card -->
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

        <!-- Approved Card -->
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
                        <p class="text-xs text-gray-500 mt-1">Confirmed consultations</p>
                    </div>
                    <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                        <i class="fa fa-thumbs-up text-green-600 text-xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Cancelled Card -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-100">
            <div class="bg-gradient-to-r from-red-600 to-red-700 px-5 py-3">
                <h3 class="text-sm font-semibold text-white flex items-center">
                    <i class="fa fa-times-circle mr-2"></i>
                    Cancelled
                </h3>
            </div>
            <div class="p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-3xl font-bold text-red-600">{{ $appointments->where('status','Cancelled')->count() }}</p>
                        <p class="text-xs text-gray-500 mt-1">Cancelled consultations</p>
                    </div>
                    <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center">
                        <i class="fa fa-ban text-red-600 text-xl"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- APPOINTMENTS TABLE -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-100">
        <div class="bg-gradient-to-r from-gray-700 to-gray-800 px-6 py-4">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-white flex items-center">
                    <i class="fa fa-list mr-2"></i>
                    Consultation List
                </h3>
                <div class="text-sm text-gray-300">
                    <i class="fa fa-calendar mr-1"></i> 
                    {{ \Carbon\Carbon::now()->format('F d, Y') }}
                </div>
            </div>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Patient</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Doctor</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Time</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($appointments as $appointment)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-gradient-to-br from-blue-100 to-blue-200 rounded-full flex items-center justify-center mr-3">
                                        <span class="text-blue-600 font-semibold text-sm">{{ substr($appointment->patient_name, 0, 1) }}</span>
                                    </div>
                                    <div>
                                        <p class="font-semibold text-gray-900">{{ $appointment->patient_name }}</p>
                                        <p class="text-xs text-gray-500">#CONS-{{ str_pad($appointment->id, 5, '0', STR_PAD_LEFT) }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center mr-2">
                                        <i class="fa fa-user-md text-purple-600 text-xs"></i>
                                    </div>
                                    <span class="text-gray-700">{{ $appointment->doctor_name }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <i class="fa fa-calendar-alt text-gray-400 mr-2"></i>
                                    <span class="font-medium">{{ \Carbon\Carbon::parse($appointment->date)->format('M d, Y') }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <i class="fa fa-clock text-gray-400 mr-2"></i>
                                    <span class="font-mono">{{ \Carbon\Carbon::parse($appointment->time)->format('h:i A') }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
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
                                <span class="px-3 py-1.5 rounded-full text-xs font-semibold flex items-center w-fit border {{ $color }}">
                                    <i class="fa {{ $icon }} mr-1.5"></i>
                                    {{ $appointment->status }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center gap-2">
                                    <!-- View Details -->
                                    <a href="{{ route('admin.appointments.show', $appointment->id) }}" 
                                       class="w-8 h-8 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100 transition flex items-center justify-center"
                                       title="View Details">
                                        <i class="fa fa-eye text-sm"></i>
                                    </a>

                                    <!-- Delete -->
                                    <form action="{{ route('admin.appointments.destroy', $appointment->id) }}" 
                                          method="POST" 
                                          class="inline" 
                                          onsubmit="return confirm('Are you sure you want to delete this appointment? This action cannot be undone.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="w-8 h-8 bg-red-50 text-red-600 rounded-lg hover:bg-red-100 transition flex items-center justify-center"
                                                title="Delete Appointment">
                                            <i class="fa fa-trash text-sm"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center text-gray-400">
                                    <i class="fa fa-calendar-times text-5xl mb-3"></i>
                                    <p class="text-lg font-medium">No appointments found</p>
                                    <p class="text-sm">There are no consultation records to display</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Table Footer with Count -->
        @if($appointments->count() > 0)
        <div class="bg-gray-50 px-6 py-3 border-t border-gray-200">
            <div class="flex items-center justify-between text-xs text-gray-500">
                <span>Showing <span class="font-medium">{{ $appointments->count() }}</span> consultations</span>
                <span>Last updated {{ \Carbon\Carbon::now()->format('h:i A') }}</span>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection