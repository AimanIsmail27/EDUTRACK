@extends('layout.administrator')

@section('title', 'Course Overview: ' . $course->C_Code) 

@section('content')

{{-- Define the active tab. Default to 'overview' --}}
@php
    $activeTab = request()->query('tab', 'overview');
@endphp

{{-- Centering Container with Top Margin (Matches Add/Edit Pages) --}}
<div class="max-w-7xl mx-auto mt-8"> 

    {{-- Main Content Card --}}
    <div class="bg-white p-8 rounded-2xl shadow-xl shadow-gray-400/30 border border-gray-100/80">

        {{-- Main Header and Course Title --}}
        <div class="mb-6 pb-4 border-b border-gray-200">
            <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">
                {{ $course->C_Code }}: {{ $course->C_Name }}
            </h1>
            <p class="text-lg text-gray-500 mt-1">Course Management & Overview</p>
        </div>

        {{-- Course Navigation Tabs --}}
        <div class="flex border-b border-gray-200 mb-8">
            
            {{-- Helper function for active class --}}
            @php
                $tabClass = function($tabName) use ($activeTab) {
                    return $activeTab === $tabName 
                        ? 'border-b-2 border-teal-600 text-teal-600 font-bold bg-teal-50/50' 
                        : 'border-b-2 border-transparent text-gray-600 hover:border-gray-300';
                };
            @endphp
            
            {{-- Overview Tab --}}
            <a href="{{ route('admin.courses.show', ['code' => $course->C_Code, 'tab' => 'overview']) }}" 
               class="px-6 py-2 rounded-t-lg transition {{ $tabClass('overview') }}">
                Overview
            </a>
            
            {{-- Participants Tab --}}
            <a href="{{ route('admin.courses.show', ['code' => $course->C_Code, 'tab' => 'participants']) }}" 
               class="px-6 py-2 rounded-t-lg transition {{ $tabClass('participants') }}">
                Participants
            </a>
            
            {{-- Assessment Tab --}}
            <a href="{{ route('admin.courses.show', ['code' => $course->C_Code, 'tab' => 'assessment']) }}" 
               class="px-6 py-2 rounded-t-lg transition {{ $tabClass('assessment') }}">
                Assessment
            </a>
            
            {{-- Grade Tab --}}
            <a href="{{ route('admin.courses.show', ['code' => $course->C_Code, 'tab' => 'grade']) }}" 
               class="px-6 py-2 rounded-t-lg transition {{ $tabClass('grade') }}">
                Grade
            </a>
        </div>

        {{-- DYNAMIC CONTENT SECTION --}}
        
        @if ($activeTab === 'overview')
            {{-- OVERVIEW CONTENT (Weekly Plan) --}}
            <div class="space-y-8">
                <h2 class="text-2xl font-bold text-gray-800 border-b pb-2 border-gray-100">Weekly Plan</h2>
                
                {{-- Weekly Plan Item 1 (Week 1) --}}
                <div x-data="{ open: true }" class="border border-gray-300 rounded-xl shadow-md overflow-hidden bg-white">
                    <div @click="open = !open" class="flex justify-between items-center p-4 bg-teal-100/50 cursor-pointer hover:bg-teal-200/50 transition">
                        <h3 class="font-bold text-lg text-teal-800">Week 1 (10-17 October)</h3>
                        <svg class="w-5 h-5 text-teal-600 transform transition-transform duration-300" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </div>
                    
                    <div x-show="open" x-collapse.duration.300ms class="p-4 space-y-3 bg-gray-50">
                        <div class="flex items-start text-gray-700">
                            <svg class="w-5 h-5 mr-3 mt-1 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            <p>Chap 0: Introduction</p>
                        </div>
                        <div class="flex items-start text-gray-700">
                            <svg class="w-5 h-5 mr-3 mt-1 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            <p>Chap 1: Introduction to Statistic</p>
                        </div>
                        <div class="flex items-start text-teal-700">
                            <svg class="w-5 h-5 mr-3 mt-1 text-teal-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z"/></svg>
                            <p class="font-semibold">Discussion Time!</p>
                        </div>
                    </div>
                </div>

                {{-- Weekly Plan Item 2 (Week 2) --}}
                <div x-data="{ open: true }" class="border border-gray-300 rounded-xl shadow-md overflow-hidden bg-white">
                    <div @click="open = !open" class="flex justify-between items-center p-4 bg-teal-100/50 cursor-pointer hover:bg-teal-200/50 transition">
                        <h3 class="font-bold text-lg text-teal-800">Week 2 (20-27 October)</h3>
                        <svg class="w-5 h-5 text-teal-600 transform transition-transform duration-300" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </div>
                    
                    <div x-show="open" x-collapse.duration.300ms class="p-4 space-y-3 bg-gray-50">
                        <div class="flex items-start text-gray-700">
                            <svg class="w-5 h-5 mr-3 mt-1 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            <p>Chap 2: Introduction</p>
                        </div>
                        <div class="flex items-start text-gray-700">
                            <svg class="w-5 h-5 mr-3 mt-1 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" /></svg>
                            <p>Lab 1: Standard Deviation</p>
                        </div>
                        <div class="flex items-start text-teal-700">
                            <svg class="w-5 h-5 mr-3 mt-1 text-teal-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z"/></svg>
                            <p class="font-semibold">Discussion Time!</p>
                        </div>
                    </div>
                </div>
                
            </div>
            
        @elseif ($activeTab === 'participants')
            {{-- PARTICIPANTS CONTENT --}}
            <h2 class="text-2xl font-bold text-gray-800">Participants List</h2>
            <p class="text-gray-600">This section will list all students and lecturers enrolled in {{ $course->C_Name }}.</p>
            <div class="p-4 bg-gray-100 rounded-lg border border-gray-200">
                <p>--- Participant Management Table goes here ---</p>
            </div>
            
        @elseif ($activeTab === 'assessment')
            {{-- ASSESSMENT CONTENT --}}
            <h2 class="text-2xl font-bold text-gray-800">Assessment Breakdown</h2>
            <p class="text-gray-600">This section will show quizzes, exams, and project submission statuses for {{ $course->C_Name }}.</p>
            <div class="p-4 bg-gray-100 rounded-lg border border-gray-200">
                <p>--- Assessment Details go here ---</p>
            </div>

        @elseif ($activeTab === 'grade')
            {{-- GRADE CONTENT --}}
            <h2 class="text-2xl font-bold text-gray-800">Course Grades</h2>
            <p class="text-gray-600">This section displays the final grades and grade distribution for {{ $course->C_Name }}.</p>
            <div class="p-4 bg-gray-100 rounded-lg border border-gray-200">
                <p>--- Grade Table and Analytics go here ---</p>
            </div>

        @endif
        {{-- END DYNAMIC CONTENT SECTION --}}

    </div>
</div>

@endsection

@push('scripts')
{{-- Include SweetAlert scripts if needed for any tab content (e.g., deleting a student from participants) --}}
@endpush