@extends('layout.lecturer')

@section('title', 'Submissions')

@section('content')
<div class="max-w-6xl mx-auto space-y-8">
    <div>
        <p class="text-xs font-semibold tracking-widest text-indigo-500 uppercase">Assessment Module · M3</p>
        <h1 class="text-3xl font-black text-indigo-900">{{ $assignment->title }} · Submissions</h1>
        <p class="text-indigo-900/60">
            {{ optional($assignment->course)->C_Name ? $assignment->course->C_Name . ' • ' . $assignment->course->C_Code : 'Course TBD' }} ·
            Due {{ $assignment->due_at ? $assignment->due_at->format('d M Y · h:i A') : 'No due date' }}
        </p>
    </div>

    <div class="strong-card rounded-3xl overflow-hidden">
        <div class="flex items-center justify-between px-6 py-4 border-b border-indigo-50">
            <div>
                <h2 class="text-xl font-black text-indigo-900">Student Submissions</h2>
                <p class="text-sm text-gray-500">{{ $submissions->count() }} received</p>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full min-w-[700px] text-left">
                <thead>
                    <tr class="text-xs font-semibold tracking-widest text-gray-500 uppercase bg-indigo-50/60">
                        <th class="px-6 py-3">Student</th>
                        <th class="px-6 py-3">Submitted At</th>
                        <th class="px-6 py-3">Status</th>
                        <th class="px-6 py-3">File</th>
                        <th class="px-6 py-3">Marks</th>
                        <th class="px-6 py-3">Feedback</th>
                        <th class="px-6 py-3"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($submissions as $submission)
                        <tr class="border-b border-indigo-50/80">
                            <td class="px-6 py-4">
                                <div class="font-semibold text-indigo-900">{{ $submission->student->name ?? 'Student' }}</div>
                                <p class="text-xs text-gray-500">{{ $submission->student->email }}</p>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                {{ optional($submission->submitted_at)->format('d M Y · h:i A') }}
                            </td>
                            <td class="px-6 py-4">
                                @php
                                    $statusColor = [
                                        'Submitted' => 'bg-amber-100 text-amber-700',
                                        'Late' => 'bg-rose-100 text-rose-700',
                                        'Graded' => 'bg-emerald-100 text-emerald-700',
                                    ][$submission->status] ?? 'bg-slate-100 text-slate-600';
                                @endphp
                                <span class="px-3 py-1 text-xs font-bold rounded-full {{ $statusColor }}">
                                    {{ $submission->status }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <a href="{{ route('lecturer.assignments.submissions.download', [$assignment, $submission]) }}" class="inline-flex items-center px-3 py-1 text-xs font-bold text-indigo-600 bg-indigo-50 rounded-full border border-indigo-100 hover:bg-white">Download</a>
                            </td>
                            <td class="px-6 py-4 text-sm font-semibold text-gray-700">
                                {{ $submission->score ? $submission->score . ' / ' . $assignment->total_marks : '—' }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                {{ $submission->feedback ?? '—' }}
                            </td>
                            <td class="px-6 py-4" x-data="{ editing: {{ $submission->score !== null ? 'false' : 'true' }} }">
                                <div x-show="!editing" x-cloak class="flex flex-col items-end gap-2">
                                    <button type="button" @click="editing = true" class="inline-flex items-center px-4 py-2 text-xs font-bold text-indigo-600 bg-indigo-50 rounded-2xl border border-indigo-100 hover:bg-white">
                                        Edit Grade
                                    </button>
                                </div>

                                <form x-show="editing" x-cloak method="POST" action="{{ route('lecturer.assignments.submissions.grade', [$assignment, $submission]) }}" class="space-y-2">
                                    @csrf
                                    <input type="number" name="score" min="0" max="{{ $assignment->total_marks }}" value="{{ old('score', $submission->score) }}" class="w-full rounded-2xl border border-indigo-100 px-3 py-2 text-sm" placeholder="Marks" required>
                                    <textarea name="feedback" rows="2" class="w-full rounded-2xl border border-indigo-100 px-3 py-2 text-sm" placeholder="Feedback (optional)">{{ old('feedback', $submission->feedback) }}</textarea>
                                    <div class="flex items-center gap-2">
                                        <button type="submit" class="flex-1 px-3 py-2 text-xs font-bold text-white bg-indigo-600 rounded-2xl">Save</button>
                                        @if ($submission->score !== null)
                                            <button type="button" @click="editing = false" class="px-3 py-2 text-xs font-bold text-gray-600 bg-gray-100 rounded-2xl border border-gray-200">Cancel</button>
                                        @endif
                                    </div>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-6 text-center text-sm text-gray-500">No submissions yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
