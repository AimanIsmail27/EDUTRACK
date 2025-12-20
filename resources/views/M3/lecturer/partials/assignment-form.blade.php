@csrf

<div class="space-y-6">
    <div>
        <label class="block text-sm font-semibold text-indigo-900">Title <span class="text-rose-500">*</span></label>
        <input type="text" name="title" value="{{ old('title', $assignment->title ?? '') }}" class="mt-2 w-full rounded-2xl border border-indigo-100 px-4 py-3 focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100" required>
        @error('title')
            <p class="mt-1 text-sm text-rose-500">{{ $message }}</p>
        @enderror
    </div>

    <div class="grid gap-6 md:grid-cols-2">
        <div>
            <label class="block text-sm font-semibold text-indigo-900">Course <span class="text-rose-500">*</span></label>
            <select name="course_code" class="mt-2 w-full rounded-2xl border border-indigo-100 px-4 py-3 bg-white focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100" required>
                <option value="">Select course</option>
                @foreach ($courses as $course)
                    <option value="{{ $course->C_Code }}" @selected(old('course_code', $assignment->course_code ?? '') === $course->C_Code)>
                        {{ $course->C_Name }} ({{ $course->C_Code }})
                    </option>
                @endforeach
            </select>
            @error('course_code')
                <p class="mt-1 text-sm text-rose-500">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label class="block text-sm font-semibold text-indigo-900">Due Date</label>
            <input type="datetime-local" name="due_at" value="{{ old('due_at', optional($assignment->due_at ?? null)->format('Y-m-d\TH:i')) }}" class="mt-2 w-full rounded-2xl border border-indigo-100 px-4 py-3 focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100">
            @error('due_at')
                <p class="mt-1 text-sm text-rose-500">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <div class="grid gap-6 md:grid-cols-2">
        <div>
            <label class="block text-sm font-semibold text-indigo-900">Total Marks</label>
            <input type="number" name="total_marks" min="1" max="1000" value="{{ old('total_marks', $assignment->total_marks ?? 100) }}" class="mt-2 w-full rounded-2xl border border-indigo-100 px-4 py-3 focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100">
            @error('total_marks')
                <p class="mt-1 text-sm text-rose-500">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label class="block text-sm font-semibold text-indigo-900">Status</label>
            @php($statusOptions = $statuses ?? \App\Models\Assignment::editableStatuses())
            <select name="status" class="mt-2 w-full rounded-2xl border border-indigo-100 px-4 py-3 bg-white focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100">
                @foreach ($statusOptions as $status)
                    <option value="{{ $status }}" @selected(old('status', $assignment->status ?? 'Draft') === $status)>{{ $status }}</option>
                @endforeach
            </select>
            @error('status')
                <p class="mt-1 text-sm text-rose-500">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <div>
        <label class="block text-sm font-semibold text-indigo-900">Instructions</label>
        <textarea name="instructions" rows="6" class="mt-2 w-full rounded-2xl border border-indigo-100 px-4 py-3 focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100">{{ old('instructions', $assignment->instructions ?? '') }}</textarea>
        @error('instructions')
            <p class="mt-1 text-sm text-rose-500">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="block text-sm font-semibold text-indigo-900">Assignment Brief (PDF)</label>
        <input type="file" name="attachment" accept="application/pdf" class="mt-2 w-full rounded-2xl border border-dashed border-indigo-200 px-4 py-5 text-sm text-indigo-700 bg-indigo-50/40 cursor-pointer">
        @if (!empty($assignment->attachment_url))
            <p class="mt-2 text-sm">
                Current file:
                <a href="{{ $assignment->attachment_url }}" target="_blank" class="font-semibold text-indigo-600 underline">Download PDF</a>
            </p>
        @endif
        <p class="mt-1 text-xs text-gray-500">Optional. PDF up to 5&nbsp;MB.</p>
        @error('attachment')
            <p class="mt-1 text-sm text-rose-500">{{ $message }}</p>
        @enderror
    </div>
</div>

<div class="flex flex-col-reverse gap-3 pt-8 sm:flex-row sm:items-center sm:justify-between">
    <a href="{{ route('lecturer.assignments.index') }}" class="inline-flex items-center justify-center px-5 py-3 text-sm font-semibold text-indigo-600 bg-white border border-indigo-100 rounded-2xl hover:bg-indigo-50">Cancel</a>
    <button type="submit" class="inline-flex items-center justify-center px-6 py-3 text-sm font-bold text-white bg-indigo-600 rounded-2xl shadow-lg shadow-indigo-400/40 hover:bg-indigo-700">
        {{ $submitLabel }}
    </button>
</div>
