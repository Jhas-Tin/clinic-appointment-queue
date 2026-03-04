<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'User Dashboard')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-100 font-sans text-gray-700">

<div class="flex min-h-screen">

    <aside class="w-64 bg-white shadow-lg rounded-r-3xl p-6 hidden md:block">
        <div class="text-center">
            <img src="{{ Auth::user()->profile_photo_url ?? 'https://www.shutterstock.com/image-vector/background-illustration-doctor-receptionist-atmosphere-260nw-2567672079.jpg' }}" alt="User" class="w-24 h-24 mx-auto rounded-full shadow">
            <h2 class="mt-4 font-bold text-lg text-blue-600">{{ Auth::user()->name }}</h2>
            <p class="text-xs text-gray-500">User Dashboard</p>
        </div>

        <nav class="mt-10 space-y-3 text-sm">
            <a href="{{ route('user.dashboard') }}" class="flex items-center gap-3 p-3 rounded-xl hover:bg-gray-100 {{ request()->routeIs('user.dashboard') ? 'bg-blue-50 text-blue-600 font-semibold' : '' }}">
                <i class="fa fa-home"></i> Dashboard
            </a>
            <a href="{{ route('user.appointments') }}" class="flex items-center gap-3 p-3 rounded-xl hover:bg-gray-100 {{ request()->routeIs('user.appointments') ? 'bg-blue-50 text-blue-600 font-semibold' : '' }}">
                <i class="fa fa-calendar-check"></i> Appointments
            </a>
            <!-- <a href="{{ route('user.payments') }}" class="flex items-center gap-3 p-3 rounded-xl hover:bg-gray-100 {{ request()->routeIs('user.payments') ? 'bg-blue-50 text-blue-600 font-semibold' : '' }}">
                <i class="fa fa-credit-card"></i> Payments
            </a> -->
            <!-- <a href="{{ route('user.profile') }}" class="flex items-center gap-3 p-3 rounded-xl hover:bg-gray-100 {{ request()->routeIs('user.profile') ? 'bg-blue-50 text-blue-600 font-semibold' : '' }}">
                <i class="fa fa-user"></i> Profile
            </a> -->
            <form method="POST" action="/logout">
                @csrf
                <button type="submit" class="w-full flex items-center gap-3 p-3 rounded-xl text-red-500 hover:bg-red-50">
                    <i class="fa fa-sign-out"></i> Logout
                </button>
            </form>
        </nav>
    </aside>

    <main class="flex-1 p-6 md:p-8 space-y-6">
        @yield('content')
    </main>
</div>

</body>
</html>
