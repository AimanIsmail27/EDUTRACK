@extends('layout.lecturer')

@section('title', 'Course Details: ' . $course->C_Code) 

@section('content')

@php
    $activeTab = request()->query('tab', 'overview');
    $filterSemester = request()->query('view_semester', 'all');

    $courseParticipants = $courseParticipants ?? collect(); 
    $studentGrades = $studentGrades ?? collect(); 

    $sourceCollection = ($activeTab === 'grade') ? $studentGrades : $courseParticipants;

    if ($filterSemester !== 'all') {
        $sourceCollection = $sourceCollection->filter(fn($item) => (string)$item['semester'] === (string)$filterSemester);
    }

    $perPage = 10;
    $currentPage = (int) request()->query('page', 1);
    $totalRecords = $sourceCollection->count();
    $studentsOnPage = $sourceCollection->slice(($currentPage - 1) * $perPage, $perPage)->all();

    $tabClass = function($tabName) use ($activeTab) {
        return $activeTab === $tabName 
            ? 'border-b-2 border-indigo-600 text-indigo-600 font-bold bg-indigo-50/50' 
            : 'border-b-2 border-transparent text-gray-600 hover:border-gray-300';
    };

    $offeredSemesters = explode(',', $course->C_SemOffered);
@endphp

<div class="max-w-7xl mx-auto mt-8" x-data="{ uploadModal: false, selectedWeek: 1 }"> 
    <div class="bg-white p-8 rounded-2xl shadow-xl border border-gray-100">

        {{-- Header --}}
        <div class="mb-6 pb-4 border-b border-gray-200">
            <h1 class="text-3xl font-black text-slate-800 tracking-tight">
                {{ $course->C_Code }}: {{ $course->C_Name }}
            </h1>
            <p class="text-sm text-indigo-600 font-bold uppercase tracking-widest mt-1">Lecturer Course Console</p>
        </div>

        {{-- Tabs --}}
        <div class="flex border-b border-gray-200 mb-8 overflow-x-auto">
            <a href="?tab=overview" class="px-6 py-3 transition {{ $tabClass('overview') }}">Overview</a>
            <a href="?tab=participants" class="px-6 py-3 transition {{ $tabClass('participants') }}">Students</a>
            <a href="?tab=assessment" class="px-6 py-3 transition {{ $tabClass('assessment') }}">Plan & Assessment</a>
            <a href="?tab=grade" class="px-6 py-3 transition {{ $tabClass('grade') }}">Grades</a>
        </div>

        {{-- 1. OVERVIEW TAB --}}
        @if ($activeTab === 'overview')
            <div class="space-y-8">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <div class="lg:col-span-2 space-y-6">
                        <div class="p-6 bg-indigo-50/50 rounded-2xl border border-indigo-100">
                            <h2 class="text-xl font-black text-indigo-900 mb-4">Course Synopsis</h2>
                            <p class="text-slate-600 leading-relaxed">{{ $course->C_Description ?? 'No description provided.' }}</p>
                        </div>
                        
                        <div class="grid grid-cols-3 gap-4">
                            <div class="bg-white border border-slate-100 p-4 rounded-xl text-center shadow-sm">
                                <p class="text-[10px] font-black text-slate-400 uppercase tracking-tighter">Credits</p>
                                <p class="text-xl font-black text-slate-800">{{ $course->C_Hour }}</p>
                            </div>
                            <div class="bg-white border border-slate-100 p-4 rounded-xl text-center shadow-sm">
                                <p class="text-[10px] font-black text-slate-400 uppercase tracking-tighter">Enrolled</p>
                                <p class="text-xl font-black text-slate-800">{{ $totalRecords }}</p>
                            </div>
                            <div class="bg-white border border-slate-100 p-4 rounded-xl text-center shadow-sm">
                                <p class="text-[10px] font-black text-slate-400 uppercase tracking-tighter">Status</p>
                                <p class="text-xs font-black text-green-600 uppercase mt-1">Active</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-slate-50 p-6 rounded-2xl border border-slate-100">
                        <h2 class="text-sm font-black text-slate-800 uppercase tracking-widest mb-4">Teaching Team</h2>
                        <div class="space-y-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-indigo-600 flex items-center justify-center text-white font-bold text-xs shadow-lg shadow-indigo-200">CO</div>
                                <div>
                                    <p class="text-xs font-black text-slate-800">{{ $course->coordinator->name ?? 'N/A' }}</p>
                                    <p class="text-[10px] text-indigo-600 font-bold uppercase">Coordinator</p>
                                </div>
                            </div>
                            @foreach($course->lecturers as $lec)
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-slate-200 flex items-center justify-center text-slate-600 font-bold text-xs uppercase">{{ substr($lec->name, 0, 2) }}</div>
                                <div>
                                    <p class="text-xs font-black text-slate-800">{{ $lec->name }}</p>
                                    <p class="text-[10px] text-slate-400 font-bold uppercase">Lecturer</p>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- WEEKLY PLAN --}}
                <div class="space-y-4">
                    <div class="flex items-center justify-between border-b pb-2">
                        <h2 class="text-2xl font-black text-slate-800">Weekly Learning Plan</h2>
                    </div>

                    @for ($week = 1; $week <= 14; $week++)
                        @php
                            $isBreak = ($week === 8);
                            $headerClass = $isBreak ? 'bg-rose-50 border-rose-100' : 'bg-white border-slate-200';
                            
                            // Group materials for this week by category
                            $groupedMaterials = $course->materials->where('week_number', $week)->groupBy('category');
                        @endphp
                        
                        <div x-data="{ open: false }" class="border rounded-2xl overflow-hidden transition-all duration-300 {{ $headerClass }} hover:shadow-md mb-4">
                            <div @click="open = !open" class="p-5 flex justify-between items-center cursor-pointer">
                                <div class="flex items-center gap-4">
                                    <div class="w-8 h-8 rounded-lg {{ $isBreak ? 'bg-rose-500' : 'bg-indigo-600' }} text-white flex items-center justify-center text-xs font-black">
                                        W{{ $week }}
                                    </div>
                                    <h3 class="font-bold {{ $isBreak ? 'text-rose-700' : 'text-slate-800' }}">
                                        {{ $isBreak ? 'Mid-Term Break' : 'Week ' . $week . ' Learning Content' }}
                                    </h3>
                                </div>
                                <svg class="w-5 h-5 transition-transform duration-300" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </div>

                            <div x-show="open" x-collapse class="px-5 pb-5 pt-2 border-t border-slate-50">
                                @if($isBreak)
                                    <p class="text-sm text-rose-600 italic">No academic delivery scheduled.</p>
                                @else
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                                        
                                        {{-- CATEGORIZED MATERIALS SECTION --}}
                                        <div class="md:col-span-2">
                                            @if($groupedMaterials->isEmpty())
                                                <p class="text-xs text-slate-400 italic">No materials uploaded yet.</p>
                                            @else
                                                <div class="space-y-6">
                                                    @foreach($groupedMaterials as $category => $materials)
                                                        <div>
                                                            {{-- Category Sub-header --}}
                                                            <div class="flex items-center gap-2 mb-3 border-b border-slate-100 pb-1">
                                                                <span class="text-[10px] font-black text-indigo-500 uppercase tracking-widest">{{ $category }}</span>
                                                                <span class="h-1 w-1 bg-slate-300 rounded-full"></span>
                                                                <span class="text-[10px] text-slate-400 font-bold">{{ $materials->count() }} Items</span>
                                                            </div>

                                                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                                                @foreach($materials as $mat)
                                                                    <div class="flex items-center justify-between p-3 bg-white rounded-xl border border-slate-200 hover:border-indigo-300 transition-colors shadow-sm">
                                                                        <div class="flex items-center gap-3 overflow-hidden">
                                                                            <div class="flex-shrink-0 p-2 bg-indigo-50 rounded-lg text-indigo-600">
                                                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                                                                            </div>
                                                                            <div class="truncate">
                                                                                <span class="text-xs font-bold text-slate-700 block truncate" title="{{ $mat->title }}">{{ $mat->title }}</span>
                                                                            </div>
                                                                        </div>
                                                                        
                                                                        <div class="flex items-center gap-1">
                                                                            <a href="{{ route('materials.download', $mat->id) }}" class="p-1.5 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition-colors shadow-sm">
                                                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                                                            </a>
                                                                            <button type="button" onclick="confirmDelete({{ $mat->id }}, '{{ $mat->title }}')" class="p-1.5 bg-rose-600 text-white rounded-md hover:bg-rose-700 transition-colors shadow-sm">
                                                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                                            </button>
                                                                            <form id="delete-form-{{ $mat->id }}" action="{{ route('materials.destroy', $mat->id) }}" method="POST" class="hidden">@csrf @method('DELETE')</form>
                                                                        </div>
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>

                                        {{-- UPLOAD AREA --}}
                                        <div class="flex flex-col gap-4">
                                            <div @click="uploadModal = true; selectedWeek = {{ $week }}" class="cursor-pointer bg-indigo-50/50 border-2 border-dashed border-indigo-200 rounded-2xl p-6 flex flex-col items-center justify-center text-center hover:bg-indigo-50 hover:border-indigo-400 transition-all group h-full">
                                                <div class="w-12 h-12 bg-indigo-600 rounded-full flex items-center justify-center text-white mb-3 shadow-lg group-hover:scale-110 transition-transform">
                                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                                </div>
                                                <p class="text-xs font-black text-indigo-900 uppercase tracking-widest">Add New Content</p>
                                                <p class="text-[10px] text-slate-400 mt-1 font-bold">Week {{ $week }}</p>
                                            </div>
                                        </div>

                                    </div>
                                @endif
                            </div>
                        </div>
                    @endfor
                </div>
            </div>

        {{-- 2. PARTICIPANTS TAB --}}
        @elseif ($activeTab === 'participants')
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-xl font-black text-slate-800">Class List</h2>
                <div class="flex gap-2">
                     @foreach ($offeredSemesters as $sem)
                        <a href="?tab=participants&view_semester={{ $sem }}" class="px-3 py-1.5 text-xs rounded-lg {{ (string)$filterSemester === (string)$sem ? 'bg-indigo-600 text-white' : 'bg-slate-100 text-slate-600 font-bold' }}">Sem {{ $sem }}</a>
                    @endforeach
                </div>
            </div>
            <div class="overflow-hidden rounded-xl border border-slate-100">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-slate-50 border-b border-slate-100">
                        <tr>
                            <th class="p-4 text-[10px] font-black uppercase text-slate-400">Student Name</th>
                            <th class="p-4 text-[10px] font-black uppercase text-slate-400">Matric ID</th>
                            <th class="p-4 text-[10px] font-black uppercase text-slate-400">Enrollment</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @forelse($studentsOnPage as $s)
                        <tr class="hover:bg-indigo-50/30 transition-colors">
                            <td class="p-4 text-sm font-bold text-slate-700">{{ $s['full_name'] }}</td>
                            <td class="p-4 text-sm font-mono text-indigo-600">{{ $s['matric_id'] }}</td>
                            <td class="p-4 text-xs font-bold text-slate-500">Semester {{ $s['semester'] }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="3" class="p-8 text-center text-slate-400 italic text-sm">No students found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        {{-- 3. GRADE TAB --}}
        @elseif ($activeTab === 'grade')
            <div class="overflow-x-auto rounded-xl border border-slate-100 shadow-sm">
                <table class="min-w-full divide-y divide-slate-100 text-sm">
                    <thead class="bg-slate-50 font-black uppercase text-slate-400 text-[10px]">
                        <tr>
                            <th class="px-6 py-4 text-left">Student</th>
                            <th class="px-6 py-4 text-center">Quiz 1 (10%)</th>
                            <th class="px-6 py-4 text-center">Quiz 2 (10%)</th>
                            <th class="px-6 py-4 text-center">IA (30%)</th>
                            <th class="px-6 py-4 text-center">GP (50%)</th>
                            <th class="px-6 py-4 text-center bg-indigo-50">Total</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-slate-50">
                        @foreach ($studentsOnPage as $student)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-6 py-4">
                                <p class="font-bold text-slate-800">{{ $student['full_name'] }}</p>
                                <p class="text-[10px] font-mono text-indigo-600 uppercase">{{ $student['matric_id'] }}</p>
                            </td>
                            <td class="px-6 py-4 text-center text-slate-600">{{ $student['quiz1'] ?? 0 }}</td>
                            <td class="px-6 py-4 text-center text-slate-600">{{ $student['quiz2'] ?? 0 }}</td>
                            <td class="px-6 py-4 text-center text-slate-600">{{ $student['ia'] ?? 0 }}</td>
                            <td class="px-6 py-4 text-center text-slate-600">{{ $student['gp'] ?? 0 }}</td>
                            <td class="px-6 py-4 text-center font-black text-indigo-600 bg-indigo-50/30">{{ $student['total'] ?? 0 }}%</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    {{-- UPLOAD MODAL --}}
    <div x-show="uploadModal" class="fixed inset-0 z-[99] flex items-center justify-center p-6 bg-slate-900/60 backdrop-blur-sm" x-cloak>
        <div class="bg-white w-full max-w-md rounded-[2rem] shadow-2xl overflow-hidden" @click.away="uploadModal = false">
            <div class="p-8">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h2 class="text-2xl font-black text-slate-800">Upload Material</h2>
                        <p class="text-xs font-bold text-indigo-600 uppercase tracking-widest">Week <span x-text="selectedWeek"></span></p>
                    </div>
                    <button @click="uploadModal = false" class="text-slate-400 hover:text-slate-600 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <form action="{{ route('materials.store') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
                    @csrf
                    <input type="hidden" name="course_code" value="{{ $course->C_Code }}">
                    <input type="hidden" name="week_number" :value="selectedWeek">

                    <div>
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-2">Material Title</label>
                        <input type="text" name="title" required placeholder="e.g. Introduction to PHP" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 text-sm font-medium">
                    </div>

                    <div>
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-2">Category</label>
                        <select name="category" required class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 text-sm font-medium">
                            <option value="Notes">Lecture Notes</option>
                            <option value="Lab Sheet">Lab Sheet</option>
                            <option value="Slides">Presentation Slides</option>
                            <option value="Reference">Reference Material</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>

                    <div>
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-2">Select File</label>
                        <input type="file" name="file" required class="w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-[10px] file:font-black file:uppercase file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 transition-all cursor-pointer">
                    </div>

                    <div class="pt-4">
                        <button type="submit" class="w-full py-4 bg-indigo-600 text-white rounded-2xl font-black text-sm shadow-xl shadow-indigo-200 hover:bg-indigo-700 transition-all transform active:scale-95">
                            UPLOAD NOW
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function confirmDelete(id, title) {
        Swal.fire({
            title: 'Delete Material?',
            text: `Are you sure you want to remove "${title}"?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#e11d48',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'Cancel',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById(`delete-form-${id}`).submit();
            } else if (result.dismiss === Swal.DismissReason.cancel) {
                Swal.fire({ title: 'Aborted', text: 'Your file is safe.', icon: 'info', confirmButtonColor: '#4f46e5' });
            }
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        @if (session('success'))
            Swal.fire({ icon: 'success', title: 'Success!', text: "{{ session('success') }}", confirmButtonColor: '#4f46e5' });
        @endif
        @if (session('error'))
            Swal.fire({ icon: 'error', title: 'Operation Failed', text: "{{ session('error') }}", confirmButtonColor: '#4f46e5' });
        @endif
    });
</script>
@endpush

@endsection