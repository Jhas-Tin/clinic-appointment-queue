<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Clinic Appointment System - Login</title>

<script src="https://cdn.tailwindcss.com"></script>

</head>

<body class="h-screen bg-cover bg-center bg-no-repeat"
      style="background-image: url('{{ asset('build/assets/images/bg.jpg') }}')">

<!-- OVERLAY -->
<div class="w-full h-full bg-blue-900/10 relative">

    <!-- HEADER -->
    <header class="relative flex items-center justify-center py-7 px-12">

        <!-- CENTER TITLE -->
        <div class="flex items-center gap-3 text-white text-3xl font-semibold drop-shadow-lg">
            <span class="text-3xl">🏥</span>
            <span>Clinic Appointment System</span>
        </div>

        <!-- REGISTER BUTTON -->
        <a href="/register"
           class="absolute right-10 top-6 bg-white text-blue-600 px-6 py-2 rounded-lg font-semibold shadow-md hover:bg-gray-100 transition">
            Register
        </a>

    </header>

    <!-- MAIN CONTENT -->
    <main class="flex items-center h-[calc(100vh-90px)]">

        <!-- LOGIN CARD -->
        <div class="ml-20 w-105 bg-white rounded-2xl shadow-[0_25px_60px_rgba(0,0,0,0.25)] px-10 py-10">

            <h2 class="text-2xl font-bold text-blue-600 mb-6">
                Login
            </h2>

            <!-- ERRORS -->
            @if ($errors->any())
                <div class="bg-red-100 text-red-600 p-3 rounded mb-4 text-sm">
                    @foreach ($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <!-- FORM -->
            <form method="POST" action="/login" class="space-y-4">
                @csrf

                <!-- EMAIL -->
                <div>
                    <label class="text-sm text-gray-600 block mb-1">
                        Email
                    </label>

                    <div class="relative">
                        <span class="absolute left-3 top-2.75 text-gray-400 text-sm">
                            📧
                        </span>

                        <input type="email"
                               name="email"
                               value="{{ old('email') }}"
                               placeholder="Enter your email"
                               required
                               class="w-full pl-10 pr-4 py-2.5 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-400">
                    </div>
                </div>

                <!-- PASSWORD -->
                <div>
                    <label class="text-sm text-gray-600 block mb-1">
                        Password
                    </label>

                    <div class="relative">
                        <span class="absolute left-3 top-2.75 text-gray-400 text-sm">
                            🔒
                        </span>

                        <input type="password"
                               name="password"
                               placeholder="Enter your password"
                               required
                               class="w-full pl-10 pr-4 py-2.5 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-400">
                    </div>
                </div>

                <!-- LOGIN BUTTON -->
                <button type="submit"
                        class="w-full mt-3 py-3 bg-linear-to-r from-blue-600 to-blue-400 text-white rounded-lg font-semibold shadow-md hover:opacity-90 transition">
                    Log In
                </button>

            </form>

        </div>

        <!-- RIGHT SPACE -->
        <div class="flex-1"></div>

    </main>

</div>

</body>
</html>
