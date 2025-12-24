@extends('layout.student')

@section('title', 'Your Courses')

@section('content')
@php
    $totalCourses = $stats['total_courses'] ?? 0;
    $totalAssignments = $stats['total_assignments'] ?? 0;
    $avgHours = $stats['avg_hours'] ?? 0;
@endphp
<div class="max-w-6xl mx-auto space-y-10">
    <section class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-emerald-50 via-white to-cyan-50 border border-emerald-100 p-8 shadow-lg shadow-emerald-900/5">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6">
            <div>
                <p class="text-xs font-black uppercase tracking-[0.4em] text-emerald-400">Your Courses</p>
                <h1 class="text-3xl md:text-4xl font-black text-slate-900 mt-2">Track every class in one view.</h1>
                <p class="text-slate-600 mt-3 text-sm md:text-base max-w-2xl">
                    {{ $personalized ? 'These are the courses you are registered in this academic year. Jump in to review briefs, lecturers, and current workload.' : 'We could not find a linked student profile, so here are featured courses available on EduTrack.' }}
                </p>
            </div>
            <div class="bg-white rounded-2xl p-6 shadow-inner border border-emerald-100 text-center">
                <p class="text-xs font-bold uppercase tracking-[0.3em] text-emerald-400">Active Courses</p>
                <p class="text-4xl font-black text-emerald-600 mt-2">{{ str_pad($totalCourses, 2, '0', STR_PAD_LEFT) }}</p>
                <p class="text-xs font-semibold text-slate-500 mt-1">{{ $personalized ? 'Linked to your matric ID' : 'Sample listing' }}</p>
            </div>
        </div>
        <div class="absolute -right-10 -top-10 w-40 h-40 bg-emerald-200/40 rounded-full blur-3xl"></div>
    </section>

    <section class="grid grid-cols-1 md:grid-cols-3 gap-5">
        <div class="bg-white rounded-2xl border border-emerald-100 p-6 shadow-md">
            <p class="text-xs font-semibold uppercase tracking-[0.3em] text-emerald-400">Assignments</p>
            <p class="text-3xl font-black text-slate-900 mt-2">{{ $totalAssignments }}</p>
            <p class="text-sm text-slate-500 mt-1">Total assessments across these courses</p>
        </div>
        <div class="bg-white rounded-2xl border border-emerald-100 p-6 shadow-md">
            <p class="text-xs font-semibold uppercase tracking-[0.3em] text-blue-400">Study Load</p>
            <p class="text-3xl font-black text-slate-900 mt-2">{{ number_format($avgHours, 1) }} hrs</p>
            <p class="text-sm text-slate-500 mt-1">Average credit hours per course</p>
        </div>
        <div class="bg-white rounded-2xl border border-emerald-100 p-6 shadow-md">
            <p class="text-xs font-semibold uppercase tracking-[0.3em] text-amber-400">Progress</p>
            <p class="text-3xl font-black text-slate-900 mt-2">{{ $personalized ? 'On Track' : 'Preview' }}</p>
            <p class="text-sm text-slate-500 mt-1">{{ $personalized ? 'Keep an eye on upcoming work' : 'Connect your matric ID for insights' }}</p>
        </div>
    </section>

    <section>
        <div class="flex items-center justify-between mb-4">
            <div>
                <p class="text-xs uppercase font-bold tracking-[0.35em] text-slate-400">Course List</p>
                <h2 class="text-2xl font-black text-slate-900">{{ $personalized ? 'Enrolled this semester' : 'Featured catalog' }}</h2>
            </div>
            <a href="{{ route('student.assignments.index') }}" class="inline-flex items-center gap-2 text-sm font-bold text-emerald-700 hover:text-emerald-500">
                Go to assessments
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @forelse ($courses as $course)
                @php
                    $semester = optional($course->pivot)->semester;
                    $year = optional($course->pivot)->year;
                    $badge = $semester ? 'Semester ' . $semester : 'Core Module';
                    $description = $course->C_Description ?: 'No description has been added for this course yet.';
                @endphp
                <article class="bg-white border border-slate-100 rounded-3xl p-6 shadow-lg shadow-emerald-900/5 flex flex-col">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-[10px] uppercase tracking-[0.4em] text-emerald-400 font-black">{{ $badge }}</p>
                            <h3 class="text-xl font-black text-slate-900 mt-2">{{ $course->C_Code }} · {{ $course->C_Name }}</h3>
                            <p class="text-sm text-slate-500 mt-1">{{ $course->C_Instructor ? 'Lecturer: ' . $course->C_Instructor : 'Lecturer TBD' }}</p>
                        </div>
                        <span class="px-3 py-1 text-[11px] font-bold rounded-full bg-emerald-50 text-emerald-600 border border-emerald-100">
                            {{ $course->participants_count }} students
                        </span>
                    </div>

                    <p class="text-sm text-slate-600 mt-4 leading-relaxed">{{ \Illuminate\Support\Str::limit($description, 160) }}</p>

                    <dl class="mt-5 grid grid-cols-3 gap-3 text-center text-xs font-semibold text-slate-500">
                        <div class="bg-slate-50 rounded-2xl p-3">
                            <dt class="uppercase tracking-[0.3em] text-slate-400">Credits</dt>
                            <dd class="text-lg text-slate-900 font-black">{{ $course->C_Hour ?? '—' }}</dd>
                        </div>
                        <div class="bg-slate-50 rounded-2xl p-3">
                            <dt class="uppercase tracking-[0.3em] text-slate-400">Assignments</dt>
                            <dd class="text-lg text-slate-900 font-black">{{ $course->assignments_count }}</dd>
                        </div>
                        <div class="bg-slate-50 rounded-2xl p-3">
                            <dt class="uppercase tracking-[0.3em] text-slate-400">Year</dt>
                            <dd class="text-lg text-slate-900 font-black">{{ $year ?? '—' }}</dd>
                        </div>
                    </dl>

                    <div class="mt-6 flex items-center justify-between text-sm">
                        <div class="flex items-center gap-2 text-slate-500">
                            <span class="inline-flex h-2 w-2 rounded-full {{ $course->assignments_count > 0 ? 'bg-emerald-400' : 'bg-slate-300' }}"></span>
                            {{ $course->assignments_count > 0 ? 'Assessments available' : 'No assessments yet' }}
                        </div>
                        <a href="{{ route('student.assignments.index') }}?course={{ urlencode($course->C_Code) }}" class="inline-flex items-center text-emerald-600 font-bold hover:text-emerald-400">
                            Open details
                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </a>
                    </div>
                </article>
            @empty
                <div class="col-span-1 md:col-span-2 bg-white border border-dashed border-emerald-200 rounded-3xl p-10 text-center">
                    <p class="text-2xl font-black text-slate-800">No courses to display yet.</p>
                    <p class="text-sm text-slate-500 mt-2">Once the administrator links your matric ID to courses, they will appear here automatically.</p>
                </div>
            @endforelse
        </div>
    </section>
</div>
@endsection
