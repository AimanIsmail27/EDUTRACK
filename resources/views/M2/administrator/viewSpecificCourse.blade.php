@extends('layout.administrator')

@section('title', 'Course Overview: ' . $course->C_Code) 

@section('content')

@php
    $activeTab = request()->query('tab', 'overview');

    // --- 1. MASTER DATA FOR GRADES (Must contain q1, q2, ia, gp keys) ---
    $allStudentsWithGrades = [
        ['matric_id' => 'CA22001', 'full_name' => 'Ahmad Bin Hassan', 'q1' => 8.5, 'q2' => 9.0, 'ia' => 28.0, 'gp' => 45.0],
        ['matric_id' => 'CA22002', 'full_name' => 'Siti Aisyah Binti Azman', 'q1' => 9.5, 'q2' => 9.5, 'ia' => 29.5, 'gp' => 48.0],
        ['matric_id' => 'CA22003', 'full_name' => 'Tan Kar Wei', 'q1' => 7.0, 'q2' => 7.5, 'ia' => 25.0, 'gp' => 40.0],
        ['matric_id' => 'CA22004', 'full_name' => 'Magesh A/L Kumar', 'q1' => 6.0, 'q2' => 8.0, 'ia' => 26.5, 'gp' => 42.5],
        ['matric_id' => 'CA22005', 'full_name' => 'Nurul Huda Binti Ramli', 'q1' => 9.0, 'q2' => 8.5, 'ia' => 29.0, 'gp' => 47.0],
        ['matric_id' => 'CA22006', 'full_name' => 'Lim Chee Keong', 'q1' => 7.5, 'q2' => 8.0, 'ia' => 27.0, 'gp' => 44.0],
        ['matric_id' => 'CA22007', 'full_name' => 'Priya Devi A/P Suresh', 'q1' => 8.0, 'q2' => 7.0, 'ia' => 24.0, 'gp' => 39.5],
        ['matric_id' => 'CA22008', 'full_name' => 'Muhammad Faiz Bin Zulkifli', 'q1' => 9.0, 'q2' => 9.0, 'ia' => 30.0, 'gp' => 50.0],
        ['matric_id' => 'CA22009', 'full_name' => 'Lee Mei Ling', 'q1' => 8.8, 'q2' => 8.9, 'ia' => 28.5, 'gp' => 46.5],
        ['matric_id' => 'CA22010', 'full_name' => 'Baljeet Singh S/O Harjit', 'q1' => 6.5, 'q2' => 7.5, 'ia' => 23.0, 'gp' => 38.0],
    ];

    // --- 2. DUMMY DATA FOR SEARCH SUGGESTIONS (NOT IN COURSE YET) ---
    $availableStudents = [
        ['matric_id' => 'CB23011', 'full_name' => 'Zara Binti Zainal'],
        ['matric_id' => 'CA23012', 'full_name' => 'Ali Bin Mutu'],
        ['matric_id' => 'CA20013', 'full_name' => 'Jason Lee Kai'],
        ['matric_id' => 'CB21014', 'full_name' => 'Fatimah Binti Khalid'],
        ['matric_id' => 'CA20015', 'full_name' => 'Chris Veng Veng'],
        ['matric_id' => 'CB23016', 'full_name' => 'Amran Bin Ali'],
    ];

    // --- Dynamic Data Source Determination (Fixing the Grade tab error) ---
    $sourceArray = ($activeTab === 'grade') ? $allStudentsWithGrades : 
        array_map(function($student) {
            return ['matric_id' => $student['matric_id'], 'full_name' => $student['full_name']];
        }, $allStudentsWithGrades);

    // PAGINATION LOGIC 
    $perPage = 5;
    $currentPage = (int) request()->query('page', 1);
    $totalStudents = count($sourceArray); 
    $totalPages = ceil($totalStudents / $perPage);
    $offset = ($currentPage - 1) * $perPage;
    $studentsOnPage = array_slice($sourceArray, $offset, $perPage);
    $startItem = min($totalStudents, $offset + 1);
    $endItem = min($totalStudents, $offset + $perPage);

    // Helper for tabs
    $tabClass = function($tabName) use ($activeTab) {
        return $activeTab === $tabName 
            ? 'border-b-2 border-teal-600 text-teal-600 font-bold bg-teal-50/50' 
            : 'border-b-2 border-transparent text-gray-600 hover:border-gray-300';
    };
    
    // Course data for Synopsis/Participants forms
    $offeredSemesters = explode(',', $course->C_SemOffered);
    $assessmentWeights = ['Quiz 1' => 10, 'Quiz 2' => 10, 'IA' => 30, 'GP' => 50];
@endphp

{{-- Centering Container with Top Margin (PROVEN LAYOUT PATTERN) --}}
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
            <a href="{{ route('admin.courses.show', ['code' => $course->C_Code, 'tab' => 'overview']) }}" 
               class="px-6 py-2 rounded-t-lg transition {{ $tabClass('overview') }}">Overview</a>
            
            <a href="{{ route('admin.courses.show', ['code' => $course->C_Code, 'tab' => 'participants']) }}" 
               class="px-6 py-2 rounded-t-lg transition {{ $tabClass('participants') }}">Participants</a>
            
            <a href="{{ route('admin.courses.show', ['code' => $course->C_Code, 'tab' => 'assessment']) }}" 
               class="px-6 py-2 rounded-t-lg transition {{ $tabClass('assessment') }}">Assessment</a>
            
            <a href="{{ route('admin.courses.show', ['code' => $course->C_Code, 'tab' => 'grade']) }}" 
               class="px-6 py-2 rounded-t-lg transition {{ $tabClass('grade') }}">Grade</a>
        </div>

        {{-- DYNAMIC CONTENT SECTION --}}
        
        @if ($activeTab === 'overview')
            {{-- OVERVIEW CONTENT --}}
            <div class="space-y-8">
                
                {{-- COURSE SYNOPSIS SECTION --}}
                <div class="p-6 bg-indigo-50/50 rounded-xl border border-indigo-200 shadow-lg">
                    <h2 class="text-2xl font-bold text-indigo-800 border-b pb-2 border-indigo-200 mb-4">Course Synopsis</h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-y-4 gap-x-6 text-gray-800">
                        <div class="flex items-center space-x-2"><span class="text-indigo-600 text-xl">📚</span><p class="text-sm"><span class="font-semibold">Credit Hours:</span> {{ $course->C_Hour }}</p></div>
                        <div class="flex items-center space-x-2"><span class="text-indigo-600 text-xl">👨‍🏫</span><p class="text-sm"><span class="font-semibold">Instructor:</span> {{ $course->C_Instructor ?? 'N/A' }}</p></div>
                        <div class="flex items-center space-x-2"><span class="text-indigo-600 text-xl">🗓️</span><p class="text-sm"><span class="font-semibold">Semesters Offered:</span> {{ str_replace(',', ', ', $course->C_SemOffered) }}</p></div>
                        <div class="md:col-span-3 flex items-center space-x-2"><span class="text-indigo-600 text-xl">🔗</span><p class="text-sm"><span class="font-semibold">Prerequisites:</span> {{ $course->C_Prerequisites ?? 'None' }}</p></div>
                    </div>

                    @if ($course->C_Description)
                        <div class="mt-4 pt-3 border-t border-indigo-200">
                            <p class="font-semibold text-sm mb-1">Detailed Description:</p>
                            <p class="text-sm text-gray-700 leading-relaxed">{{ $course->C_Description }}</p>
                        </div>
                    @endif
                </div>

                <h2 class="text-2xl font-bold text-gray-800 border-b pb-2 border-gray-100">Weekly Plan</h2>
                
                {{-- DYNAMICALLY GENERATED 14 WEEKS (RETAINED) --}}
                @for ($week = 1; $week <= 14; $week++)
                    @php
                        $isBreak = ($week === 8);
                        $weekTitle = $isBreak ? "Week 8: Mid-Term Break" : "Week $week";
                        $headerClass = $isBreak ? 'bg-red-100/70 text-red-800' : 'bg-teal-100/50 text-teal-800';
                    @endphp
                    <div x-data="{ open: {{ $week <= 2 ? 'true' : 'false' }} }" 
                         class="border border-gray-300 rounded-xl shadow-md overflow-hidden bg-white">
                        <div @click="open = !open" class="flex justify-between items-center p-4 cursor-pointer transition {{ $headerClass }} hover:opacity-80">
                            <h3 class="font-bold text-lg">{{ $weekTitle }} (Dates Placeholder)</h3>
                            <svg class="w-5 h-5 transform transition-transform duration-300" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </div>
                        <div x-show="open" x-collapse.duration.300ms class="p-4 space-y-3 bg-gray-50">
                            @if ($isBreak)
                                <div class="p-3 bg-red-50 text-red-700 rounded-lg text-center font-semibold">No classes this week. Enjoy the break!</div>
                            @else
                                <div class="flex items-start text-gray-700"><svg class="w-5 h-5 mr-3 mt-1 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg><p>Lecture: Topic for Week {{ $week }}</p></div>
                                <div class="flex items-start text-teal-700"><svg class="w-5 h-5 mr-3 mt-1 text-teal-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z"/></svg><p class="font-semibold">Discussion & Q/A Session</p></div>
                            @endif
                        </div>
                    </div>
                @endfor
            </div>
            
        @elseif ($activeTab === 'participants')
            {{-- PARTICIPANTS CONTENT --}}
            <h2 class="text-2xl font-bold text-gray-800 mb-4">Participants List ({{ $totalStudents }} Students)</h2>

            {{-- Participant Controls: Search, Semester Select, and Add FORM --}}
            <form id="addParticipantForm" method="POST" action="{{ route('admin.course.addParticipant', $course->C_Code) ?? '#' }}" 
                  class="flex flex-col md:flex-row items-start md:items-center gap-3 mb-6 p-4 bg-gray-50 rounded-lg border border-gray-200">
                @csrf
                
                {{-- 1. Student Search (Autocomplete) --}}
                <div class="relative flex-grow w-full md:w-1/2 min-w-[250px]">
                    <input type="hidden" name="matric_id" id="selected-student-id" required> 
                    <input type="text" id="student-search" placeholder="Search Matric ID or Name (e.g., CA20...)" 
                           class="w-full p-2 border border-gray-300 rounded-md focus:ring-teal-500 focus:border-teal-500 shadow-inner" autocomplete="off">
                    
                    <div id="suggestion-box" class="absolute z-20 w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-lg max-h-48 overflow-y-auto hidden">
                        {{-- Suggestions will be populated by JavaScript --}}
                    </div>
                </div>
                
                {{-- 2. Semester Selection Dropdown --}}
                <div class="w-full md:w-1/4">
                    <select name="semester" id="add-semester" required
                            class="w-full p-2 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500 shadow-inner text-sm">
                        <option value="">Select Enrollment Semester</option>
                        @foreach ($offeredSemesters as $sem)
                            <option value="{{ $sem }}">Semester {{ $sem }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- 3. Add button (Disabled until a valid student is selected) --}}
                <button type="submit" id="add-participant-btn" disabled
                        class="w-full md:w-auto px-4 py-2 bg-teal-600 text-white rounded-md font-semibold transition shadow-md disabled:bg-teal-300 hover:bg-teal-700 text-sm">
                    <i class="fas fa-user-plus mr-1"></i> Add Participant
                </button>
            </form>

            {{-- Display Validation Errors for Add Participant Form --}}
            @if ($errors->any() && session('participant_add_attempt'))
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-3 mb-5 rounded-lg text-sm" role="alert">
                    <p class="font-bold">Error Adding Participant:</p>
                    <ul class="list-disc ml-5 mt-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Participants Table (Retained pagination and structure) --}}
            <div class="overflow-x-auto rounded-xl shadow-lg border border-gray-100">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-teal-50/70">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Matric ID</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Full Name</th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        @foreach ($studentsOnPage as $participant)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-teal-700">{{ $participant['matric_id'] }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $participant['full_name'] }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <button class="text-indigo-600 hover:text-indigo-900 mr-3">View Profile</button>
                                <button class="text-red-600 hover:text-red-900">Remove</button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Pagination Controls (Retained) --}}
            <div class="mt-4 flex justify-between items-center text-sm text-gray-600">
                <p>Showing {{ $startItem }} to {{ $endItem }} of {{ $totalStudents }} total participants</p>
                <div class="flex space-x-1">
                    @if ($currentPage > 1)
                        <a href="{{ route('admin.courses.show', ['code' => $course->C_Code, 'tab' => 'participants', 'page' => $currentPage - 1]) }}" class="px-3 py-1 border rounded-md bg-white hover:bg-gray-100 cursor-pointer">Previous</a>
                    @else
                        <span class="px-3 py-1 border rounded-md bg-gray-200 text-gray-500 cursor-not-allowed">Previous</span>
                    @endif
                    @for ($i = 1; $i <= $totalPages; $i++)
                        <a href="{{ route('admin.courses.show', ['code' => $course->C_Code, 'tab' => 'participants', 'page' => $i]) }}" class="px-3 py-1 border rounded-md @if($currentPage === $i) bg-teal-600 text-white font-semibold @else hover:bg-gray-100 @endif">{{ $i }}</a>
                    @endfor
                    @if ($currentPage < $totalPages)
                        <a href="{{ route('admin.courses.show', ['code' => $course->C_Code, 'tab' => 'participants', 'page' => $currentPage + 1]) }}" class="px-3 py-1 border rounded-md bg-white hover:bg-gray-100 cursor-pointer">Next</a>
                    @else
                        <span class="px-3 py-1 border rounded-md bg-gray-200 text-gray-500 cursor-not-allowed">Next</span>
                    @endif
                </div>
            </div>
            
        @elseif ($activeTab === 'assessment')
            {{-- ASSESSMENT CONTENT --}}
            <h2 class="text-2xl font-bold text-gray-800 border-b pb-2 border-gray-100 mb-6">Assessment Breakdown (100% Total)</h2>
            <div class="space-y-4">
                @foreach (['Quiz 1: Chapters 1-3' => '10%', 'Quiz 2: Chapters 4-6' => '10%', 'Individual Assignment' => '30%', 'Group Project & Presentation' => '50%'] as $name => $weight)
                    <div class="flex items-center justify-between p-4 bg-teal-50 rounded-xl border border-teal-200 shadow-sm">
                        <div class="flex items-center space-x-4">
                            <span class="text-2xl">{{ str_contains($name, 'Quiz') ? '📚' : (str_contains($name, 'Group') ? '👥' : '✏️') }}</span>
                            <p class="font-semibold text-lg text-gray-800">{{ $name }}</p>
                        </div>
                        <div class="text-right">
                            <span class="text-xl font-extrabold text-teal-600">{{ $weight }}</span>
                            <p class="text-sm text-gray-500">of Final Grade</p>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="mt-8 p-6 bg-gray-50 border-l-4 border-teal-500 rounded-lg shadow-inner">
                <h3 class="font-bold text-xl text-gray-700">Course Grading Policy Summary</h3>
                <p class="text-gray-600 mt-2 text-sm">Student performance is evaluated based on continuous assessment throughout the semester. The total weightage is 100%, covering two quizzes (20%), one major individual assignment (30%), and a group project (50%).</p>
            </div>
            

        @elseif ($activeTab === 'grade')
            {{-- GRADE CONTENT --}}
            <h2 class="text-2xl font-bold text-gray-800 border-b pb-2 border-gray-100 mb-6">Course Grades Summary</h2>
            <div class="p-3 bg-gray-50 rounded-xl border border-gray-200 mb-4 shadow-sm">
                <p class="text-sm text-gray-700 font-semibold">Weightage: Quiz 1 (10%), Quiz 2 (10%), Indiv. Assign. (30%), Group Project (50%).</p>
            </div>
            <div class="overflow-x-auto rounded-xl shadow-lg border border-gray-100">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-teal-50/70">
                        <tr>
                            <th scope="col" class="sticky left-0 bg-teal-50/70 px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider z-10">Matric ID</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Full Name</th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Quiz 1 (10%)</th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Quiz 2 (10%)</th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Indiv. Assign. (30%)</th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Group Project (50%)</th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-extrabold text-gray-800 uppercase tracking-wider bg-teal-200/50">Total Score (100%)</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        @foreach ($studentsOnPage as $student)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="sticky left-0 bg-white/90 px-6 py-4 whitespace-nowrap text-sm font-medium text-teal-700 z-10">{{ $student['matric_id'] }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $student['full_name'] }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm">{{ number_format($student['q1'], 1) }}%</td>
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm">{{ number_format($student['q2'], 1) }}%</td>
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm">{{ number_format($student['ia'], 1) }}%</td>
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm">{{ number_format($student['gp'], 1) }}%</td>
                            @php $total = $student['q1'] + $student['q2'] + $student['ia'] + $student['gp']; @endphp
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-extrabold bg-teal-100/50 {{ $total >= 50 ? 'text-green-700' : 'text-red-700' }}">
                                {{ number_format($total, 1) }}%
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-4 flex justify-between items-center text-sm text-gray-600">
                <p>Showing {{ $startItem }} to {{ $endItem }} of {{ $totalStudents }} total participants</p>
                <div class="flex space-x-1">
                    @if ($currentPage > 1)
                        <a href="{{ route('admin.courses.show', ['code' => $course->C_Code, 'tab' => 'grade', 'page' => $currentPage - 1]) }}" class="px-3 py-1 border rounded-md bg-white hover:bg-gray-100 cursor-pointer">Previous</a>
                    @else
                        <span class="px-3 py-1 border rounded-md bg-gray-200 text-gray-500 cursor-not-allowed">Previous</span>
                    @endif
                    @for ($i = 1; $i <= $totalPages; $i++)
                        <a href="{{ route('admin.courses.show', ['code' => $course->C_Code, 'tab' => 'grade', 'page' => $i]) }}" class="px-3 py-1 border rounded-md @if($currentPage === $i) bg-teal-600 text-white font-semibold @else hover:bg-gray-100 @endif">{{ $i }}</a>
                    @endfor
                    @if ($currentPage < $totalPages)
                        <a href="{{ route('admin.courses.show', ['code' => $course->C_Code, 'tab' => 'grade', 'page' => $currentPage + 1]) }}" class="px-3 py-1 border rounded-md bg-white hover:bg-gray-100 cursor-pointer">Next</a>
                    @else
                        <span class="px-3 py-1 border rounded-md bg-gray-200 text-gray-500 cursor-not-allowed">Next</span>
                    @endif
                </div>
            </div>

        @endif
        {{-- END DYNAMIC CONTENT SECTION --}}

    </div>
</div>

@endsection

@push('scripts')
{{-- Include SweetAlert scripts if needed --}}
<script>
    // Make the available student data accessible to JavaScript
    const availableStudents = @json($availableStudents);

    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('student-search');
        const suggestionBox = document.getElementById('suggestion-box');
        const selectedStudentIdInput = document.getElementById('selected-student-id');
        const semesterSelect = document.getElementById('add-semester'); 
        const addButton = document.getElementById('add-participant-btn');
        
        // Helper function to check if both student and semester are selected
        function checkReadiness() {
            // Check if selectedStudentIdInput has a value (student selected from list)
            const studentSelected = !!selectedStudentIdInput.value;
            // Check if semesterSelect has a value (semester selected from dropdown)
            const semesterSelected = !!semesterSelect.value;
            // Enable button only if both conditions are true
            addButton.disabled = !(studentSelected && semesterSelected);
        }

        // Function to filter students based on input
        function filterStudents(query) {
            query = query.toLowerCase();
            return availableStudents.filter(student => 
                student.matric_id.toLowerCase().includes(query) || 
                student.full_name.toLowerCase().includes(query)
            );
        }

        // Function to render the suggestion box
        function renderSuggestions(filteredStudents) {
            suggestionBox.innerHTML = '';
            
            if (filteredStudents.length === 0 || searchInput.value.length < 2) {
                suggestionBox.classList.add('hidden');
                return;
            }

            filteredStudents.forEach(student => {
                const item = document.createElement('div');
                item.className = 'p-2 cursor-pointer hover:bg-teal-100 text-sm text-gray-800 border-b border-gray-100 last:border-b-0';
                item.innerHTML = `<span class="font-semibold text-teal-700">${student.matric_id}</span> - ${student.full_name}`;
                
                item.addEventListener('click', () => {
                    // 1. Set the visible input text
                    searchInput.value = `${student.matric_id} - ${student.full_name}`;
                    
                    // 2. Set the hidden input value (for form submission)
                    selectedStudentIdInput.value = student.matric_id;
                    
                    // 3. Hide the suggestion box
                    suggestionBox.classList.add('hidden');
                    
                    // 4. Check readiness and enable the Add button
                    checkReadiness();
                });
                suggestionBox.appendChild(item);
            });
            
            suggestionBox.classList.remove('hidden');
        }

        // Handle input change event
        searchInput.addEventListener('input', () => {
            const query = searchInput.value;
            // Clear selected student and disable button if input changes
            selectedStudentIdInput.value = '';

            if (query.length >= 2) {
                const filtered = filterStudents(query);
                renderSuggestions(filtered);
            } else {
                suggestionBox.classList.add('hidden');
            }
            checkReadiness();
        });
        
        // Handle semester change event
        semesterSelect.addEventListener('change', checkReadiness);

        // Hide suggestions when clicking outside
        document.addEventListener('click', (e) => {
            if (!suggestionBox.contains(e.target) && e.target !== searchInput) {
                suggestionBox.classList.add('hidden');
            }
        });
        
        // Final check before submission (client-side form validation before sending)
        document.getElementById('addParticipantForm').addEventListener('submit', function(e) {
            // Check readiness again just to be safe (though button should be disabled)
            if (!selectedStudentIdInput.value || !semesterSelect.value) {
                e.preventDefault();
                alert('Please select a student from the suggestion list AND select an enrollment semester.');
            }
            // Add a hidden field to flag that a submission attempt occurred
            const hiddenInput = document.createElement('input');
            hiddenInput.type = 'hidden';
            hiddenInput.name = 'participant_add_attempt';
            hiddenInput.value = '1';
            this.appendChild(hiddenInput);
        });

        // Initial check on load (in case values are retained)
        checkReadiness();
    });
</script>
@endpush