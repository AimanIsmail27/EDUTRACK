<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EduTrack Student | @yield('title', 'Dashboard')</title>

    <script src="https://cdn.tailwindcss.com"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('styles')

    <style>
        [x-cloak] { display:none !important; }
        .sidebar-scroll::-webkit-scrollbar { width: 4px; }
        .sidebar-scroll::-webkit-scrollbar-thumb { background: #10b981; border-radius: 4px; } 
        
        body {
            background: linear-gradient(135deg, #dbeafe 0%, #eff6ff 50%, #bfdbfe 100%); 
            background-attachment: fixed;
            min-height: 100vh;
        }
    </style>
</head>

<body class="font-sans antialiased" x-data="{ sidebarOpen: true }">

    {{-- Header --}}
    <header class="bg-white/95 backdrop-blur-md shadow-lg fixed top-0 left-0 w-full z-30 h-16 transition-all duration-300 border-b border-emerald-200"
            :class="sidebarOpen ? 'lg:pl-[250px]' : 'lg:pl-0'" >
        <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">

            <div class="flex items-center">
                <button @click="sidebarOpen = !sidebarOpen"
                    class="p-2 text-gray-700 hover:text-emerald-700 focus:ring-2 focus:ring-emerald-500 rounded-lg mr-4 transition-colors">
                    <svg x-show="!sidebarOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                    <svg x-show="sidebarOpen" x-cloak class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
                <div class="text-2xl font-black text-emerald-900 tracking-tight">
                    EduTrack <span class="bg-emerald-600 text-white text-[10px] px-2 py-0.5 rounded-md ml-1 align-middle uppercase tracking-widest font-bold">Student</span>
                </div>
            </div>

            <div class="flex items-center space-x-4">
                <div class="text-sm font-bold text-emerald-900 hidden sm:block">{{ Auth::user()->name ?? 'Student Name' }}</div>
                <div class="h-10 w-10 rounded-xl bg-emerald-600 text-white flex items-center justify-center font-black text-xs shadow-md border-2 border-emerald-200">
                    ST
                </div>
            </div>
        </div>
    </header>

    <div class="flex">
        {{-- Sidebar --}}
        <div class="fixed left-0 top-0 z-20 bg-white border-r border-emerald-200 pt-16 w-[250px] h-full sidebar-scroll overflow-y-auto transform transition-transform duration-300 ease-in-out shadow-2xl shadow-emerald-900/10"
             :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'">

            <nav class="p-4 space-y-3 mt-6">
                @php
                    $routeIs = function($names) {
                        return request()->is($names) || request()->routeIs($names)
                        ? 'bg-emerald-600 text-white shadow-lg shadow-emerald-200 font-bold' 
                        : 'text-emerald-900/70 hover:bg-emerald-50 hover:text-emerald-700 font-semibold';
                    };
                @endphp

                {{-- Dashboard --}}
                <a href="{{ route('dashboard.student') }}" class="flex items-center p-3 text-sm rounded-xl transition-all duration-200 {{ $routeIs('dashboard.student') }}">
                <a href="{{ route('dashboard.student') }}" class="flex items-center p-3 text-sm rounded-xl transition-all duration-200 {{ $routeIs('dashboard.student') }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    Dashboard
                </a>

                {{-- Your Courses (UPDATED LINK) --}}
                <a href="{{ route('student.courses') }}" class="flex items-center p-3 text-sm rounded-xl transition-all duration-200 {{ $routeIs('student.courses*') }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.247 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                    Your Courses
                </a>

                {{-- NEW: Assessment --}}
                <a href="{{ route('student.assignments.index') }}" class="flex items-center p-3 text-sm rounded-xl transition-all duration-200 {{ $routeIs('student.assignments.*') }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                    </svg>
                    Assessment
                </a>
            </nav>

            {{-- Logout --}}
            <div class="absolute bottom-0 left-0 w-full p-4 bg-emerald-50/50 border-t border-emerald-100">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="flex items-center justify-center w-full p-3 text-sm font-bold text-white bg-rose-500 rounded-xl hover:bg-rose-600 transition-all shadow-lg shadow-rose-200 group">
                        <svg class="w-5 h-5 mr-2 text-slate-300 group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a1 1 0 01-1 1H5a1 1 0 01-1-1V7a1 1 0 011-1h7a1 1 0 011 1v1"/>
                        </svg>
                        Logout
                    </button>
                </form>
            </div>
        </div>

        {{-- Main Content --}}
        <main class="flex-grow min-h-screen pt-20 pb-8 px-4 md:px-8 transition-all duration-300"
              :class="sidebarOpen ? 'lg:ml-[250px]' : 'ml-0'">
            @yield('content')
        </main>

        {{-- Mobile Overlay --}}
        <div x-show="sidebarOpen" @click="sidebarOpen = false" class="fixed inset-0 bg-emerald-950/40 backdrop-blur-sm z-10 lg:hidden" x-cloak></div>
    </div>

@stack('scripts')

@if (request()->routeIs('student.assignments.*'))
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const flashSuccess = @json(session('success'));
            const flashError = @json(session('error'));

            if (typeof Swal !== 'undefined') {
                if (flashSuccess) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: flashSuccess,
                        confirmButtonColor: '#10b981',
                    });
                } else if (flashError) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops!',
                        text: flashError,
                        confirmButtonColor: '#10b981',
                    });
                }

                document.querySelectorAll('form[data-swal-confirm]').forEach((form) => {
                    form.addEventListener('submit', (event) => {
                        if (form.dataset.swalConfirmed === '1') return;

                        event.preventDefault();

                        const title = form.getAttribute('data-swal-title') || 'Are you sure?';
                        const text = form.getAttribute('data-swal-text') || '';
                        const icon = form.getAttribute('data-swal-icon') || 'warning';
                        const confirmButtonText = form.getAttribute('data-swal-confirm-button') || 'Yes';
                        const cancelButtonText = form.getAttribute('data-swal-cancel-button') || 'Cancel';

                        Swal.fire({
                            title,
                            text,
                            icon,
                            showCancelButton: true,
                            confirmButtonText,
                            cancelButtonText,
                            confirmButtonColor: '#10b981',
                        }).then((result) => {
                            if (result.isConfirmed) {
                                form.dataset.swalConfirmed = '1';
                                form.submit();
                            }
                        });
                    });
                });
            }
        });
    </script>
@endif
</body>
</html>