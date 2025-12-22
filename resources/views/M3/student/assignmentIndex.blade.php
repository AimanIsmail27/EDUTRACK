@extends('layout.student')

@section('title', 'Assignments')

@section('content')
<div class="max-w-6xl mx-auto space-y-8">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div class="flex flex-col gap-2">
            <p class="text-xs font-semibold tracking-widest text-emerald-500 uppercase">Assessment Module · M3</p>
            <h1 class="text-3xl font-black text-emerald-900">Assignments</h1>
            <p class="text-emerald-900/70">All published assessments across your courses, with due dates and submission status.</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('student.assignments.calendar') }}" class="px-5 py-3 text-sm font-bold text-emerald-700 bg-white border border-emerald-100 rounded-2xl shadow-sm hover:bg-emerald-600 hover:text-white hover:border-emerald-600 hover:shadow transition">
                Assignment Calendar
            </a>
        </div>
    </div>

    <div class="space-y-4">
        @forelse ($assignments as $assignment)
            @php
                $submission = $assignment->submissions->first();
                $pastDue = $assignment->due_at && now()->greaterThan($assignment->due_at);
            @endphp
            <div class="bg-white rounded-3xl shadow-lg border border-emerald-50 px-6 py-5 flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-xl font-black text-emerald-900">{{ $assignment->title }}</h2>
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
                    @if ($pastDue && !$submission)
                        <p class="text-xs font-semibold text-rose-600 mt-1">Submission closed — past due.</p>
                    @endif
                </div>
                <div class="mt-4 sm:mt-0">
                    @php
                        $buttonLabel = 'View & Submit';
                        if ($submission) {
                            $buttonLabel = $submission->status === 'Graded' ? 'View Marks' : 'View Submission';
                        } elseif ($pastDue) {
                            $buttonLabel = 'View Details';
                        }

                        $buttonClasses = 'inline-flex items-center px-5 py-3 text-sm font-bold text-white rounded-2xl shadow-lg';
                        if ($submission && $submission->status === 'Graded') {
                            $buttonClasses .= ' bg-sky-600 shadow-sky-300/60 hover:bg-sky-700';
                        } else {
                            $buttonClasses .= ' bg-emerald-600 shadow-emerald-300/60 hover:bg-emerald-700';
                        }
                    @endphp
                    <a href="{{ route('student.assignments.show', $assignment) }}" class="{{ $buttonClasses }}">
                        {{ $buttonLabel }}
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
