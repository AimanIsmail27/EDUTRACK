@extends('layout.administrator')

@section('title', 'Add New Course')

@section('content')

<div class="max-w-7xl mx-auto mt-8"> 

    {{-- Main Content Card --}}
    <div class="bg-white p-8 rounded-2xl shadow-xl shadow-gray-400/30 border border-gray-100/80">

        <div class="flex items-center justify-between mb-6 border-b pb-3 border-gray-100">
            <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">
                Add New Course
            </h1>
            <div class="text-teal-600 text-xl">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
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
        <form action="{{ route('admin.courses.store') }}" method="POST" class="space-y-6">
            @csrf

            {{-- Section 1: Course Identification --}}
            <div class="space-y-4">
                <h2 class="text-lg font-bold text-gray-800 border-b pb-1 border-gray-100">Course Identification</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="relative">
                        <label for="C_Code" class="block text-xs font-semibold uppercase text-gray-600 mb-1">Course Code:</label>
                        <input type="text" name="C_Code" id="C_Code" value="{{ old('C_Code') }}" placeholder="e.g., BCN1010" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50/70 text-gray-900 placeholder-gray-400 focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition shadow-inner text-sm">
                    </div>

                    <div class="relative">
                        <label for="C_Name" class="block text-xs font-semibold uppercase text-gray-600 mb-1">Course Name:</label>
                        <input type="text" name="C_Name" id="C_Name" value="{{ old('C_Name') }}" placeholder="e.g., Data Mining Fundamentals" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50/70 text-gray-900 placeholder-gray-400 focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition shadow-inner text-sm">
                    </div>
                </div>
            </div>

            {{-- Section 2: Teaching Assignment (NEW) --}}
            <div class="space-y-4 pt-2">
                <h2 class="text-lg font-bold text-gray-800 border-b pb-1 border-gray-100">Teaching Assignment</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    
                    {{-- 1 Coordinator (Pulls from User list with role lecturer) --}}
                    <div class="relative">
                        <label for="coordinator_id" class="block text-xs font-semibold uppercase text-gray-600 mb-1">Course Coordinator (Primary):</label>
                        <select name="coordinator_id" id="coordinator_id" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-white text-gray-900 focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition shadow-inner text-sm">
                            <option value="">-- Select Main Coordinator --</option>
                            @foreach($lecturers as $lecturer)
                                <option value="{{ $lecturer->id }}" {{ old('coordinator_id') == $lecturer->id ? 'selected' : '' }}>
                                    {{ $lecturer->name }} ({{ $lecturer->email }})
                                </option>
                            @endforeach
                        </select>
                        <p class="text-[10px] text-gray-400 mt-1 italic">The primary lecturer in charge of this course.</p>
                    </div>

                    {{-- Multiple Lecturers Involved (Teaching Team) --}}
                    <div class="relative">
                        <label for="lecturer_ids" class="block text-xs font-semibold uppercase text-gray-600 mb-1">Teaching Team / Involved Lecturers:</label>
                        <select name="lecturer_ids[]" id="lecturer_ids" multiple
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-white text-gray-900 focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition shadow-inner text-sm min-h-[42px]">
                            @foreach($lecturers as $lecturer)
                                <option value="{{ $lecturer->id }}" {{ (is_array(old('lecturer_ids')) && in_array($lecturer->id, old('lecturer_ids'))) ? 'selected' : '' }}>
                                    {{ $lecturer->name }}
                                </option>
                            @endforeach
                        </select>
                        <p class="text-[10px] text-gray-400 mt-1 italic">Hold Ctrl (Cmd) to select multiple lecturers.</p>
                    </div>

                </div>
            </div>
            
            {{-- Section 3: Logistics --}}
            <div class="space-y-4 pt-2">
                <h2 class="text-lg font-bold text-gray-800 border-b pb-1 border-gray-100">Logistics and Requirements</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="relative">
                        <label for="C_Hour" class="block text-xs font-semibold uppercase text-gray-600 mb-1">Credit Hour:</label>
                        <input type="number" name="C_Hour" id="C_Hour" value="{{ old('C_Hour') }}" placeholder="3" required min="1" max="8"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50/70 text-gray-900 text-sm">
                    </div>

                    <div class="relative">
                        <label class="block text-xs font-semibold uppercase text-gray-600 mb-1">Semester Offered:</label>
                        <div class="space-y-2 p-3 border border-gray-300 rounded-lg bg-gray-50/70 shadow-inner">
                            @php $oldSemesters = old('C_SemOffered', []); @endphp
                            <div class="flex items-center">
                                <input type="checkbox" name="C_SemOffered[]" id="sem1" value="1" {{ in_array('1', $oldSemesters) ? 'checked' : '' }} class="h-4 w-4 text-teal-600 border-gray-300 rounded focus:ring-teal-500">
                                <label for="sem1" class="ml-2 text-sm text-gray-700">Semester 1</label>
                            </div>
                            <div class="flex items-center">
                                <input type="checkbox" name="C_SemOffered[]" id="sem2" value="2" {{ in_array('2', $oldSemesters) ? 'checked' : '' }} class="h-4 w-4 text-teal-600 border-gray-300 rounded focus:ring-teal-500">
                                <label for="sem2" class="ml-2 text-sm text-gray-700">Semester 2</label>
                            </div>
                            <div class="flex items-center">
                                <input type="checkbox" name="C_SemOffered[]" id="sem3" value="3" {{ in_array('3', $oldSemesters) ? 'checked' : '' }} class="h-4 w-4 text-teal-600 border-gray-300 rounded focus:ring-teal-500">
                                <label for="sem3" class="ml-2 text-sm text-gray-700">Semester 3 (Short/Special)</label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="relative">
                        <label for="C_Prerequisites" class="block text-xs font-semibold uppercase text-gray-600 mb-1">Prerequisites (Optional):</label>
                        <input type="text" name="C_Prerequisites" id="C_Prerequisites" value="{{ old('C_Prerequisites') }}" placeholder="e.g., BCN1001, BCN1002"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50/70 text-gray-900 text-sm">
                    </div>
                </div>
            </div>

            <div class="space-y-4 pt-2">
                <h2 class="text-lg font-bold text-gray-800 border-b pb-1 border-gray-100">Course Description</h2>
                <div>
                    <label for="C_Description" class="block text-xs font-semibold uppercase text-gray-600 mb-1">Detailed Description (Optional):</label>
                    <textarea name="C_Description" id="C_Description" rows="4" placeholder="Course summary..."
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50/70 text-gray-900 text-sm">{{ old('C_Description') }}</textarea>
                </div>
            </div>

            <div class="pt-4 flex justify-end space-x-4">
                <button type="submit" class="px-6 py-2.5 bg-teal-600 text-white rounded-lg font-bold hover:bg-teal-700 transition duration-300 shadow-lg shadow-teal-600/40 transform hover:scale-[1.02] text-sm">
                    COMPLETE & SAVE
                </button>
                <a href="{{ route('admin.viewAllCourse') }}" class="px-6 py-2.5 bg-red-600 text-white rounded-lg font-bold hover:bg-red-700 transition duration-300 shadow-lg shadow-red-600/30 inline-flex items-center text-sm">
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
            Swal.fire({ icon: 'success', title: 'Success!', text: "{{ session('success') }}", confirmButtonText: 'OK', confirmButtonColor: '#10B981' });
        @endif

        @if (session('error'))
            Swal.fire({ icon: 'error', title: 'Oops...', text: "{{ session('error') }}", confirmButtonText: 'OK', confirmButtonColor: '#10B981' });
        @endif
        
        @if ($errors->any())
            Swal.fire({
                icon: 'warning',
                title: 'Attempt Failed!',
                html: '<p class="text-sm text-gray-600 mb-3">Please correct the errors in the form.</p>',
                confirmButtonText: 'Got it',
                confirmButtonColor: '#10B981'
            });
        @endif
    });
</script>
@endpush