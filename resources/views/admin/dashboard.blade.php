<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title>Doctor Dashboard</title>
<script src="https://cdn.tailwindcss.com"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body class="bg-[#f5f7fb] font-sans text-gray-700">

<div class="flex min-h-screen">

    <!-- SIDEBAR -->
    <aside class="w-64 bg-white shadow-lg rounded-r-3xl p-6">
        <div class="text-center">
            <img src="" class="w-24 h-24 mx-auto rounded-full shadow">
            <h2 class="mt-4 font-bold text-lg text-blue-600">Admin</h2>
            <p class="text-xs text-gray-500"></p>
        </div>

        <nav class="mt-10 space-y-3 text-sm">
            <a class="flex items-center gap-3 p-3 rounded-xl bg-blue-50 text-blue-600 font-semibold" href="#">
                <i class="fa fa-chart-pie"></i> Dashboard
            </a>
            <a class="flex items-center gap-3 p-3 rounded-xl hover:bg-gray-100" href="#">
                <i class="fa fa-calendar-check"></i> Appointment
            </a>
            <a class="flex items-center gap-3 p-3 rounded-xl hover:bg-gray-100" href="#">
                <i class="fa fa-credit-card"></i> Payment
            </a>
            <a class="flex items-center gap-3 p-3 rounded-xl hover:bg-gray-100" href="#">
                <i class="fa fa-user"></i> Profile
            </a>
            <a class="flex items-center gap-3 p-3 rounded-xl hover:bg-gray-100" href="#">
                <i class="fa fa-cog"></i> Settings
            </a>
            <form method="POST" action="/logout">
                @csrf
                <button type="submit"
                    class="w-full flex items-center gap-3 p-3 rounded-xl text-red-500 hover:bg-red-50">
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
                <h3 class="text-sm font-medium text-gray-700 mb-4">Patients Summary December 2021</h3>
                <div class="flex-1 flex items-center justify-center">
                    <canvas id="patientsChart" class="max-h-52"></canvas>
                </div>
                <div class="flex gap-6 justify-center mt-4">
                    <div class="flex items-center gap-2"><div class="w-3 h-3 bg-amber-400 rounded-sm"></div><span class="text-xs text-gray-600">New Patients</span></div>
                    <div class="flex items-center gap-2"><div class="w-3 h-3 bg-purple-200 rounded-sm"></div><span class="text-xs text-gray-600">Old Patients</span></div>
                    <div class="flex items-center gap-2"><div class="w-3 h-3 bg-blue-900 rounded-sm"></div><span class="text-xs text-gray-600">Total Patients</span></div>
                </div>
            </div>

            <!-- Today Appointment -->
            <div class="bg-white rounded-2xl p-5 shadow-sm flex flex-col h-full">
                <h3 class="text-sm font-medium text-blue-700 mb-3">Today Appointment</h3>
                <div class="mb-2">
                    <div class="grid grid-cols-[80px_1fr_80px] gap-4 text-xs font-medium text-gray-600 pb-2">
                        <span>Patient</span><span>Name/Diagnosis</span><span class="text-right">Time</span>
                    </div>
                </div>
                <div class="space-y-2 flex-1 overflow-auto">
                    <!-- ROWS -->
                    <div class="grid grid-cols-[80px_1fr_80px] gap-4 items-center py-2">
                        <img src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=100" class="w-10 h-10 rounded-full object-cover" />
                        <div><p class="text-sm font-medium text-gray-800">M.J. Mical</p><p class="text-xs text-gray-500">Health Checkup</p></div>
                        <div class="text-right"><span class="inline-block px-3 py-1 bg-blue-500 text-white text-xs rounded-md">On Going</span></div>
                    </div>
                    <div class="grid grid-cols-[80px_1fr_80px] gap-4 items-center py-2">
                        <img src="https://images.unsplash.com/photo-1500648767791-00dcc994a43e?w=100" class="w-10 h-10 rounded-full object-cover" />
                        <div><p class="text-sm font-medium text-gray-800">Sanath Deo</p><p class="text-xs text-gray-500">Health Checkup</p></div>
                        <div class="text-right text-sm font-medium text-blue-600">12:30 PM</div>
                    </div>
                    <div class="grid grid-cols-[80px_1fr_80px] gap-4 items-center py-2">
                        <img src="https://images.unsplash.com/photo-1494790108377-be9c29b29330?w=100" class="w-10 h-10 rounded-full object-cover" />
                        <div><p class="text-sm font-medium text-gray-800">Loeara Phanj</p><p class="text-xs text-gray-500">Report</p></div>
                        <div class="text-right text-sm font-medium text-blue-600">01:00 PM</div>
                    </div>
                    <div class="grid grid-cols-[80px_1fr_80px] gap-4 items-center py-2">
                        <img src="https://images.unsplash.com/photo-1506794778202-cad84cf45f1d?w=100" class="w-10 h-10 rounded-full object-cover" />
                        <div><p class="text-sm font-medium text-gray-800">Komola Haris</p><p class="text-xs text-gray-500">Common Cold</p></div>
                        <div class="text-right text-sm font-medium text-blue-600">01:30 PM</div>
                    </div>
                </div>
                <button class="w-full mt-3 text-blue-600 text-sm font-medium hover:underline">See All</button>
            </div>

            <!-- Next Patient Details -->
            <div class="bg-white rounded-2xl p-5 shadow-sm flex flex-col h-full">
                <h3 class="text-sm font-medium text-blue-700 mb-3">Next Patient Details</h3>
                <div class="flex items-start gap-3 mb-4">
                    <img src="https://images.unsplash.com/photo-1500648767791-00dcc994a43e?w=100" class="w-12 h-12 rounded-full object-cover" />
                    <div class="flex-1">
                        <h4 class="font-semibold text-gray-800">Sanath Deo</h4>
                        <p class="text-xs text-gray-500">Health Checkup</p>
                    </div>
                    <div class="text-right">
                        <p class="text-[10px] font-medium text-gray-600">Patient ID</p>
                        <p class="text-xs text-gray-800">0220092020005</p>
                    </div>
                </div>
                <div class="grid grid-cols-3 gap-x-4 gap-y-3 mb-4 text-xs">
                    <div><p class="text-gray-500 mb-0.5">D.O.B</p><p class="text-gray-800">15 January 1989</p></div>
                    <div><p class="text-gray-500 mb-0.5">Sex</p><p class="text-gray-800">Male</p></div>
                    <div><p class="text-gray-500 mb-0.5">Weight</p><p class="text-gray-800">59 Kg</p></div>
                    <div><p class="text-gray-500 mb-0.5">Last Appointment</p><p class="text-gray-800">15 Dec - 2021</p></div>
                    <div><p class="text-gray-500 mb-0.5">Height</p><p class="text-gray-800">172 cm</p></div>
                    <div><p class="text-gray-500 mb-0.5">Reg. Date</p><p class="text-gray-800">10 Dec 2021</p></div>
                </div>
                <div class="mb-4">
                    <p class="text-xs text-gray-700 font-medium mb-2">Patient History</p>
                    <div class="flex gap-2 flex-wrap">
                        <span class="px-3 py-1 bg-blue-100 text-blue-700 text-xs rounded-full">Hypertension</span>
                        <span class="px-3 py-1 bg-red-100 text-red-700 text-xs rounded-full">Fever</span>
                    </div>
                </div>
                <div class="flex gap-2 mb-4">
                    <button class="flex-1 flex items-center justify-center gap-2 px-3 py-2 bg-blue-600 text-white text-xs rounded-lg hover:bg-blue-700 transition">📞 (+88) 555-6762</button>
                    <button class="px-3 py-2 border-2 border-blue-600 text-blue-600 rounded-lg hover:bg-blue-50 transition">📄</button>
                    <button class="px-3 py-2 border-2 border-blue-600 text-blue-600 rounded-lg hover:bg-blue-50 transition">💬</button>
                </div>
                <div class="flex-1">
                    <h4 class="text-xs font-medium text-gray-700 mb-2">Last Prescriptions</h4>
                    <div class="bg-linear-to-r from-blue-50 to-purple-50 rounded-lg p-3 text-xs text-gray-600 h-20 flex items-center justify-center">
                        No prescription data available
                    </div>
                </div>
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

            <!-- Appointment Request -->
            <div class="bg-white rounded-2xl p-5 shadow-sm flex flex-col h-full">
                <h3 class="text-sm font-medium text-blue-700 mb-3">Appointment Request</h3>
                <div class="space-y-3 flex-1">
                    <!-- Request 1 -->
                    <div class="flex items-center gap-3">
                        <img src="https://images.unsplash.com/photo-1438761681033-6461ffad8d80?w=100" class="w-12 h-12 rounded-full object-cover">
                        <div class="flex-1">
                            <h4 class="font-medium text-gray-800 text-sm">Maria Sarafat</h4>
                            <p class="text-xs text-gray-500">Cold</p>
                        </div>
                        <div class="flex gap-1.5">
                            <button class="w-8 h-8 flex items-center justify-center bg-blue-100 hover:bg-blue-200 rounded-md">✔</button>
                            <button class="w-8 h-8 flex items-center justify-center bg-red-100 hover:bg-red-200 rounded-md">✖</button>
                            <button class="w-8 h-8 flex items-center justify-center bg-cyan-100 hover:bg-cyan-200 rounded-md">📅</button>
                        </div>
                    </div>
                    <!-- Request 2 -->
                    <div class="flex items-center gap-3">
                        <img src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=100" class="w-12 h-12 rounded-full object-cover">
                        <div class="flex-1">
                            <h4 class="font-medium text-gray-800 text-sm">Jhon Deo</h4>
                            <p class="text-xs text-gray-500">Over waiting</p>
                        </div>
                        <div class="flex gap-1.5">
                            <button class="w-8 h-8 flex items-center justify-center bg-blue-100 hover:bg-blue-200 rounded-md">✔</button>
                            <button class="w-8 h-8 flex items-center justify-center bg-red-100 hover:bg-red-200 rounded-md">✖</button>
                            <button class="w-8 h-8 flex items-center justify-center bg-cyan-100 hover:bg-cyan-200 rounded-md">📅</button>
                        </div>
                    </div>
                </div>
                <button class="w-full mt-3 text-blue-600 text-sm font-medium hover:underline">See All</button>
            </div>

            <!-- Calendar -->
            <div class="bg-white rounded-2xl p-5 shadow-sm flex flex-col h-full">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-sm font-medium text-blue-700">Calendar</h3>
                    <span class="text-xs text-gray-500">December - 2021</span>
                </div>
                <div class="grid grid-cols-7 gap-1 flex-1 text-xs text-center">
                    <!-- Days -->
                    <div class="font-medium text-gray-600">Sa</div>
                    <div class="font-medium text-gray-600">Su</div>
                    <div class="font-medium text-gray-600">Mo</div>
                    <div class="font-medium text-gray-600">Tu</div>
                    <div class="font-medium text-gray-600">We</div>
                    <div class="font-medium text-gray-600">Th</div>
                    <div class="font-medium text-gray-600">Fr</div>
                    <!-- Dates -->
                    <div></div><div></div><div></div>
                    <div>1</div><div>2</div><div>3</div><div>4</div>
                    <div>5</div><div>6</div><div>7</div><div>8</div><div>9</div><div>10</div><div>11</div>
                    <div>12</div><div>13</div><div>14</div><div>15</div><div>16</div><div>17</div><div>18</div>
                    <div>19</div><div>20</div>
                    <div class="bg-blue-600 text-white font-semibold rounded-lg">21</div>
                    <div>22</div><div>23</div><div>24</div><div>25</div>
                    <div>26</div><div>27</div><div>28</div><div>29</div><div>30</div><div>31</div>
                </div>
            </div>

        </div>

    </main>
</div>

<script>
const ctx = document.getElementById('patientsChart');

new Chart(ctx, {
    type: 'doughnut',
    data: {
        labels: ['Total Patients', 'Old Patients', 'New Patients'],
        datasets: [{
            data: [45, 30, 25],
            backgroundColor: [
                '#1e3a8a', // dark blue
                '#fbbf24', // amber
                '#ddd6fe'  // light purple
            ],
            borderWidth: 5,
            borderColor: '#ffffff'
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        cutout: '65%',
        plugins: {
            legend: { display: false }
        }
    }
});
</script>


</body>
</html>