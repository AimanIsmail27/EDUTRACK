@extends('layout.administrator')

@section('title', 'Edit Course: ' . $course->C_Code)

@section('content')

<div class="max-w-7xl mx-auto mt-8"> 

    {{-- Main Content Card --}}
    <div class="bg-white p-8 rounded-2xl shadow-xl shadow-gray-400/30 border border-gray-100/80">

        <div class="flex items-center justify-between mb-6 border-b pb-3 border-gray-100">
            <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">
                Edit Course: {{ $course->C_Code }}
            </h1>
            <div class="text-teal-600 text-xl">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
            </div>
        </div>

        {{-- Display Validation Errors --}}
        @if ($errors->any())
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-3 mb-5 rounded-xl text-sm" role="alert">
                <p class="font-bold">Reason for Error:</p>
                <ul class="list-disc ml-5 mt-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- FORM START --}}
        <form action="{{ route('admin.courses.update', $course->C_Code) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="space-y-4">
                <h2 class="text-lg font-bold text-gray-800 border-b pb-1 border-gray-100">Course Identification</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Course Code (Read-Only) --}}
                    <div class="relative">
                        <label for="C_Code" class="block text-xs font-semibold uppercase text-gray-600 mb-1">Course Code:</label>
                        <input type="text" id="C_Code" value="{{ $course->C_Code }}" readonly
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-200/70 text-gray-700 shadow-inner text-sm cursor-not-allowed">
                        <p class="text-[10px] text-gray-500 mt-1 italic">Course code is used as a primary key and cannot be edited.</p>
                    </div>

                    {{-- Course Name --}}
                    <div class="relative">
                        <label for="C_Name" class="block text-xs font-semibold uppercase text-gray-600 mb-1">Course Name:</label>
                        <input type="text" name="C_Name" id="C_Name" value="{{ old('C_Name', $course->C_Name) }}" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50/70 text-gray-900 focus:ring-2 focus:ring-teal-500 transition text-sm">
                    </div>
                </div>
            </div>

            {{-- Section: Teaching Assignment (UPDATED) --}}
            <div class="space-y-4 pt-2">
                <h2 class="text-lg font-bold text-gray-800 border-b pb-1 border-gray-100">Teaching Assignment</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    
                    {{-- Edit Coordinator --}}
                    <div class="relative">
                        <label for="coordinator_id" class="block text-xs font-semibold uppercase text-gray-600 mb-1">Course Coordinator:</label>
                        <select name="coordinator_id" id="coordinator_id" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-white text-gray-900 focus:ring-2 focus:ring-teal-500 transition text-sm">
                            <option value="">-- Select Coordinator --</option>
                            @foreach($lecturers as $lecturer)
                                <option value="{{ $lecturer->id }}" {{ old('coordinator_id', $course->coordinator_id) == $lecturer->id ? 'selected' : '' }}>
                                    {{ $lecturer->name }} ({{ $lecturer->email }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Edit Involved Lecturers (Multi-select) --}}
                    <div class="relative">
                        <label for="lecturer_ids" class="block text-xs font-semibold uppercase text-gray-600 mb-1">Teaching Team / Involved Lecturers:</label>
                        <select name="lecturer_ids[]" id="lecturer_ids" multiple
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-white text-gray-900 focus:ring-2 focus:ring-teal-500 transition text-sm min-h-[42px]">
                            @php
                                // Get currently assigned lecturers as an array of IDs
                                $assignedLecturers = old('lecturer_ids', $course->lecturers->pluck('id')->toArray());
                            @endphp
                            @foreach($lecturers as $lecturer)
                                <option value="{{ $lecturer->id }}" {{ in_array($lecturer->id, $assignedLecturers) ? 'selected' : '' }}>
                                    {{ $lecturer->name }}
                                </option>
                            @endforeach
                        </select>
                        <p class="text-[10px] text-gray-400 mt-1 italic">Hold Ctrl (Cmd) to modify selection.</p>
                    </div>

                </div>
            </div>
            
            <div class="space-y-4 pt-2">
                <h2 class="text-lg font-bold text-gray-800 border-b pb-1 border-gray-100">Logistics and Requirements</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="relative">
                        <label for="C_Hour" class="block text-xs font-semibold uppercase text-gray-600 mb-1">Credit Hour:</label>
                        <input type="number" name="C_Hour" id="C_Hour" value="{{ old('C_Hour', $course->C_Hour) }}" required min="1" max="8"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50/70 text-sm">
                    </div>

                    <div class="relative">
                        <label class="block text-xs font-semibold uppercase text-gray-600 mb-1">Semester Offered:</label>
                        <div class="space-y-2 p-3 border border-gray-300 rounded-lg bg-gray-50/70 shadow-inner">
                            @php 
                                // Handle CSV string from DB or array from old input
                                $currentSemesters = old('C_SemOffered', explode(',', $course->C_SemOffered)); 
                            @endphp
                            @for ($i = 1; $i <= 3; $i++)
                                <div class="flex items-center">
                                    <input type="checkbox" name="C_SemOffered[]" id="sem{{ $i }}" value="{{ $i }}" 
                                           {{ in_array($i, $currentSemesters) ? 'checked' : '' }}
                                           class="h-4 w-4 text-teal-600 border-gray-300 rounded focus:ring-teal-500">
                                    <label for="sem{{ $i }}" class="ml-2 text-sm text-gray-700">Semester {{ $i }}{{ $i == 3 ? ' (Short)' : '' }}</label>
                                </div>
                            @endfor
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="relative">
                        <label for="C_Prerequisites" class="block text-xs font-semibold uppercase text-gray-600 mb-1">Prerequisites (Optional):</label>
                        <input type="text" name="C_Prerequisites" id="C_Prerequisites" value="{{ old('C_Prerequisites', $course->C_Prerequisites) }}" placeholder="e.g., BCN1001"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50/70 text-sm">
                    </div>
                </div>
            </div>

            <div class="space-y-4 pt-2">
                <h2 class="text-lg font-bold text-gray-800 border-b pb-1 border-gray-100">Course Description</h2>
                <div>
                    <label for="C_Description" class="block text-xs font-semibold uppercase text-gray-600 mb-1">Detailed Description (Optional):</label>
                    <textarea name="C_Description" id="C_Description" rows="4" 
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50/70 text-sm">{{ old('C_Description', $course->C_Description) }}</textarea>
                </div>
            </div>

            <div class="pt-4 flex justify-end space-x-4">
                <button type="submit" class="px-6 py-2.5 bg-teal-600 text-white rounded-lg font-bold hover:bg-teal-700 transition duration-300 shadow-lg shadow-teal-600/40 transform hover:scale-[1.02] text-sm">
                    UPDATE COURSE
                </button>
                <a href="{{ route('admin.viewAllCourse') }}" class="px-6 py-2.5 bg-red-600 text-white rounded-lg font-bold hover:bg-red-700 transition duration-300 shadow-lg text-sm">
                    CANCEL
                </a>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        @if (session('success'))
            Swal.fire({ icon: 'success', title: 'Success!', text: "{{ session('success') }}", confirmButtonColor: '#10B981' });
        @endif
        @if ($errors->any())
            Swal.fire({
                icon: 'warning',
                title: 'Update Failed!',
                html: '<p class="text-sm">Please check the highlighted errors.</p>',
                confirmButtonColor: '#10B981'
            });
        @endif
    });
</script>
@endpush