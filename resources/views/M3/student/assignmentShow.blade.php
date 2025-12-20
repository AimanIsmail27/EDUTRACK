@extends('layout.student')

@section('title', $assignment->title)

@section('content')
<div class="max-w-4xl mx-auto space-y-8">
    <div>
        <p class="text-xs font-semibold tracking-widest text-emerald-500 uppercase">Assessment Module · M3</p>
        <h1 class="text-3xl font-black text-emerald-900">{{ $assignment->title }}</h1>
        <p class="text-emerald-900/70">
            {{ optional($assignment->course)->C_Name ? $assignment->course->C_Name . ' • ' . $assignment->course->C_Code : 'Course TBD' }}
            · Total {{ $assignment->total_marks }} marks
        </p>
        <p class="text-sm font-semibold text-emerald-800 mt-2">
            Due: {{ $assignment->due_at ? $assignment->due_at->format('d M Y · h:i A') : 'No due date' }}
        </p>
    </div>

    @if (session('success'))
        <div class="p-4 text-sm font-semibold text-emerald-700 bg-emerald-100 border border-emerald-200 rounded-2xl">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white rounded-3xl shadow-lg border border-emerald-50 p-6 space-y-4">
        <h2 class="text-xl font-bold text-emerald-900">Instructions</h2>
        <p class="text-sm text-slate-600 whitespace-pre-line">{{ $assignment->instructions ?? 'No additional instructions provided.' }}</p>
        @if ($assignment->attachment_url)
            <a href="{{ $assignment->attachment_url }}" target="_blank" class="inline-flex items-center px-4 py-2 text-sm font-semibold text-emerald-600 bg-emerald-50 rounded-2xl border border-emerald-100">Download Assignment Brief (PDF)</a>
        @endif
    </div>

    <div class="bg-white rounded-3xl shadow-lg border border-emerald-50 p-6 space-y-4">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-bold text-emerald-900">Your Submission</h2>
            @if ($submission)
                <span class="text-xs font-bold px-3 py-1 rounded-full {{ $submission->status === 'Late' ? 'bg-rose-100 text-rose-700' : ($submission->status === 'Graded' ? 'bg-emerald-100 text-emerald-700' : 'bg-blue-100 text-blue-700') }}">
                    {{ $submission->status }}
                </span>
            @endif
        </div>

        @if ($submission)
            <div class="text-sm text-gray-600">
                <p>Submitted {{ optional($submission->submitted_at)->format('d M Y · h:i A') }}</p>
                <a href="{{ $submission->file_url }}" target="_blank" class="inline-flex items-center px-4 py-2 mt-2 text-sm font-semibold text-indigo-600 bg-indigo-50 rounded-2xl border border-indigo-100">Download your file</a>
            </div>
        @else
            <p class="text-sm text-gray-500">No submission uploaded yet.</p>
        @endif

        @if ($submission && $submission->score !== null)
            <div class="p-4 bg-emerald-50 border border-emerald-100 rounded-2xl">
                <p class="text-sm font-bold text-emerald-800">Marks: {{ $submission->score }} / {{ $assignment->total_marks }}</p>
                <p class="text-sm text-emerald-700 mt-1">{{ $submission->feedback ?? 'No feedback provided.' }}</p>
            </div>
        @endif

        <form method="POST" action="{{ route('student.assignments.submit', $assignment) }}" enctype="multipart/form-data" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-semibold text-emerald-900">Upload PDF</label>
                <input type="file" name="attachment" accept="application/pdf" class="mt-2 w-full rounded-2xl border border-dashed border-emerald-200 px-4 py-5 text-sm text-emerald-700 bg-emerald-50/40 cursor-pointer">
                <p class="text-xs text-gray-500 mt-1">Max 10MB. Uploading after the due date will be marked as late.</p>
                @error('attachment')
                    <p class="mt-1 text-sm text-rose-500">{{ $message }}</p>
                @enderror
            </div>
            <button type="submit" class="inline-flex items-center justify-center px-6 py-3 text-sm font-bold text-white bg-emerald-600 rounded-2xl shadow-lg hover:bg-emerald-700">
                Submit Work
            </button>
        </form>
    </div>
</div>
@endsection
