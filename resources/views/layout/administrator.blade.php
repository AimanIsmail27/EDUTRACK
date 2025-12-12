<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EduTrack Admin | @yield('title', 'Dashboard')</title>

    <!-- Tailwind -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Alpine.js -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script defer src="https://unpkg.com/@alpinejs/collapse@3.x.x/dist/cdn.min.js"></script>

    <style>
        [x-cloak] { display:none !important; }
        .sidebar-scroll::-webkit-scrollbar { width: 4px; }
        .sidebar-scroll::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
    </style>
</head>

<body class="bg-gray-100 font-sans antialiased" x-data="{ sidebarOpen: true }">

    <!-- HEADER -->
    <header class="bg-white shadow-md fixed top-0 left-0 w-full z-30 h-16">
        <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">

            <!-- Hamburger -->
            <button @click="sidebarOpen = !sidebarOpen"
                class="p-2 text-gray-500 hover:text-gray-700 focus:ring-2 focus:ring-indigo-500 rounded-lg">

                <!-- Open Icon -->
                <svg x-show="!sidebarOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                          d="M4 6h16M4 12h16M4 18h16"/>
                </svg>

                <!-- Close Icon -->
                <svg x-show="sidebarOpen" x-cloak class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>

            <div class="text-2xl font-extrabold text-gray-800 tracking-wider">EduTrack</div>

            <div class="flex items-center space-x-4">
                <div class="text-sm font-medium text-gray-700 hidden sm:block">Admin User</div>
                <div class="h-8 w-8 rounded-full bg-indigo-600 text-white flex items-center justify-center font-semibold text-xs">
                    AD
                </div>
            </div>
        </div>
    </header>


    <div class="flex">

        <!-- SIDEBAR -->
        <div class="
            fixed left-0 top-16 z-20 bg-white shadow-xl
            w-[250px] h-[calc(100vh-4rem)]
            sidebar-scroll overflow-y-auto
            transform transition-transform duration-300 ease-in-out
        "
        :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
        >

            <nav class="p-4 space-y-2">

                <a href="#" class="flex items-center p-3 text-base font-semibold text-indigo-700 bg-indigo-100 rounded-lg hover:bg-indigo-50">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    Dashboard
                </a>

                <!-- Register User -->
                <div x-data="{ open: false }" class="space-y-1">
                    <button @click="open = !open" class="w-full flex justify-between items-center p-3 text-base font-semibold text-gray-700 hover:bg-indigo-50 hover:text-indigo-600 rounded-lg">
                        <span class="flex items-center">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            Register User
                        </span>
                        <svg class="w-4 h-4 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>

                    <div x-show="open" x-collapse.duration.300ms class="pl-4 space-y-1" x-cloak>
                        <a href="#" class="block p-2 text-sm text-gray-600 hover:bg-indigo-100 hover:text-indigo-700 rounded-lg">Student</a>
                        <a href="#" class="block p-2 text-sm text-gray-600 hover:bg-indigo-100 hover:text-indigo-700 rounded-lg">Lecturer</a>
                    </div>
                </div>

                <!-- Manage Courses -->
                <div x-data="{ open: false }" class="space-y-1">
                    <button @click="open = !open" class="w-full flex justify-between items-center p-3 text-base font-semibold text-gray-700 hover:bg-indigo-50 hover:text-indigo-600 rounded-lg">
                        <span class="flex items-center">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            Manage Courses
                        </span>
                        <svg class="w-4 h-4 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>

                    <div x-show="open" x-collapse.duration.300ms class="pl-4 space-y-1" x-cloak>
                        <a href="#" class="block p-2 text-sm text-gray-600 hover:bg-indigo-100 hover:text-indigo-700 rounded-lg">View All Courses</a>
                        <a href="#" class="block p-2 text-sm text-gray-600 hover:bg-indigo-100 hover:text-indigo-700 rounded-lg">Add New Courses</a>
                    </div>
                </div>

            </nav>

            <!-- LOGOUT BUTTON -->
            <div class="absolute bottom-0 left-0 w-full p-4 bg-white border-t">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                        class="flex items-center w-full p-3 text-base font-semibold text-red-600 bg-red-100 rounded-lg hover:bg-red-200">

                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a1 1 0 01-1 1H5a1 1 0 01-1-1V7a1 1 0 011-1h7a1 1 0 011 1v1"/>
                        </svg>

                        Logout
                    </button>
                </form>
            </div>

        </div>

        <!-- MAIN CONTENT -->
        <main class="flex-grow min-h-screen pt-20 pb-8 px-4 md:px-8 transition-all duration-300"
              :style="sidebarOpen ? 'margin-left:250px' : 'margin-left:0'">
            @yield('content')
        </main>

        <!-- MOBILE OVERLAY -->
        <div 
            x-show="sidebarOpen" 
            @click="sidebarOpen = false"
            class="fixed inset-0 bg-black bg-opacity-50 z-10 lg:hidden"
            x-cloak
        ></div>

    </div>

</body>
</html>
