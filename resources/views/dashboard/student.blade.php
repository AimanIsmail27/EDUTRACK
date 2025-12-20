@extends('layout.student')

@section('title', 'Student Dashboard')

@section('content')
<div class="max-w-7xl mx-auto">
    {{-- Welcome Header --}}
    <div class="mb-8">
        <h1 class="text-3xl font-black text-emerald-900 tracking-tight">
            Happy Learning, {{ Auth::user()->name ?? 'Student' }}! 🎓
        </h1>
        <p class="text-emerald-700 font-medium mt-1">You are currently enrolled in 4 courses this semester.</p>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        {{-- Enrolled Courses --}}
        <div class="bg-white p-6 rounded-2xl shadow-xl shadow-emerald-900/10 border-b-4 border-emerald-500">
            <div class="flex items-center">
                <div class="p-3 bg-emerald-100 rounded-xl mr-4 text-emerald-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.247 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                </div>
                <div>
                    <p class="text-slate-400 text-xs font-bold uppercase tracking-wider">Enrolled</p>
                    <p class="text-2xl font-black text-slate-800">04 Courses</p>
                </div>
            </div>
        </div>

        {{-- Completed Assignments --}}
        <div class="bg-white p-6 rounded-2xl shadow-xl shadow-emerald-900/10 border-b-4 border-blue-500">
            <div class="flex items-center">
                <div class="p-3 bg-blue-100 rounded-xl mr-4 text-blue-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
                </div>
                <div>
                    <p class="text-slate-400 text-xs font-bold uppercase tracking-wider">Tasks Done</p>
                    <p class="text-2xl font-black text-slate-800">18 / 24</p>
                </div>
            </div>
        </div>

        {{-- GPA/Grade Estimate --}}
        <div class="bg-white p-6 rounded-2xl shadow-xl shadow-emerald-900/10 border-b-4 border-amber-500">
            <div class="flex items-center">
                <div class="p-3 bg-amber-100 rounded-xl mr-4 text-amber-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                </div>
                <div>
                    <p class="text-slate-400 text-xs font-bold uppercase tracking-wider">Current Standing</p>
                    <p class="text-2xl font-black text-slate-800">Good Standing</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Student Content Grid --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        {{-- Course Progress List --}}
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white p-8 rounded-3xl shadow-lg border border-emerald-100">
                <h3 class="text-xl font-bold text-slate-800 mb-6">Course Progress</h3>
                <div class="space-y-6">
                    {{-- Course 1 --}}
                    <div>
                        <div class="flex justify-between mb-2">
                            <span class="text-sm font-bold text-slate-700 uppercase">Advanced Web Development</span>
                            <span class="text-sm font-bold text-emerald-600">75%</span>
                        </div>
                        <div class="w-full bg-slate-100 rounded-full h-3">
                            <div class="bg-emerald-500 h-3 rounded-full" style="width: 75%"></div>
                        </div>
                    </div>
                    {{-- Course 2 --}}
                    <div>
                        <div class="flex justify-between mb-2">
                            <span class="text-sm font-bold text-slate-700 uppercase">Database Management</span>
                            <span class="text-sm font-bold text-emerald-600">40%</span>
                        </div>
                        <div class="w-full bg-slate-100 rounded-full h-3">
                            <div class="bg-emerald-400 h-3 rounded-full" style="width: 40%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Upcoming Deadlines --}}
        <div class="space-y-6">
            <div class="bg-slate-800 text-white p-8 rounded-3xl shadow-xl">
                <h3 class="text-lg font-bold mb-4 flex items-center">
                    <span class="mr-2">⏰</span> Upcoming Deadlines
                </h3>
                <div class="space-y-4">
                    <div class="border-l-4 border-emerald-400 pl-4">
                        <p class="text-xs text-emerald-300 font-bold uppercase">Dec 20 • 11:59 PM</p>
                        <p class="text-sm font-medium">Final Group Project Submission</p>
                    </div>
                    <div class="border-l-4 border-amber-400 pl-4">
                        <p class="text-xs text-amber-300 font-bold uppercase">Dec 22 • 02:00 PM</p>
                        <p class="text-sm font-medium">Individual Assignment: Database Design</p>
                    </div>
                </div>
                <a href="{{ route('student.assignments.index') }}" class="w-full mt-6 inline-flex justify-center py-2 bg-emerald-500 text-white rounded-xl text-xs font-bold uppercase hover:bg-emerald-400 transition-colors">
                    View All Assessments
                </a>
            </div>
        </div>

    </div>
</div>
@endsection