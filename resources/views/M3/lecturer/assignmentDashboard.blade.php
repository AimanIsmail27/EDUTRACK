@extends('layout.lecturer')

@section('title', 'Assessment List')

@section('content')
<div class="max-w-6xl mx-auto space-y-8">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <p class="text-xs font-semibold tracking-widest text-indigo-500 uppercase">Assessment Module · M3</p>
            <h1 class="text-3xl font-black text-indigo-900">Assessment List</h1>
            <p class="text-indigo-900/60">Monitor upcoming assessments and publish new ones for your cohort.</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('lecturer.assignments.calendar') }}" class="px-5 py-3 text-sm font-bold text-indigo-600 bg-white border border-indigo-100 rounded-2xl shadow-sm hover:shadow transition">
                Assessment Calendar
            </a>
            <a href="{{ route('lecturer.assignments.create') }}" class="px-5 py-3 text-sm font-bold text-white bg-indigo-600 rounded-2xl shadow-lg shadow-indigo-400/40 hover:bg-indigo-700 transition">
                Create New Assessment
            </a>
        </div>
    </div>

    @php
        $totalCount = $assignments->count();
        $upcomingCount = $assignments->filter(fn($item) => $item->due_at && now()->diffInDays($item->due_at, false) <= 14 && now()->diffInDays($item->due_at, false) >= 0)->count();
    @endphp

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
        <div class="strong-card p-5 rounded-3xl">
            <p class="text-xs font-semibold tracking-widest text-indigo-400 uppercase">Assessments</p>
            <p class="mt-1 text-3xl font-black text-indigo-900">{{ $totalCount }}</p>
        </div>
        <div class="strong-card p-5 rounded-3xl">
            <p class="text-xs font-semibold tracking-widest text-indigo-400 uppercase">Upcoming Due</p>
            <p class="mt-1 text-3xl font-black text-indigo-900">{{ $upcomingCount }}</p>
        </div>
    </div>

    <div class="strong-card rounded-3xl overflow-hidden">
        <div class="flex items-center justify-between px-6 py-4 border-b border-indigo-50">
            <div>
                <h2 class="text-xl font-black text-indigo-900">Assessment Overview</h2>
                <p class="text-sm text-gray-500">Quick snapshot of everything that is assigned to your students.</p>
            </div>
            <div class="flex items-center gap-2 text-sm">
                <span class="inline-flex items-center px-3 py-1 text-xs font-semibold text-indigo-600 bg-indigo-50 rounded-full">Sorted by date</span>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left min-w-[600px]">
                <thead>
                    <tr class="text-xs font-semibold tracking-widest text-gray-500 uppercase bg-indigo-50/60">
                        <th class="px-6 py-3">Title</th>
                        <th class="px-6 py-3">Due Date</th>
                        <th class="px-6 py-3">Total Marks</th>
                        <th class="px-6 py-3">Submissions</th>
                        <th class="px-6 py-3">Brief</th>
                        <th class="px-6 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($assignments as $assignment)
                        <tr class="border-b border-indigo-50/80 hover:bg-indigo-50/40 transition">
                            <td class="px-6 py-4">
                                <div class="font-semibold text-indigo-900">{{ $assignment->title }}</div>
                                <p class="text-xs text-gray-500">
                                    Course: {{ optional($assignment->course)->C_Name ? $assignment->course->C_Name .' ('. $assignment->course->C_Code .')' : '—' }}
                                </p>
                            </td>
                            <td class="px-6 py-4 text-sm font-semibold text-gray-700">
                                {{ $assignment->due_at ? $assignment->due_at->format('d M Y · h:i A') : 'No due date' }}
                            </td>
                            <td class="px-6 py-4 text-sm font-semibold text-gray-700">
                                {{ $assignment->total_marks }}
                            </td>
                            <td class="px-6 py-4 text-sm font-semibold text-gray-700">
                                {{ $assignment->submissions_count ?? 0 }}
                            </td>
                            <td class="px-6 py-4">
                                @if ($assignment->attachment_url)
                                    <a href="{{ route('lecturer.assignments.brief.download', $assignment) }}" class="inline-flex items-center justify-center px-4 py-2 text-xs font-bold text-sky-700 bg-sky-50 rounded-full border border-sky-100 hover:bg-white whitespace-nowrap">
                                        View PDF
                                    </a>
                                @else
                                    <span class="text-xs text-gray-400">—</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-nowrap items-center justify-end gap-2">
                                    <a href="{{ route('lecturer.assignments.edit', $assignment) }}" class="inline-flex items-center justify-center px-4 py-2 text-xs font-bold text-violet-600 bg-violet-50 rounded-2xl border border-violet-100 hover:bg-white whitespace-nowrap">Edit</a>
                                    <a href="{{ route('lecturer.assignments.submissions', $assignment) }}" class="inline-flex items-center justify-center px-4 py-2 text-xs font-bold text-sky-600 bg-sky-50 rounded-2xl border border-sky-100 hover:bg-white whitespace-nowrap">Submissions</a>
                                    <form action="{{ route('lecturer.assignments.destroy', $assignment) }}" method="POST" class="inline-flex" data-swal-confirm data-swal-title="Delete this assessment?" data-swal-text="This action cannot be undone." data-swal-confirm-button="Delete">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inline-flex items-center justify-center px-4 py-2 text-xs font-bold text-rose-600 bg-rose-50 rounded-2xl border border-rose-100 hover:bg-white whitespace-nowrap">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-6 text-center text-sm text-gray-500">No assessments yet. Click "Create New Assessment" to add one.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
