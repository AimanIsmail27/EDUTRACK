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
        @if ($pastDue && !$submission)
            <p class="mt-2 text-sm font-semibold text-rose-600">Submission closed — the due date has passed.</p>
        @endif
    </div>

    <div class="bg-white rounded-3xl shadow-lg border border-emerald-50 p-6 space-y-4">
        <h2 class="text-xl font-bold text-emerald-900">Instructions</h2>
        <p class="text-sm text-slate-600 whitespace-pre-line">{{ $assignment->instructions ?? 'No additional instructions provided.' }}</p>
        @if ($assignment->attachment_url)
            <a href="{{ route('student.assignments.brief.download', $assignment) }}" class="inline-flex items-center px-4 py-2 text-sm font-semibold text-emerald-600 bg-emerald-50 rounded-2xl border border-emerald-100 hover:bg-emerald-600 hover:text-white hover:border-emerald-600 hover:shadow transition">Download Assignment Brief (PDF)</a>
        @endif
    </div>

    <div class="bg-white rounded-3xl shadow-lg border border-emerald-50 p-6 space-y-4">
        <div class="flex flex-col items-center text-center gap-2">
            <h2 class="text-xl font-bold text-emerald-900">Your Submission</h2>
            @if ($submission)
                <span class="text-xs font-bold px-3 py-1 rounded-full {{ $submission->status === 'Late' ? 'bg-rose-100 text-rose-700' : ($submission->status === 'Graded' ? 'bg-emerald-100 text-emerald-700' : 'bg-blue-100 text-blue-700') }}">
                    {{ $submission->status }}
                </span>
            @endif
        </div>

        @if ($submission)
            <div class="text-sm text-gray-600 text-center space-y-3">
                <p>Submitted {{ optional($submission->submitted_at)->format('d M Y · h:i A') }}</p>
                <a href="{{ route('student.assignments.submission.download', $assignment) }}" class="inline-flex items-center px-4 py-2 text-sm font-semibold text-indigo-600 bg-indigo-50 rounded-2xl border border-indigo-100 hover:bg-indigo-600 hover:text-white hover:border-indigo-600 hover:shadow transition">Download your file</a>
                @if ($submission->score === null)
                    <p class="text-sm font-semibold text-indigo-600">Waiting for your lecturer to grade your work.</p>
                    @if (!$pastDue)
                        <form method="POST" action="{{ route('student.assignments.submission.destroy', $assignment) }}" data-swal-confirm data-swal-title="Remove your submission?" data-swal-text="You will need to upload a replacement." data-swal-confirm-button="Remove">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="inline-flex items-center px-4 py-2 text-sm font-bold text-rose-600 bg-rose-50 border border-rose-100 rounded-2xl hover:bg-rose-100">
                                Delete Submission
                            </button>
                        </form>
                    @endif
                @endif
            </div>
        @else
            <p class="text-sm text-gray-500">No submission uploaded yet.</p>
            @if ($pastDue)
                <p class="text-sm font-semibold text-rose-600">Submission is no longer allowed because the due date has passed.</p>
            @endif
        @endif

        @if ($submission && $submission->score !== null)
            <div class="p-4 bg-emerald-50 border border-emerald-100 rounded-2xl">
                <p class="text-sm font-bold text-emerald-800">Marks: {{ $submission->score }} / {{ $assignment->total_marks }}</p>
                <p class="text-sm text-emerald-700 mt-1">{{ $submission->feedback ?? 'No feedback provided.' }}</p>
            </div>
        @endif

        @if (!$submission && !$pastDue)
            <form method="POST" action="{{ route('student.assignments.submit', $assignment) }}" enctype="multipart/form-data" class="space-y-4" data-swal-confirm data-swal-icon="question" data-swal-title="Submit your work?" data-swal-text="Make sure you uploaded the correct PDF." data-swal-confirm-button="Submit">
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
        @elseif (!$submission && $pastDue)
            <div class="p-4 bg-rose-50 border border-rose-100 rounded-2xl text-sm font-semibold text-rose-700">
                Submission window closed. Please contact your lecturer for assistance.
            </div>
        @endif
    </div>

    @if ($assignment->due_at)
        <div id="assignment-due-countdown-card" class="rounded-3xl bg-slate-900 text-white p-4 sm:p-5 border border-slate-800 shadow-lg max-w-3xl mx-auto text-center transition-all duration-500 ease-out">
            <p class="text-[10px] uppercase tracking-[0.35em] text-slate-400">Countdown</p>
            <p class="text-xl font-black mt-1">Time left to submit</p>
            <div id="assignment-due-countdown" data-due="{{ $assignment->due_at->toIso8601String() }}" class="mt-3 grid grid-cols-2 sm:grid-cols-4 gap-3 justify-items-center">
                @foreach (['days' => 'Days', 'hours' => 'Hours', 'minutes' => 'Minutes', 'seconds' => 'Seconds'] as $unit => $label)
                    <div data-segment class="bg-black/30 rounded-2xl px-3 py-4 w-32 text-center shadow-inner shadow-black/40 transition-colors duration-500">
                        <span class="block text-3xl font-bold tracking-tight countdown-segment-value" data-unit="{{ $unit }}">00</span>
                        <span class="text-[11px] font-semibold uppercase tracking-widest text-slate-400">{{ $label }}</span>
                    </div>
                @endforeach
            </div>
            <p id="assignment-due-countdown-status" class="mt-2 text-xs font-semibold"></p>
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const countdownEl = document.getElementById('assignment-due-countdown');
    if (!countdownEl) return;

    const cardEl = document.getElementById('assignment-due-countdown-card');
    const statusEl = document.getElementById('assignment-due-countdown-status');
    const dueDate = new Date(countdownEl.dataset.due);
    const valueNodes = countdownEl.querySelectorAll('.countdown-segment-value');
    const segmentNodes = countdownEl.querySelectorAll('[data-segment]');

    const render = () => {
        const now = new Date();
        const diffMs = dueDate.getTime() - now.getTime();

        if (diffMs <= 0) {
            valueNodes.forEach(node => node.textContent = '00');
            if (statusEl) {
                statusEl.textContent = 'Deadline reached — submissions closed.';
                statusEl.className = 'text-sm font-semibold text-rose-300';
            }
            if (cardEl) {
                cardEl.classList.remove('bg-slate-900', 'border-slate-800', 'shadow-lg');
                cardEl.classList.add('bg-gradient-to-br', 'from-rose-950', 'via-rose-900', 'to-rose-800', 'border-rose-700', 'shadow-rose-500/40', 'ring-2', 'ring-rose-500/50');
            }
            segmentNodes.forEach(segment => {
                segment.classList.remove('bg-black/30');
                segment.classList.add('bg-rose-900/70');
            });
            return false;
        }

        const totalSeconds = Math.floor(diffMs / 1000);
        const segments = {
            days: Math.floor(totalSeconds / 86400),
            hours: Math.floor((totalSeconds % 86400) / 3600),
            minutes: Math.floor((totalSeconds % 3600) / 60),
            seconds: totalSeconds % 60
        };

        valueNodes.forEach(node => {
            const unit = node.dataset.unit;
            const value = segments[unit];
            node.textContent = String(value).padStart(2, '0');
        });

        if (statusEl) {
            statusEl.textContent = 'Keep going — you still have time.';
            statusEl.className = 'text-sm font-semibold text-slate-300';
        }

        return true;
    };

    if (!render()) {
        return;
    }

    const interval = setInterval(() => {
        if (!render()) {
            clearInterval(interval);
        }
    }, 1000);
});
</script>
@endpush
