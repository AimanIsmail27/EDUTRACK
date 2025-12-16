@extends('layout.administrator')

@section('title', 'Administrator Dashboard')

@section('content')

{{-- Main Content Area: Centered and spaced --}}
<div class="max-w-7xl mx-auto mt-8 p-4 md:p-8">

    {{-- Dashboard Title --}}
    <h1 class="text-3xl md:text-4xl font-extrabold text-gray-900 tracking-tight mb-8 border-b pb-3">
        Administrator Dashboard
    </h1>

    {{-- 1. KPI/Metric Cards Row (Mimicking the top row of image_a99aed.jpg) --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-12">
        {{-- Dummy KPI Card 1 (Total Students) --}}
        <div class="bg-white p-6 rounded-xl shadow-lg border border-gray-100/80 transition duration-300 hover:shadow-xl">
            <p class="text-sm text-gray-500 font-medium">Total Students</p>
            <p class="text-4xl font-bold text-indigo-600 mt-1">4,520</p>
        </div>
        {{-- Dummy KPI Card 2 (Total Lecturers) --}}
        <div class="bg-white p-6 rounded-xl shadow-lg border border-gray-100/80 transition duration-300 hover:shadow-xl">
            <p class="text-sm text-gray-500 font-medium">Total Lecturers</p>
            <p class="text-4xl font-bold text-green-600 mt-1">185</p>
        </div>
        {{-- Dummy KPI Card 3 (Active Courses) --}}
        <div class="bg-white p-6 rounded-xl shadow-lg border border-gray-100/80 transition duration-300 hover:shadow-xl">
            <p class="text-sm text-gray-500 font-medium">Active Courses</p>
            <p class="text-4xl font-bold text-yellow-600 mt-1">45</p>
        </div>
        {{-- Dummy KPI Card 4 (Pending Actions) --}}
        <div class="bg-white p-6 rounded-xl shadow-lg border border-gray-100/80 transition duration-300 hover:shadow-xl">
            <p class="text-sm text-gray-500 font-medium">Pending Actions</p>
            <p class="text-4xl font-bold text-red-600 mt-1">7</p>
        </div>
    </div>


    {{-- 2. Main Dashboard Content Row --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

        {{-- Left Column: Role Shortcut Cards Container --}}
        <div class="lg:col-span-2 p-6 bg-white rounded-xl shadow-lg border border-gray-100/80">
            <h2 class="text-2xl font-bold text-gray-800 border-b pb-2 mb-6">Role Dashboards</h2>
            <div class="flex flex-wrap gap-8 justify-start">
                
                {{-- Student Dashboard Card (Containerized) --}}
                <a href="{{ route('dashboard.student') ?? '#' }}"
                   class="flex flex-col items-center justify-center w-48 h-40 bg-gray-50 border border-gray-200 rounded-xl shadow-md hover:shadow-lg hover:bg-gray-100 transition duration-300 p-6 text-gray-800 text-center">
                    <div class="text-5xl text-teal-600 mb-2">🎓</div>
                    <div class="text-lg font-semibold mt-2">Student</div>
                </a>

                {{-- Lecturer Dashboard Card (Containerized) --}}
                <a href="{{ route('dashboard.lecturer') ?? '#' }}"
                   class="flex flex-col items-center justify-center w-48 h-40 bg-gray-50 border border-gray-200 rounded-xl shadow-md hover:shadow-lg hover:bg-gray-100 transition duration-300 p-6 text-gray-800 text-center">
                    <div class="text-5xl text-indigo-600 mb-2">👨‍🏫</div>
                    <div class="text-lg font-semibold mt-2">Lecturer</div>
                </a>
            </div>
        </div>


        {{-- Right Column: Quick Actions Container --}}
        <div class="lg:col-span-1">
            <div class="p-6 bg-white rounded-xl shadow-lg border border-gray-100/80">
                <h2 class="text-2xl font-bold text-gray-800 border-b pb-2 mb-6">Quick Actions</h2>
                <div class="space-y-4">
                    
                    {{-- Register New Student --}}
                    <a href="{{ '#' }}"
                       class="block px-4 py-3 bg-indigo-600 text-white rounded-lg font-bold hover:bg-indigo-700 transition shadow-md text-center">
                        Register New Student
                    </a>

                    {{-- Add New Course --}}
                    <a href="{{ route('admin.courses.create') }}"
                       class="block px-4 py-3 bg-teal-600/90 text-white rounded-lg font-bold hover:bg-teal-700 transition shadow-md text-center">
                        Add New Course
                    </a>
                    
                    {{-- View System Logs (Placeholder) --}}
                    <a href="{{ '#' }}"
                       class="block px-4 py-3 bg-gray-100 text-gray-700 rounded-lg font-bold hover:bg-gray-200 transition text-center border border-gray-300">
                        View System Logs
                    </a>
                </div>
            </div>
            
            {{-- Shortcut to Course Catalogue (Separate link) --}}
            <div class="mt-4">
                <a href="{{ route('admin.viewAllCourse') }}"
                   class="block px-4 py-3 bg-gray-100 text-gray-700 rounded-lg font-bold hover:bg-gray-200 transition text-center border border-gray-300 shadow-md">
                    Manage Course Catalogue
                </a>
            </div>
        </div>
    </div>
</div>
@endsection