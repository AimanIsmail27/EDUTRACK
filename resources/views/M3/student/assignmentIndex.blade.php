@extends('layout.student')

@section('title', 'Assignments')

@section('content')
<div class="max-w-6xl mx-auto space-y-8">
    <div class="flex flex-col gap-2">
        <p class="text-xs font-semibold tracking-widest text-emerald-500 uppercase">Assessment Module · M3</p>
        <h1 class="text-3xl font-black text-emerald-900">Assignments</h1>
        <p class="text-emerald-900/70">All published assessments across your courses, with due dates and submission status.</p>
    </div>

    <div class="space-y-4">
        @forelse ($assignments as $assignment)
            @php $submission = $assignment->submissions->first(); @endphp
            <div class="bg-white rounded-3xl shadow-lg border border-emerald-50 px-6 py-5 flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <div class="flex items-center gap-3">
                        <h2 class="text-xl font-black text-emerald-900">{{ $assignment->title }}</h2>
                        @php
                            $statusColor = [
                                'Published' => 'bg-emerald-100 text-emerald-700',
                                'Scheduled' => 'bg-blue-100 text-blue-700',
                                'Closed' => 'bg-slate-200 text-slate-700',
                            ][$assignment->status] ?? 'bg-slate-100 text-slate-600';
                        @endphp
                        <span class="px-3 py-1 text-xs font-bold rounded-full {{ $statusColor }}">{{ $assignment->status }}</span>
                    </div>
                    <p class="text-sm text-gray-500 mt-1">
                        {{ optional($assignment->course)->C_Name ? $assignment->course->C_Name . ' • ' . $assignment->course->C_Code : 'Course TBD' }}
                    </p>
                    <p class="text-sm font-semibold text-emerald-800 mt-2">
                        Due: {{ $assignment->due_at ? $assignment->due_at->format('d M Y · h:i A') : 'No due date' }}
                    </p>
                    <p class="text-xs text-gray-500 mt-1">
                        @if ($submission)
                            Submitted {{ optional($submission->submitted_at)->diffForHumans() }} ({{ $submission->status }})
                        @else
                            Not submitted yet
                        @endif
                    </p>
                </div>
                <div class="mt-4 sm:mt-0">
                    <a href="{{ route('student.assignments.show', $assignment) }}" class="inline-flex items-center px-5 py-3 text-sm font-bold text-white bg-emerald-600 rounded-2xl shadow-lg shadow-emerald-300/60 hover:bg-emerald-700">
                        View & Submit
                    </a>
                </div>
            </div>
        @empty
            <div class="bg-white rounded-3xl border border-emerald-50 p-8 text-center text-sm text-gray-500">
                No assignments available right now.
            </div>
        @endforelse
    </div>
</div>
@endsection
