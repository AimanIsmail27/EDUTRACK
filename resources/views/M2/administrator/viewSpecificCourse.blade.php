@extends('layout.administrator')

@section('title', 'Course Overview: ' . $course->C_Code) 

@section('content')

@php
    $activeTab = request()->query('tab', 'overview');
    $filterSemester = request()->query('view_semester', 'all');

    // These collections come from the Controller
    $courseParticipants = $courseParticipants ?? collect(); 
    $studentGrades = $studentGrades ?? collect(); 

    // Determine data source for pagination based on tab
    $sourceCollection = ($activeTab === 'grade') ? $studentGrades : $courseParticipants;

    // Apply Semester Filter Logic
    if ($filterSemester !== 'all') {
        $sourceCollection = $sourceCollection->filter(function($item) use ($filterSemester) {
            return (string)$item['semester'] === (string)$filterSemester;
        });
    }

    // Pagination logic
    $perPage = 5;
    $currentPage = (int) request()->query('page', 1);
    
    if ($sourceCollection->isEmpty()) {
        $studentsOnPage = [];
        $totalRecords = 0;
        $totalPages = 0;
    } else {
        $totalRecords = $sourceCollection->count();
        $totalPages = ceil($totalRecords / $perPage);
        $offset = ($currentPage - 1) * $perPage;
        $studentsOnPage = $sourceCollection->slice($offset, $perPage)->all();
    }

    $startItem = ($totalRecords > 0) ? min($totalRecords, (($currentPage - 1) * $perPage) + 1) : 0;
    $endItem = min($totalRecords, $currentPage * $perPage);

    $tabClass = function($tabName) use ($activeTab) {
        return $activeTab === $tabName 
            ? 'border-b-2 border-teal-600 text-teal-600 font-bold bg-teal-50/50' 
            : 'border-b-2 border-transparent text-gray-600 hover:border-gray-300';
    };

    $offeredSemesters = explode(',', $course->C_SemOffered);
@endphp

<div class="max-w-7xl mx-auto mt-8"> 
    <div class="bg-white p-8 rounded-2xl shadow-xl shadow-gray-400/30 border border-gray-100/80">

        {{-- Course Header --}}
        <div class="mb-6 pb-4 border-b border-gray-200">
            <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">
                {{ $course->C_Code }}: {{ $course->C_Name }}
            </h1>
            <p class="text-lg text-gray-500 mt-1">Course Management & Overview</p>
        </div>

        {{-- Tabs Navigation --}}
        <div class="flex border-b border-gray-200 mb-8">
            <a href="?tab=overview" class="px-6 py-2 rounded-t-lg transition {{ $tabClass('overview') }}">Overview</a>
            <a href="?tab=participants" class="px-6 py-2 rounded-t-lg transition {{ $tabClass('participants') }}">Participants</a>
            <a href="?tab=assessment" class="px-6 py-2 rounded-t-lg transition {{ $tabClass('assessment') }}">Assessment</a>
            <a href="?tab=grade" class="px-6 py-2 rounded-t-lg transition {{ $tabClass('grade') }}">Grade</a>
        </div>

        {{-- 1. OVERVIEW TAB --}}
        @if ($activeTab === 'overview')
            <div class="space-y-8">
                <div class="p-6 bg-teal-50/50 rounded-xl border border-teal-200 shadow-lg">
                    <h2 class="text-2xl font-bold text-teal-800 border-b pb-2 border-teal-200 mb-4">Course Synopsis</h2>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-y-4 gap-x-6 text-gray-800">
                        
                        <div class="flex items-center space-x-2">
                            <span class="text-teal-600 text-xl">📚</span>
                            <p class="text-sm"><span class="font-semibold">Credit Hours:</span> {{ $course->C_Hour }}</p>
                        </div>

                        <div class="flex items-center space-x-2">
                            <span class="text-teal-600 text-xl">👨‍🏫</span>
                            <p class="text-sm">
                                <span class="font-semibold">Coordinator:</span> 
                                {{ $course->coordinator->name ?? 'Not Assigned' }}
                            </p>
                        </div>

                        <div class="flex items-center space-x-2">
                            <span class="text-teal-600 text-xl">🗓️</span>
                            <p class="text-sm"><span class="font-semibold">Semesters Offered:</span> {{ str_replace(',', ', ', $course->C_SemOffered) }}</p>
                        </div>

                        <div class="flex items-center space-x-2">
                            <span class="text-teal-600 text-xl">🔗</span>
                            <p class="text-sm"><span class="font-semibold">Prerequisites:</span> {{ $course->C_Prerequisites ?? 'None' }}</p>
                        </div>

                        <div class="md:col-span-2 flex items-start space-x-2">
                            <span class="text-teal-600 text-xl">👥</span>
                            <div class="text-sm">
                                <span class="font-semibold">Teaching Team:</span>
                                @if($course->lecturers->count() > 0)
                                    <span class="text-gray-700">
                                        {{ $course->lecturers->pluck('name')->implode(', ') }}
                                    </span>
                                @else
                                    <span class="text-gray-500 italic">No additional lecturers assigned</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    @if ($course->C_Description)
                        <div class="mt-4 pt-3 border-t border-teal-200">
                            <p class="font-semibold text-sm mb-1 text-teal-800">Detailed Description:</p>
                            <p class="text-sm text-gray-700 leading-relaxed">{{ $course->C_Description }}</p>
                        </div>
                    @endif
                </div>

                {{-- WEEKLY CONTENT - VERTICAL CATEGORY STACK --}}
                <div class="space-y-4">
                    <div class="flex items-center justify-between border-b pb-2 border-gray-100">
                        <h2 class="text-2xl font-bold text-gray-800">Weekly Learning Materials</h2>
                        <span class="text-xs font-semibold text-teal-600 bg-teal-50 px-3 py-1 rounded-full uppercase tracking-wider">Read Only</span>
                    </div>

                    @for ($week = 1; $week <= 14; $week++)
                        @php
                            $isBreak = ($week === 8);
                            $headerClass = $isBreak ? 'bg-red-50 border-red-100' : 'bg-white border-gray-200';
                            $weekMaterials = $course->materials->where('week_number', $week)->groupBy('category');
                        @endphp
                        
                        <div x-data="{ open: {{ $week <= 1 ? 'true' : 'false' }} }" class="border rounded-2xl overflow-hidden transition-all duration-300 {{ $headerClass }} hover:shadow-md mb-2">
                            <div @click="open = !open" class="p-5 flex justify-between items-center cursor-pointer hover:bg-gray-50">
                                <div class="flex items-center gap-4">
                                    <div class="w-8 h-8 rounded-lg {{ $isBreak ? 'bg-red-500' : 'bg-teal-600' }} text-white flex items-center justify-center text-xs font-black shadow-sm">
                                        W{{ $week }}
                                    </div>
                                    <h3 class="font-bold text-lg {{ $isBreak ? 'text-red-700' : 'text-gray-800' }}">
                                        {{ $isBreak ? 'Mid-Term Break' : 'Week ' . $week . ' Content' }}
                                    </h3>
                                </div>
                                <svg class="w-5 h-5 transition-transform duration-300" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </div>

                            <div x-show="open" x-collapse class="px-5 pb-5 pt-2 border-t border-gray-50 bg-gray-50/30">
                                @if($isBreak)
                                    <p class="text-sm text-red-600 italic">No academic delivery scheduled.</p>
                                @elseif($weekMaterials->isEmpty())
                                    <p class="text-sm text-gray-400 italic text-center py-4">No materials uploaded for this week.</p>
                                @else
                                    {{-- VERTICAL STACKING OF CATEGORIES --}}
                                    <div class="space-y-6 mt-2">
                                        @foreach($weekMaterials as $category => $materials)
                                            <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm">
                                                <div class="flex items-center gap-2 mb-4 border-b border-gray-50 pb-2">
                                                    <span class="text-xs font-black text-teal-600 uppercase tracking-widest">{{ $category }}</span>
                                                    <span class="px-2 py-0.5 bg-gray-100 text-[10px] font-bold text-gray-500 rounded-md">{{ $materials->count() }} Files</span>
                                                </div>
                                                
                                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                                    @foreach($materials as $mat)
                                                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg border border-gray-100 hover:border-teal-300 transition-colors group">
                                                            <div class="flex items-center gap-3 overflow-hidden">
                                                                <div class="p-2 bg-white rounded-md shadow-sm text-teal-500 group-hover:text-teal-600">
                                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                                                </div>
                                                                <span class="text-xs font-bold text-gray-700 truncate" title="{{ $mat->title }}">{{ $mat->title }}</span>
                                                            </div>
                                                            <a href="{{ route('materials.download', $mat->id) }}" class="ml-4 p-2 bg-teal-600 text-white rounded-lg hover:bg-teal-700 shadow-md flex-shrink-0 transition-transform active:scale-95" title="Download">
                                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                                            </a>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endfor
                </div>
            </div>

        {{-- 2. PARTICIPANTS TAB --}}
        @elseif ($activeTab === 'participants')
            {{-- Search and Enrollment Form --}}
            <div x-data="{ searchEnrolled: '' }">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-4 gap-4">
                    <h2 class="text-2xl font-bold text-gray-800">Participants List ({{ $totalRecords }} Students)</h2>
                    <div class="flex flex-col sm:flex-row gap-2">
                        <div class="relative">
                            <input type="text" x-model="searchEnrolled" placeholder="Search enrolled..." class="text-sm p-2 pl-8 border border-gray-300 rounded-lg focus:ring-teal-500 shadow-sm">
                            <svg class="w-4 h-4 absolute left-2.5 top-2.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </div>
                        <div class="flex items-center space-x-1 bg-gray-100 p-1 rounded-lg border border-gray-200">
                            <span class="text-[10px] font-bold text-gray-500 px-2 uppercase">View:</span>
                            <a href="?tab=participants&view_semester=all" class="px-2 py-1 text-xs rounded-md transition {{ $filterSemester === 'all' ? 'bg-white shadow-sm text-teal-600 font-bold' : 'text-gray-600' }}">All</a>
                            @foreach ($offeredSemesters as $sem)
                                <a href="?tab=participants&view_semester={{ $sem }}" class="px-2 py-1 text-xs rounded-md transition {{ (string)$filterSemester === (string)$sem ? 'bg-white shadow-sm text-teal-600 font-bold' : 'text-gray-600' }}">{{ $sem }}</a>
                            @endforeach
                        </div>
                    </div>
                </div>

                <form id="addParticipantForm" method="POST" action="{{ route('admin.course.addParticipant', $course->C_Code) }}">
                    @csrf
                    <div class="flex flex-col md:flex-row items-start md:items-center gap-3 mb-6 p-4 bg-gray-50 rounded-lg border border-gray-200">
                        <div class="relative flex-grow w-full md:w-1/2">
                            <input type="hidden" name="matric_id" id="selected-student-id" required> 
                            <input type="text" id="student-search" placeholder="Enroll New Student..." class="w-full p-2 border border-gray-300 rounded-md shadow-inner" autocomplete="off">
                            <div id="suggestion-box" class="absolute z-20 w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-xl max-h-48 overflow-y-auto hidden"></div>
                        </div>
                        <div class="w-full md:w-1/4">
                            <select name="semester" id="add-semester" required class="w-full p-2 border border-gray-300 rounded-md text-sm">
                                <option value="">Select Semester</option>
                                @foreach ($offeredSemesters as $sem)
                                    <option value="{{ $sem }}">Semester {{ $sem }}</option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" id="add-participant-btn" disabled class="px-6 py-2 bg-teal-600 text-white rounded-md font-semibold disabled:bg-gray-300 shadow-md">Enroll</button>
                    </div>
                </form>

                <div class="overflow-x-auto rounded-xl shadow-lg border border-gray-100">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-teal-50/70 uppercase font-bold text-gray-600 text-xs tracking-wider">
                            <tr>
                                <th class="px-6 py-3 text-left">Matric ID</th>
                                <th class="px-6 py-3 text-left">Full Name</th>
                                <th class="px-6 py-3 text-center">Semester</th>
                                <th class="px-6 py-3 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            @forelse ($studentsOnPage as $participant)
                            <tr class="hover:bg-gray-50 transition" x-show="searchEnrolled === '' || '{{ strtolower($participant['matric_id']) }}'.includes(searchEnrolled.toLowerCase()) || '{{ strtolower($participant['full_name']) }}'.includes(searchEnrolled.toLowerCase())">
                                <td class="px-6 py-4 text-sm font-medium text-teal-700">{{ $participant['matric_id'] }}</td>
                                <td class="px-6 py-4 text-sm text-gray-900">{{ $participant['full_name'] }}</td>
                                <td class="px-6 py-4 text-sm text-center text-gray-600">Sem {{ $participant['semester'] ?? '-' }}</td>
                                <td class="px-6 py-4 text-right">
                                    <button type="button" onclick="handleDelete('{{ $participant['matric_id'] }}', '{{ $participant['full_name'] }}')" class="text-red-600 hover:underline text-sm font-semibold">Remove</button>
                                    <form id="remove-form-{{ $participant['matric_id'] }}" action="{{ route('admin.course.removeParticipant', [$course->C_Code, $participant['matric_id']]) }}" method="POST" style="display: none;">@csrf @method('DELETE')</form>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="4" class="px-6 py-8 text-center text-gray-500 italic">No participants found.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        {{-- 3. ASSESSMENT TAB --}}
        @elseif ($activeTab === 'assessment')
            <h2 class="text-2xl font-bold text-gray-800 border-b pb-2 border-gray-100 mb-6">Assessment Breakdown (100% Total)</h2>
            <div class="space-y-4">
                @foreach (['Quiz 1' => '10%', 'Quiz 2' => '10%', 'Individual Assignment' => '30%', 'Group Project' => '50%'] as $name => $weight)
                    <div class="flex items-center justify-between p-4 bg-teal-50 rounded-xl border border-teal-200 shadow-sm">
                        <p class="font-semibold text-lg text-gray-800">{{ $name }}</p>
                        <span class="text-xl font-extrabold text-teal-600">{{ $weight }}</span>
                    </div>
                @endforeach
            </div>

        {{-- 4. GRADE TAB --}}
        @elseif ($activeTab === 'grade')
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
                <h2 class="text-2xl font-bold text-gray-800">Course Grades Summary</h2>
                <div class="flex items-center space-x-1 bg-gray-100 p-1 rounded-lg border border-gray-200">
                    <span class="text-[10px] font-bold text-gray-500 px-2 uppercase">Filter:</span>
                    <a href="?tab=grade&view_semester=all" class="px-3 py-1 text-xs rounded-md transition {{ $filterSemester === 'all' ? 'bg-white shadow-sm text-teal-600 font-bold' : 'text-gray-600' }}">All</a>
                    @foreach ($offeredSemesters as $sem)
                        <a href="?tab=grade&view_semester={{ $sem }}" class="px-3 py-1 text-xs rounded-md transition {{ (string)$filterSemester === (string)$sem ? 'bg-white shadow-sm text-teal-600 font-bold' : 'text-gray-600' }}">Sem {{ $sem }}</a>
                    @endforeach
                </div>
            </div>

            <div class="overflow-x-auto rounded-xl border border-gray-100 shadow-lg">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-teal-50/70 font-bold uppercase text-gray-600">
                        <tr>
                            <th class="px-6 py-3 text-left">Matric ID</th>
                            <th class="px-6 py-3 text-left">Full Name</th>
                            <th class="px-6 py-3 text-center">Quiz 1</th>
                            <th class="px-6 py-3 text-center">Quiz 2</th>
                            <th class="px-6 py-4 text-center">IA</th>
                            <th class="px-6 py-3 text-center">GP</th>
                            <th class="px-6 py-3 text-center bg-teal-100/50">Total</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        @forelse ($studentsOnPage as $student)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 font-medium text-teal-700">{{ $student['matric_id'] }}</td>
                            <td class="px-6 py-4 text-gray-900">{{ $student['full_name'] }}</td>
                            <td class="px-6 py-4 text-center">{{ $student['quiz1'] ?? 0 }}%</td>
                            <td class="px-6 py-4 text-center">{{ $student['quiz2'] ?? 0 }}%</td>
                            <td class="px-6 py-4 text-center">{{ $student['ia'] ?? 0 }}%</td>
                            <td class="px-6 py-4 text-center">{{ $student['gp'] ?? 0 }}%</td>
                            <td class="px-6 py-4 text-center font-bold {{ ($student['total'] ?? 0) >= 50 ? 'text-green-600' : 'text-red-600' }}">
                                {{ $student['total'] ?? 0 }}%
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="7" class="px-6 py-8 text-center text-gray-500 italic">No student records found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    function handleDelete(matricId, name) {
        Swal.fire({
            title: 'Remove Participant?',
            text: `Are you sure you want to remove ${name} (${matricId})?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#0d9488',
            confirmButtonText: 'Yes, remove them!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('remove-form-' + matricId).submit();
            }
        });
    }

    window.onload = function() {
        @if(session('success'))
            Swal.fire({ title: 'Success!', text: "{{ session('success') }}", icon: 'success', confirmButtonColor: '#0d9488' });
        @endif
        @if(session('error'))
            Swal.fire({ title: 'Failed', text: "{{ session('error') }}", icon: 'error', confirmButtonColor: '#0d9488' });
        @endif
    };

    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('student-search');
        const suggestionBox = document.getElementById('suggestion-box');
        const selectedId = document.getElementById('selected-student-id');
        const semSelect = document.getElementById('add-semester');
        const btn = document.getElementById('add-participant-btn');

        if(searchInput) {
            async function fetchStudents(term) {
                try {
                    const response = await fetch(`{{ route('admin.students.search') }}?term=${encodeURIComponent(term)}&course_code={{ $course->C_Code }}`);
                    const students = await response.json();
                    renderSuggestions(students);
                } catch (error) { console.error('Fetch error:', error); }
            }

            function renderSuggestions(students) {
                suggestionBox.innerHTML = '';
                if (students.length === 0) {
                    suggestionBox.innerHTML = '<div class="p-3 text-gray-500 text-sm italic text-center">No student found</div>';
                    suggestionBox.classList.remove('hidden');
                    return;
                }
                students.forEach(s => {
                    const div = document.createElement('div');
                    div.className = 'p-3 hover:bg-teal-50 cursor-pointer text-sm border-b border-gray-100 text-gray-700 font-medium';
                    div.textContent = `${s.matric_id} - ${s.name}`;
                    div.onclick = () => {
                        searchInput.value = `${s.matric_id} - ${s.name}`;
                        selectedId.value = s.matric_id;
                        suggestionBox.classList.add('hidden');
                        checkBtn();
                    };
                    suggestionBox.appendChild(div);
                });
                suggestionBox.classList.remove('hidden');
            }

            searchInput.addEventListener('input', function() {
                const val = this.value.trim();
                selectedId.value = ''; 
                checkBtn();
                if (val.length < 1) { suggestionBox.classList.add('hidden'); return; }
                fetchStudents(val);
            });

            semSelect.onchange = checkBtn;
            function checkBtn() { btn.disabled = !(selectedId.value && semSelect.value); }
            document.addEventListener('click', (e) => { if (!suggestionBox.contains(e.target) && e.target !== searchInput) suggestionBox.classList.add('hidden'); });
        }
    });
</script>
@endpush