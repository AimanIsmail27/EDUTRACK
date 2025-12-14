@extends('layout.administrator')

@section('title', 'Edit Course: ' . $course->C_Code)

@section('content')

{{-- 
    Centering Container with Top Margin (Same as Add Page)
    We keep the mt-8 here for reliable vertical gap from the header.
--}}
<div class="max-w-7xl mx-auto mt-8"> 

    {{-- Main Content Card: The form container itself --}}
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

        {{-- Display Validation Errors (Same as Add Page) --}}
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

        {{-- FORM START: Target the update route --}}
        <form action="{{ route('admin.courses.update', $course->C_Code) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="space-y-4">
                <h2 class="text-lg font-bold text-gray-800 border-b pb-1 border-gray-100">Course Identification</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    
                    {{-- Course Code (C_Code) - Display Only, Cannot be changed --}}
                    <div class="relative">
                        <label for="C_Code" class="block text-xs font-semibold uppercase text-gray-600 mb-1">Course Code:</label>
                        <input type="text" 
                               id="C_Code" 
                               value="{{ $course->C_Code }}"
                               readonly
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-200/70 text-gray-700 placeholder-gray-400 shadow-inner text-sm cursor-not-allowed">
                        <p class="text-xs text-gray-500 mt-1">Course code cannot be edited.</p>
                    </div>

                    {{-- Course Name (C_Name) - Populated with existing data --}}
                    <div class="relative">
                        <label for="C_Name" class="block text-xs font-semibold uppercase text-gray-600 mb-1">Course Name:</label>
                        <input type="text" 
                               name="C_Name" 
                               id="C_Name" 
                               value="{{ old('C_Name', $course->C_Name) }}"
                               placeholder="e.g., Data Mining Fundamentals"
                               required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50/70 text-gray-900 placeholder-gray-400 focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition shadow-inner text-sm @error('C_Name') border-red-500 ring-red-200 @enderror">
                        @error('C_Name')
                            <p class="text-red-500 text-xs mt-1 absolute">Required and must be unique.</p>
                        @enderror
                    </div>

                </div>
            </div>
            
            <div class="space-y-4 pt-2">
                <h2 class="text-lg font-bold text-gray-800 border-b pb-1 border-gray-100">Logistics and Requirements</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                    {{-- Credit Hour (C_Hour) - Populated with existing data --}}
                    <div class="relative">
                        <label for="C_Hour" class="block text-xs font-semibold uppercase text-gray-600 mb-1">Credit Hour:</label>
                        <input type="number" 
                               name="C_Hour" 
                               id="C_Hour" 
                               value="{{ old('C_Hour', $course->C_Hour) }}"
                               placeholder="3 or 4"
                               required
                               min="1"
                               max="8"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50/70 text-gray-900 placeholder-gray-400 focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition shadow-inner text-sm @error('C_Hour') border-red-500 ring-red-200 @enderror">
                        @error('C_Hour')
                            <p class="text-red-500 text-xs mt-1 absolute">Required (1-8 hours).</p>
                        @enderror
                    </div>

                    {{-- Semester Offered (C_SemOffered) - Checkboxes populated from CSV string --}}
                    <div class="relative">
                        <label class="block text-xs font-semibold uppercase text-gray-600 mb-1">Semester Offered:</label>
                        
                        <div class="space-y-2 p-3 border border-gray-300 rounded-lg bg-gray-50/70 shadow-inner">

                            @php
                                // Get old values (if validation failed) OR split the stored CSV string
                                $currentSemesters = old('C_SemOffered', explode(',', $course->C_SemOffered));
                            @endphp

                            {{-- Checkbox 1 --}}
                            <div class="flex items-center">
                                <input type="checkbox" 
                                       name="C_SemOffered[]" 
                                       id="sem1" 
                                       value="1" 
                                       {{ in_array('1', $currentSemesters) ? 'checked' : '' }}
                                       class="h-4 w-4 text-teal-600 border-gray-300 rounded focus:ring-teal-500">
                                <label for="sem1" class="ml-2 text-sm text-gray-700">Semester 1</label>
                            </div>

                            {{-- Checkbox 2 --}}
                            <div class="flex items-center">
                                <input type="checkbox" 
                                       name="C_SemOffered[]" 
                                       id="sem2" 
                                       value="2" 
                                       {{ in_array('2', $currentSemesters) ? 'checked' : '' }}
                                       class="h-4 w-4 text-teal-600 border-gray-300 rounded focus:ring-teal-500">
                                <label for="sem2" class="ml-2 text-sm text-gray-700">Semester 2</label>
                            </div>

                            {{-- Checkbox 3 --}}
                            <div class="flex items-center">
                                <input type="checkbox" 
                                       name="C_SemOffered[]" 
                                       id="sem3" 
                                       value="3" 
                                       {{ in_array('3', $currentSemesters) ? 'checked' : '' }}
                                       class="h-4 w-4 text-teal-600 border-gray-300 rounded focus:ring-teal-500">
                                <label for="sem3" class="ml-2 text-sm text-gray-700">Semester 3 (Short/Special)</label>
                            </div>
                            
                        </div>
                        
                        @error('C_SemOffered')
                            <p class="text-red-500 text-xs mt-1">Please select at least one semester.</p>
                        @enderror
                    </div>

                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    
                    {{-- Prerequisites (C_Prerequisites) - Populated with existing data --}}
                    <div class="relative">
                        <label for="C_Prerequisites" class="block text-xs font-semibold uppercase text-gray-600 mb-1">Prerequisites (Optional):</label>
                        <input type="text" 
                               name="C_Prerequisites" 
                               id="C_Prerequisites" 
                               value="{{ old('C_Prerequisites', $course->C_Prerequisites) }}"
                               placeholder="Comma-separated codes (e.g., BCN1001, BCN1002)"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50/70 text-gray-900 placeholder-gray-400 focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition shadow-inner text-sm @error('C_Prerequisites') border-red-500 ring-red-200 @enderror">
                    </div>

                    {{-- Instructor Name (C_Instructor) - Populated with existing data --}}
                    <div class="relative">
                        <label for="C_Instructor" class="block text-xs font-semibold uppercase text-gray-600 mb-1">Instructor Name (Optional):</label>
                        <input type="text" 
                               name="C_Instructor" 
                               id="C_Instructor" 
                               value="{{ old('C_Instructor', $course->C_Instructor) }}"
                               placeholder="Enter lead instructor name"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50/70 text-gray-900 placeholder-gray-400 focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition shadow-inner text-sm @error('C_Instructor') border-red-500 ring-red-200 @enderror">
                    </div>

                </div>
            </div>

            <div class="space-y-4 pt-2">
                <h2 class="text-lg font-bold text-gray-800 border-b pb-1 border-gray-100">Course Description</h2>

                <div>
                    <label for="C_Description" class="block text-xs font-semibold uppercase text-gray-600 mb-1">Detailed Description (Optional):</label>
                    <textarea name="C_Description" 
                              id="C_Description" 
                              rows="4"
                              placeholder="Provide a detailed summary of the course content, learning objectives, and expected outcomes."
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50/70 text-gray-900 placeholder-gray-400 focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition shadow-inner text-sm @error('C_Description') border-red-500 ring-red-200 @enderror">{{ old('C_Description', $course->C_Description) }}</textarea>
                </div>
            </div>

            {{-- Action Buttons --}}
            <div class="pt-4 flex justify-end space-x-4">
                
                {{-- Update Button (Primary Teal) --}}
                <button type="submit" class="px-6 py-2.5 bg-teal-600 text-white rounded-lg font-bold hover:bg-teal-700 transition duration-300 shadow-lg shadow-teal-600/40 transform hover:scale-[1.02] text-sm">
                    UPDATE COURSE
                </button>
                
                {{-- Cancel Button (Secondary Red/Danger) --}}
                <a href="{{ route('admin.viewAllCourse') }}" 
                   class="px-6 py-2.5 bg-red-600 text-white rounded-lg font-bold hover:bg-red-700 transition duration-300 shadow-lg shadow-red-600/30 inline-flex items-center text-sm">
                    CANCEL
                </a>
            </div>

        </form>
        {{-- FORM END --}}

    </div>
</div>
{{-- END: Centering Container --}}

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
                html: '<p class="text-sm text-gray-600 mb-3">Please correct the following errors:</p>' +
                      '<ul>' +
                      @foreach ($errors->all() as $error)
                          '<li>- {{ $error }}</li>' +
                      @endforeach
                      '</ul>' +
                      '<p class="mt-4 text-xs">Highlighted fields require attention.</p>',
                confirmButtonText: 'Got it',
                confirmButtonColor: '#10B981'
            });
        @endif
    });
</script>
@endpush