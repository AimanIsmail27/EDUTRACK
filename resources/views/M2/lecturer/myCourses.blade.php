@extends('layout.lecturer')

@section('title', 'My Courses')

@section('content')
<div class="max-w-7xl mx-auto">
    {{-- Header Section --}}
    <div class="mb-10 flex flex-col lg:flex-row lg:items-end justify-between gap-6">
        <div>
            <h1 class="text-4xl font-black text-indigo-950 tracking-tight">My Teaching Courses</h1>
            <p class="text-indigo-600/70 font-bold mt-2 flex items-center">
                <span class="w-2 h-2 bg-indigo-500 rounded-full animate-pulse mr-2"></span>
                Academic Year 2024/2025 • Semester 1
            </p>
        </div>
        
        <div class="bg-white/80 backdrop-blur-md border border-indigo-100 p-4 px-8 rounded-[1.5rem] shadow-xl shadow-indigo-900/5 flex items-center gap-6">
            <div class="text-center border-r border-indigo-100 pr-6">
                <p class="text-[10px] font-black text-indigo-400 uppercase tracking-widest">Active Courses</p>
                <p class="text-xl font-black text-indigo-900">{{ $courses->count() }}</p>
            </div>
            <div class="text-center">
                <p class="text-[10px] font-black text-indigo-400 uppercase tracking-widest">Total Students</p>
                {{-- Sums up participants_count across all courses for this lecturer --}}
                <p class="text-xl font-black text-indigo-900">{{ $courses->sum('participants_count') }}</p>
            </div>
        </div>
    </div>

    {{-- Courses Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-10">
        
        @forelse($courses as $course)
        <div class="group relative bg-white rounded-[2.5rem] shadow-2xl shadow-indigo-900/10 border border-indigo-50 overflow-hidden transition-all duration-500 hover:-translate-y-3">
            {{-- Unified Indigo Header --}}
            <div class="h-40 bg-gradient-to-br from-indigo-700 to-indigo-500 relative p-8">
                <div class="absolute inset-0 opacity-20" style="background-image: radial-gradient(circle at 2px 2px, white 1px, transparent 0); background-size: 20px 20px;"></div>
                
                <div class="relative flex justify-between items-start">
                    <span class="bg-white/20 backdrop-blur-md text-white text-[10px] font-black px-3 py-1.5 rounded-full uppercase tracking-widest border border-white/30">
                        {{ $course->coordinator_id == auth()->id() ? 'Lead Coordinator' : 'Teaching Team' }}
                    </span>
                    <div class="bg-white p-3 rounded-2xl shadow-lg transform group-hover:rotate-12 transition-transform duration-500">
                        <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.247 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                        </svg>
                    </div>
                </div>
            </div>

            {{-- Floating Title Card --}}
            <div class="px-8 -mt-10 relative z-10">
                <div class="bg-white rounded-3xl p-6 shadow-xl border border-indigo-50 group-hover:border-indigo-200 transition-colors">
                    <h2 class="text-xs font-black text-indigo-500 tracking-[0.2em] uppercase mb-1">{{ $course->C_Code }}</h2>
                    <h3 class="text-xl font-black text-slate-800 leading-tight">{{ $course->C_Name }}</h3>
                </div>
            </div>

            <div class="p-8 pt-6">
                {{-- Course Meta --}}
                <div class="mb-6">
                    <div class="flex justify-between text-[10px] font-black uppercase text-slate-400 mb-2">
                        <span>Status</span>
                        <span class="text-indigo-600">Active</span>
                    </div>
                    <div class="w-full bg-slate-100 h-2 rounded-full overflow-hidden">
                        {{-- Dummy progress for now, could be linked to current date vs semester end --}}
                        <div class="bg-indigo-500 h-full w-[100%] rounded-full"></div>
                    </div>
                </div>

                {{-- Quick Metrics --}}
                <div class="flex items-center justify-between py-4 border-y border-slate-50 mb-6 text-center">
                    <div>
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Students</p>
                        <p class="text-lg font-black text-slate-700">{{ $course->participants_count }}</p>
                    </div>
                    <div class="w-px h-8 bg-slate-100"></div>
                    <div>
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Credits</p>
                        <p class="text-lg font-black text-slate-700">{{ $course->C_Hour }}.0</p>
                    </div>
                </div>

                {{-- Action Button --}}
                <a href="{{ route('lecturer.courses.show', $course->C_Code) }}" class="flex items-center justify-center w-full py-4 bg-indigo-600 text-white rounded-2xl font-bold shadow-lg shadow-indigo-100 hover:bg-indigo-700 hover:shadow-indigo-200 transition-all group active:scale-95">
                    View Course Details
                    <svg class="w-4 h-4 ml-2 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                    </svg>
                </a>
            </div>
        </div>
        @empty
        <div class="col-span-full py-20 text-center">
            <div class="bg-slate-50 rounded-[3rem] p-12 border-2 border-dashed border-slate-200">
                <p class="text-slate-400 font-bold">You are not currently assigned to any courses.</p>
            </div>
        </div>
        @endforelse

    </div>
</div>
@endsection