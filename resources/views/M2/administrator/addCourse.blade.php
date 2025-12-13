@extends('layout.administrator')

@section('title', 'Add New Course')

@section('content')

{{-- 
    NEW: Centering Container with Top Margin
    Added mt-8 to ensure a visible gap from the fixed header, 
    since the layout's <main> padding appears insufficient or overridden.
--}}
<div class="max-w-7xl mx-auto mt-8"> 

    {{-- Main Content Card: The form container itself --}}
    <div class="bg-white p-8 rounded-2xl shadow-xl shadow-gray-400/30 border border-gray-100/80">

        <div class="flex items-center justify-between mb-6 border-b pb-3 border-gray-100">
            <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">
                Add New Course
            </h1>
            {{-- Decorative icon matching the modern theme --}}
            <div class="text-teal-600 text-xl">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                </svg>
            </div>
        </div>

        {{-- Display Validation Errors --}}
        @if ($errors->any())
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-3 mb-5 rounded-xl text-sm" role="alert">
                <p class="font-bold">Validation Error:</p>
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

            <div class="space-y-4">
                <h2 class="text-lg font-bold text-gray-800 border-b pb-1 border-gray-100">Course Identification</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    
                    {{-- Course Code (C_Code) --}}
                    <div class="relative">
                        <label for="C_Code" class="block text-xs font-semibold uppercase text-gray-600 mb-1">Course Code:</label>
                        <input type="text" 
                               name="C_Code" 
                               id="C_Code" 
                               value="{{ old('C_Code') }}"
                               placeholder="e.g., BCN1010"
                               required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50/70 text-gray-900 placeholder-gray-400 focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition shadow-inner text-sm @error('C_Code') border-red-500 ring-red-200 @enderror">
                        @error('C_Code')
                            <p class="text-red-500 text-xs mt-1 absolute">Required and must be unique.</p>
                        @enderror
                    </div>

                    {{-- Course Name (C_Name) --}}
                    <div class="relative">
                        <label for="C_Name" class="block text-xs font-semibold uppercase text-gray-600 mb-1">Course Name:</label>
                        <input type="text" 
                               name="C_Name" 
                               id="C_Name" 
                               value="{{ old('C_Name') }}"
                               placeholder="e.g., Data Mining Fundamentals"
                               required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50/70 text-gray-900 placeholder-gray-400 focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition shadow-inner text-sm @error('C_Name') border-red-500 ring-red-200 @enderror">
                    </div>

                </div>
            </div>
            
            <div class="space-y-4 pt-2">
                <h2 class="text-lg font-bold text-gray-800 border-b pb-1 border-gray-100">Logistics and Requirements</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                    {{-- Credit Hour (C_Hour) --}}
                    <div class="relative">
                        <label for="C_Hour" class="block text-xs font-semibold uppercase text-gray-600 mb-1">Credit Hour:</label>
                        <input type="number" 
                               name="C_Hour" 
                               id="C_Hour" 
                               value="{{ old('C_Hour') }}"
                               placeholder="3 or 4"
                               required
                               min="1"
                               max="8"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50/70 text-gray-900 placeholder-gray-400 focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition shadow-inner text-sm @error('C_Hour') border-red-500 ring-red-200 @enderror">
                        @error('C_Hour')
                            <p class="text-red-500 text-xs mt-1 absolute">Required (1-8 hours).</p>
                        @enderror
                    </div>

                    {{-- Semester Offered (C_SemOffered) --}}
                    <div class="relative">
                        <label for="C_SemOffered" class="block text-xs font-semibold uppercase text-gray-600 mb-1">Semester Offered:</label>
                        <select name="C_SemOffered" 
                                id="C_SemOffered" 
                                required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50/70 text-gray-900 focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition shadow-inner text-sm @error('C_SemOffered') border-red-500 ring-red-200 @enderror">
                            <option value="" disabled selected class="text-gray-400">Select semester number (1, 2, or 3)</option>
                            <option value="1" {{ old('C_SemOffered') == '1' ? 'selected' : '' }}>Semester 1</option>
                            <option value="2" {{ old('C_SemOffered') == '2' ? 'selected' : '' }}>Semester 2</option>
                            <option value="3" {{ old('C_SemOffered') == '3' ? 'selected' : '' }}>Semester 3 (Short/Special)</option>
                        </select>
                        @error('C_SemOffered')
                            <p class="text-red-500 text-xs mt-1 absolute">Required (1, 2, or 3).</p>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    
                    {{-- Prerequisites (C_Prerequisites) --}}
                    <div class="relative">
                        <label for="C_Prerequisites" class="block text-xs font-semibold uppercase text-gray-600 mb-1">Prerequisites (Optional):</label>
                        <input type="text" 
                               name="C_Prerequisites" 
                               id="C_Prerequisites" 
                               value="{{ old('C_Prerequisites') }}"
                               placeholder="Comma-separated codes (e.g., BCN1001, BCN1002)"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50/70 text-gray-900 placeholder-gray-400 focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition shadow-inner text-sm @error('C_Prerequisites') border-red-500 ring-red-200 @enderror">
                    </div>

                    {{-- Instructor Name (C_Instructor) --}}
                    <div class="relative">
                        <label for="C_Instructor" class="block text-xs font-semibold uppercase text-gray-600 mb-1">Instructor Name (Optional):</label>
                        <input type="text" 
                               name="C_Instructor" 
                               id="C_Instructor" 
                               value="{{ old('C_Instructor') }}"
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
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50/70 text-gray-900 placeholder-gray-400 focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition shadow-inner text-sm @error('C_Description') border-red-500 ring-red-200 @enderror">{{ old('C_Description') }}</textarea>
                </div>
            </div>

            {{-- Action Buttons --}}
            <div class="pt-4 flex justify-end space-x-4">
                
                {{-- Complete Button (Primary Teal) --}}
                <button type="submit" class="px-6 py-2.5 bg-teal-600 text-white rounded-lg font-bold hover:bg-teal-700 transition duration-300 shadow-lg shadow-teal-600/40 transform hover:scale-[1.02] text-sm">
                    COMPLETE & SAVE
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