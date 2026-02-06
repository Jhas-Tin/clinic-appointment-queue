<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-100 font-sans text-gray-700">

<div class="flex min-h-screen">

    <!-- SIDEBAR -->
    <aside class="w-64 bg-white shadow-lg rounded-r-3xl p-6 hidden md:block">
        <div class="text-center">
            <img src="" alt="User" class="w-24 h-24 mx-auto rounded-full shadow">
            <h2 class="mt-4 font-bold text-lg text-blue-600">{{ Auth::user()->name }}</h2>
            <p class="text-xs text-gray-500">User Dashboard</p>
        </div>

        <nav class="mt-10 space-y-3 text-sm">
            <a class="flex items-center gap-3 p-3 rounded-xl hover:bg-gray-100 bg-blue-50 text-blue-600 font-semibold" href="#">
                <i class="fa fa-home"></i> Dashboard
            </a>
            <a class="flex items-center gap-3 p-3 rounded-xl hover:bg-gray-100" href="#">
                <i class="fa fa-calendar-check"></i> Appointments
            </a>
            <a class="flex items-center gap-3 p-3 rounded-xl hover:bg-gray-100" href="#">
                <i class="fa fa-credit-card"></i> Payments
            </a>
            <a class="flex items-center gap-3 p-3 rounded-xl hover:bg-gray-100" href="#">
                <i class="fa fa-user"></i> Profile
            </a>
            <form method="POST" action="/logout">
                @csrf
                <button type="submit" class="w-full flex items-center gap-3 p-3 rounded-xl text-red-500 hover:bg-red-50">
                    <i class="fa fa-sign-out"></i> Logout
                </button>
            </form>
        </nav>
    </aside>

    <!-- MAIN CONTENT -->
    <main class="flex-1 p-6 md:p-8 space-y-6">

        

        <!-- Upcoming Appointments -->
        <div class="bg-white rounded-2xl p-5 shadow-md">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Upcoming Appointments</h2>
            <div class="space-y-3">
                <div class="flex items-center justify-between py-3 px-4 bg-gray-50 rounded-md">
                    <div>
                        <p class="text-sm font-medium text-gray-800">John</p>
                        <p class="text-xs text-gray-500">12 Feb, 2026 - 10:00 AM</p>
                    </div>
                    <span class="text-xs font-semibold text-blue-600">Confirmed</span>
                </div>
                <div class="flex items-center justify-between py-3 px-4 bg-gray-50 rounded-md">
                    <div>
                        <p class="text-sm font-medium text-gray-800">Jane</p>
                        <p class="text-xs text-gray-500">15 Feb, 2026 - 02:00 PM</p>
                    </div>
                    <span class="text-xs font-semibold text-yellow-600">Pending</span>
                </div>
                <div class="flex items-center justify-between py-3 px-4 bg-gray-50 rounded-md">
                    <div>
                        <p class="text-sm font-medium text-gray-800">Emily</p>
                        <p class="text-xs text-gray-500">20 Feb, 2026 - 11:00 AM</p>
                    </div>
                    <span class="text-xs font-semibold text-red-600">Cancelled</span>
                </div>
            </div>
        </div>

        

    </main>
</div>

</body>
</html>
