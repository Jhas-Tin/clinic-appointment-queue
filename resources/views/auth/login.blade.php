<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Clinic Appointment System - Login</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="h-screen bg-cover bg-center bg-no-repeat"
      style='background-image: url("https://www.shutterstock.com/image-vector/online-doctor-appointment-booking-system-600nw-2655423645.jpg")'>


<div class="w-full h-full bg-blue-900/30 flex flex-col">

    <!-- HEADER -->
    <header class="flex items-center justify-center py-7 px-6 relative">

        <div class="flex items-center gap-3 text-white text-3xl font-semibold drop-shadow-lg">
            <span>🏥</span>
            <span>Clinic Appointment System</span>
        </div>

        <a href="/register"
           class="absolute right-6 top-6 bg-white text-blue-600 px-5 py-2 rounded-lg font-semibold shadow-md hover:bg-gray-100 transition">
            Register
        </a>

    </header>

    <!-- MAIN CONTENT -->
    <main class="flex flex-1 items-center justify-center px-4">

        <div class="w-full max-w-md bg-white rounded-2xl shadow-2xl px-8 py-10">
            <h2 class="text-3xl font-bold text-blue-600 mb-6 text-center">
                Login
            </h2>

            @if ($errors->any())
                <div class="bg-red-100 text-red-600 p-3 rounded mb-4 text-sm">
                    @foreach ($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="/login" class="space-y-5">
                @csrf

                <div>
                    <label class="text-sm text-gray-600 block mb-1">Email</label>
                    <div class="relative">
                        <span class="absolute left-3 top-2.5 text-gray-400 text-lg">📧</span>
                        <input type="email"
                               name="email"
                               value="{{ old('email') }}"
                               placeholder="Enter your email"
                               required
                               class="w-full pl-10 pr-4 py-2.5 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-400">
                    </div>
                </div>

                <div>
                    <label class="text-sm text-gray-600 block mb-1">Password</label>
                    <div class="relative">
                        <span class="absolute left-3 top-2.5 text-gray-400 text-lg">🔒</span>
                        <input type="password"
                               name="password"
                               placeholder="Enter your password"
                               required
                               class="w-full pl-10 pr-4 py-2.5 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-400">
                    </div>
                </div>

                <!-- FIXED LOGIN BUTTON -->
                <button type="submit"
                        class="w-full mt-3 py-3 bg-gradient-to-r from-blue-600 to-blue-400 text-white rounded-lg font-semibold shadow-md hover:opacity-90 transition">
                    Log In
                </button>

            </form>

        </div>

    </main>

</div>
</body>
</html>
