@extends('layout.student')

@section('title', 'My Enrolled Courses')

@section('content')
<div class="max-w-7xl mx-auto">
    {{-- Header Section --}}
    <div class="mb-10 flex flex-col lg:flex-row lg:items-end justify-between gap-6">
        <div>
            <h1 class="text-4xl font-black text-emerald-950 tracking-tight">My Learning Journey</h1>
            <p class="text-emerald-600/70 font-bold mt-2 flex items-center">
                <span class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse mr-2"></span>
                Academic Year 2024/2025 • Semester 1
            </p>
        </div>
        
        <div class="bg-white/80 backdrop-blur-md border border-emerald-100 p-4 px-8 rounded-[1.5rem] shadow-xl shadow-emerald-900/5 flex items-center gap-6">
            <div class="text-center border-r border-emerald-100 pr-6">
                <p class="text-[10px] font-black text-emerald-400 uppercase tracking-widest">Enrolled Courses</p>
                <p class="text-xl font-black text-emerald-900">{{ $courses->count() }}</p>
            </div>
            <div class="text-center">
                <p class="text-[10px] font-black text-emerald-400 uppercase tracking-widest">Total Credits</p>
                {{-- Sums up credit hours for the student --}}
                <p class="text-xl font-black text-emerald-900">{{ $courses->sum('C_Hour') }}.0</p>
            </div>
        </div>
    </div>

    {{-- Courses Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-10">
        
        @forelse($courses as $course)
        <div class="group relative bg-white rounded-[2.5rem] shadow-2xl shadow-emerald-900/10 border border-emerald-50 overflow-hidden transition-all duration-500 hover:-translate-y-3">
            
            {{-- Emerald/Teal Header --}}
            <div class="h-40 bg-gradient-to-br from-emerald-600 to-teal-500 relative p-8">
                <div class="absolute inset-0 opacity-20" style="background-image: radial-gradient(circle at 2px 2px, white 1px, transparent 0); background-size: 20px 20px;"></div>
                
                <div class="relative flex justify-between items-start">
                    <span class="bg-white/20 backdrop-blur-md text-white text-[10px] font-black px-3 py-1.5 rounded-full uppercase tracking-widest border border-white/30">
                        Semester {{ $course->pivot->semester ?? 'N/A' }}
                    </span>
                    <div class="bg-white p-3 rounded-2xl shadow-lg transform group-hover:rotate-12 transition-transform duration-500">
                        <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.247 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                        </svg>
                    </div>
                </div>
            </div>

            {{-- Floating Title Card --}}
            <div class="px-8 -mt-10 relative z-10">
                <div class="bg-white rounded-3xl p-6 shadow-xl border border-emerald-50 group-hover:border-emerald-200 transition-colors">
                    <h2 class="text-xs font-black text-emerald-500 tracking-[0.2em] uppercase mb-1">{{ $course->C_Code }}</h2>
                    <h3 class="text-xl font-black text-slate-800 leading-tight h-14 overflow-hidden">{{ $course->C_Name }}</h3>
                </div>
            </div>

            <div class="p-8 pt-6">
                {{-- Quick Meta --}}
                <div class="flex items-center justify-between py-4 border-b border-slate-50 mb-6">
                    <div class="flex items-center">
                        <div class="w-10 h-10 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-700 text-xs font-bold mr-3 shadow-inner">
                            {{ substr($course->coordinator->name ?? '?', 0, 1) }}
                        </div>
                        <div>
                            <p class="text-[9px] font-black text-slate-400 uppercase leading-none mb-1">Coordinator</p>
                            <p class="text-xs font-bold text-slate-700 truncate w-32">{{ $course->coordinator->name ?? 'Not Assigned' }}</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-[9px] font-black text-slate-400 uppercase leading-none mb-1">Credits</p>
                        <p class="text-sm font-black text-emerald-600">{{ $course->C_Hour }}.0</p>
                    </div>
                </div>

                {{-- Action Button --}}
                <a href="{{ route('student.courses.show', $course->C_Code) }}" class="flex items-center justify-center w-full py-4 bg-emerald-600 text-white rounded-2xl font-bold shadow-lg shadow-emerald-100 hover:bg-emerald-700 transition-all group active:scale-95">
                    Enter Classroom
                    <svg class="w-4 h-4 ml-2 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                    </svg>
                </a>
            </div>
        </div>
        @empty
        <div class="col-span-full py-20 text-center">
            <div class="bg-white rounded-[3rem] p-12 border-2 border-dashed border-emerald-200">
                <div class="w-20 h-20 bg-emerald-50 rounded-full flex items-center justify-center mx-auto mb-4 text-emerald-400">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.247 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-black text-emerald-950">No Enrolled Courses</h3>
                <p class="text-slate-400 font-bold mt-2">You haven't been registered for any courses yet.</p>
            </div>
        </div>
        @endforelse

    </div>
</div>
@endsection