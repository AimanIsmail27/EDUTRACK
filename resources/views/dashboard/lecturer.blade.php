@extends('layout.lecturer')

@section('title', 'Lecturer Dashboard')

@section('content')
<div class="max-w-7xl mx-auto">
    {{-- Welcome Header --}}
    <div class="mb-8 p-8 bg-white/50 backdrop-blur-md rounded-3xl border border-white shadow-sm">
        <h1 class="text-4xl font-black text-indigo-950 tracking-tight">
            Welcome back, <span class="text-indigo-600">{{ Auth::user()->name ?? 'Professor' }}</span>!
        </h1>
        <p class="text-indigo-900/60 font-medium mt-2">Manage your curriculum and monitor student performance.</p>
    </div>

    {{-- Stats Overview Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        {{-- Card 1: Courses --}}
        <div class="bg-indigo-600 p-6 rounded-3xl shadow-lg shadow-indigo-200 flex items-center text-white">
            <div class="p-3 bg-white/20 rounded-2xl mr-4">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
            </div>
            <div>
                <p class="text-indigo-100 text-xs font-bold uppercase tracking-wider">Active Courses</p>
                <p class="text-3xl font-black">04</p>
            </div>
        </div>

        {{-- Card 2: Students --}}
        <div class="bg-white p-6 rounded-3xl shadow-sm border border-indigo-100 flex items-center">
            <div class="p-3 bg-violet-100 rounded-2xl mr-4 text-violet-600">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
            </div>
            <div>
                <p class="text-gray-400 text-xs font-bold uppercase tracking-wider">Total Students</p>
                <p class="text-3xl font-black text-gray-800">128</p>
            </div>
        </div>

        {{-- Card 3: Pending Assessment --}}
        <div class="bg-white p-6 rounded-3xl shadow-sm border border-indigo-100 flex items-center">
            <div class="p-3 bg-rose-100 rounded-2xl mr-4 text-rose-600">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
            </div>
            <div>
                <p class="text-gray-400 text-xs font-bold uppercase tracking-wider">Ungraded Items</p>
                <p class="text-3xl font-black text-gray-800">09</p>
            </div>
        </div>
    </div>

    {{-- Layout Grid --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        {{-- Schedule Section --}}
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white p-8 rounded-3xl shadow-sm border border-indigo-50">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-xl font-bold text-gray-800">Class Schedule</h3>
                    <button class="text-sm font-bold text-indigo-600 hover:text-indigo-700">View Calendar →</button>
                </div>
                <div class="space-y-4">
                    <div class="group flex items-center p-4 rounded-2xl border border-transparent hover:border-indigo-100 hover:bg-indigo-50/50 transition-all duration-300">
                        <div class="bg-indigo-600 text-white p-3 rounded-xl font-bold text-center min-w-[60px]">
                            <span class="block text-xs uppercase opacity-80">Dec</span>
                            <span class="text-lg">18</span>
                        </div>
                        <div class="ml-6">
                            <p class="text-sm font-bold text-indigo-600">10:00 AM - 12:00 PM</p>
                            <h4 class="font-bold text-gray-900">Advanced Networking (Section 02)</h4>
                            <p class="text-sm text-gray-500">Bilik Kuliah 4 • Level 2</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Sidebar Notifications --}}
        <div class="space-y-6">
            <div class="bg-indigo-900 text-white p-8 rounded-3xl shadow-lg relative overflow-hidden">
                <div class="relative z-10">
                    <h3 class="text-lg font-bold mb-2">Announcement</h3>
                    <p class="text-indigo-200 text-sm leading-relaxed mb-4">Final moderation for Semester 1 results is due by this Friday.</p>
                    <a href="#" class="inline-block bg-white text-indigo-900 px-4 py-2 rounded-xl text-xs font-bold uppercase tracking-tight">System Update</a>
                </div>
                {{-- Decorative Circle --}}
                <div class="absolute -right-10 -bottom-10 w-40 h-40 bg-white/10 rounded-full"></div>
            </div>
        </div>
    </div>
</div>
@endsection