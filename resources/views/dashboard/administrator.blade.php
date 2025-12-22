@extends('layout.administrator')

@section('title', 'Administrator Dashboard')

@php
    $totalStudents = \App\Models\User::where('role', 'student')->count();
    $totalLecturers = \App\Models\User::where('role', 'lecturer')->count();
    $activeCourses = \App\Models\Course::count();
    $totalUsers = \App\Models\User::count();
@endphp

@section('content')

{{-- Main Content Area --}}
<div class="max-w-7xl mx-auto mt-8 p-4 md:p-8">

    {{-- Dashboard Title --}}
    <h1 class="text-3xl md:text-4xl font-extrabold text-gray-900 tracking-tight mb-8">
        Administrator Dashboard
    </h1>

    {{-- Statistics and Quick Actions Layout --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        
        {{-- Left Side: Statistics Cards (2x2 Grid) --}}
        <div class="lg:col-span-2">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                {{-- Total Students --}}
                <div class="bg-white p-6 rounded-xl shadow-md border border-gray-200 hover:shadow-lg transition">
                    <div class="flex items-center justify-between mb-4">
                        <div class="bg-indigo-100 p-3 rounded-lg">
                            <svg class="w-8 h-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <p class="text-sm text-gray-600 font-medium mb-2">Total Students</p>
                    <p class="text-4xl font-bold text-indigo-600">{{ $totalStudents }}</p>
                </div>

                {{-- Total Lecturers --}}
                <div class="bg-white p-6 rounded-xl shadow-md border border-gray-200 hover:shadow-lg transition">
                    <div class="flex items-center justify-between mb-4">
                        <div class="bg-green-100 p-3 rounded-lg">
                            <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                    </div>
                    <p class="text-sm text-gray-600 font-medium mb-2">Total Lecturers</p>
                    <p class="text-4xl font-bold text-green-600">{{ $totalLecturers }}</p>
                </div>

                {{-- Active Courses --}}
                <div class="bg-white p-6 rounded-xl shadow-md border border-gray-200 hover:shadow-lg transition">
                    <div class="flex items-center justify-between mb-4">
                        <div class="bg-teal-100 p-3 rounded-lg">
                            <svg class="w-8 h-8 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                            </svg>
                        </div>
                    </div>
                    <p class="text-sm text-gray-600 font-medium mb-2">Active Courses</p>
                    <p class="text-4xl font-bold text-teal-600">{{ $activeCourses }}</p>
                </div>

                {{-- Total Users --}}
                <div class="bg-white p-6 rounded-xl shadow-md border border-gray-200 hover:shadow-lg transition">
                    <div class="flex items-center justify-between mb-4">
                        <div class="bg-purple-100 p-3 rounded-lg">
                            <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <p class="text-sm text-gray-600 font-medium mb-2">Total Users</p>
                    <p class="text-4xl font-bold text-purple-600">{{ $totalUsers }}</p>
                </div>
            </div>
        </div>

        {{-- Right Side: Quick Actions --}}
        <div class="lg:col-span-1">
            <div class="bg-white p-6 rounded-xl shadow-md border border-gray-200">
                <h2 class="text-2xl font-bold text-gray-800 border-b pb-2 mb-6">Quick Actions</h2>
                <div class="space-y-4">
                    
                    {{-- Register Student --}}
                    <a href="{{ route('register.student') }}"
                       class="block px-4 py-3 bg-indigo-600 text-white rounded-lg font-bold hover:bg-indigo-700 transition shadow-md text-center">
                        Register Student
                    </a>

                    {{-- Register Lecturer --}}
                    <a href="{{ route('register.lecturer') }}"
                       class="block px-4 py-3 bg-green-600 text-white rounded-lg font-bold hover:bg-green-700 transition shadow-md text-center">
                        Register Lecturer
                    </a>

                    {{-- Manage Course Catalogue --}}
                    <a href="{{ route('admin.viewAllCourse') }}"
                       class="block px-4 py-3 bg-purple-600 text-white rounded-lg font-bold hover:bg-purple-700 transition shadow-md text-center">
                        Manage Course Catalogue
                    </a>

                    {{-- Add New Course --}}
                    <a href="{{ route('admin.courses.create') }}"
                       class="block px-4 py-3 bg-teal-600 text-white rounded-lg font-bold hover:bg-teal-700 transition shadow-md text-center">
                        Add New Course
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
