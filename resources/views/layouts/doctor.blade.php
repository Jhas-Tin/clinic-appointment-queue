<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title>@yield('title', 'Doctor Dashboard')</title>
<script src="https://cdn.tailwindcss.com"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body class="bg-[#f5f7fb] font-sans text-gray-700">

<div class="flex min-h-screen">

    <!-- SIDEBAR -->
    <aside class="w-64 bg-white shadow-lg rounded-r-3xl p-6 sticky top-0 h-screen overflow-y-auto flex-shrink-0">
        <div class="text-center">
            <img src="https://img.freepik.com/premium-vector/doctor-profile-with-medical-service-icon_617655-48.jpg" class="w-24 h-24 mx-auto rounded-full shadow">
            <h2 class="mt-4 font-bold text-lg text-blue-600">Doctor</h2>
            <p class="text-xs text-gray-500">{{ Auth::guard('doctor')->user()->email ?? '' }}</p>
        </div>

        <nav class="mt-10 space-y-3 text-sm">
            <a href="{{ route('doctor.dashboard') }}" 
            class="flex items-center gap-3 p-3 rounded-xl hover:bg-gray-100 {{ request()->is('doctor/dashboard') ? 'bg-blue-50 text-blue-600 font-semibold' : '' }}">
                <i class="fa fa-calendar-check"></i> Appointments
            </a>

            <a href="{{ route('doctor.availability') }}" 
            class="flex items-center gap-3 p-3 rounded-xl hover:bg-gray-100 {{ request()->is('doctor/availability') ? 'bg-blue-50 text-blue-600 font-semibold' : '' }}">
                <i class="fa fa-clock"></i> Availability
            </a>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                    class="w-full flex items-center gap-3 p-3 rounded-xl text-red-500 hover:bg-red-50">
                    <i class="fa fa-sign-out"></i> Logout
                </button>
            </form>
        </nav>
    </aside>

    <!-- MAIN -->
    <main class="flex-1 p-8 overflow-y-auto">
        <!-- TOP BAR -->
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-2xl font-bold text-gray-800">@yield('title', 'Doctor Dashboard')</h1>
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

        <!-- PAGE CONTENT -->
        @yield('content')

    </main>
</div>

{{-- 🔥 THIS IS THE IMPORTANT FIX --}}
@yield('scripts')

</body>
</html>