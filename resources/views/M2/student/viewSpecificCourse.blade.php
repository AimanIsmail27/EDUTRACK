@extends('layout.student')

@section('title', 'Course Materials: ' . $course->C_Code) 

@section('content')

@php
    $activeTab = request()->query('tab', 'overview');

    // 1. Get current student's Matric ID via relationship
    $currentUserMatric = auth()->user()->student->MatricID ?? null;

    // 2. Filter grades to ONLY show this student's data for privacy
    $myGrade = $studentGrades->firstWhere('matric_id', $currentUserMatric);

    // 3. Tab styling logic
    $tabClass = function($tabName) use ($activeTab) {
        return $activeTab === $tabName 
            ? 'border-b-2 border-emerald-600 text-emerald-600 font-bold bg-emerald-50/50' 
            : 'border-b-2 border-transparent text-gray-600 hover:border-emerald-300';
    };
@endphp

<div class="max-w-7xl mx-auto mt-8"> 
    <div class="bg-white p-8 rounded-2xl shadow-xl border border-emerald-100/50">

        {{-- Course Header --}}
        <div class="mb-6 pb-4 border-b border-gray-200">
            <h1 class="text-3xl font-black text-emerald-950 tracking-tight">
                {{ $course->C_Code }}: {{ $course->C_Name }}
            </h1>
            <p class="text-sm text-emerald-600 font-bold uppercase tracking-widest mt-1">Student Learning Portal</p>
        </div>

        {{-- Tabs Navigation --}}
        <div class="flex border-b border-gray-200 mb-8 overflow-x-auto">
            <a href="?tab=overview" class="px-6 py-3 transition {{ $tabClass('overview') }}">Materials</a>
            <a href="?tab=participants" class="px-6 py-3 transition {{ $tabClass('participants') }}">Classmates</a>
            <a href="?tab=grade" class="px-6 py-3 transition {{ $tabClass('grade') }}">My Grades</a>
            <a href="?tab=assessment" class="px-6 py-3 transition {{ $tabClass('assessment') }}">Weightage Info</a>
        </div>

        {{-- 1. OVERVIEW & MATERIALS TAB --}}
        @if ($activeTab === 'overview')
            <div class="space-y-8">
                <div class="p-6 bg-emerald-50/50 rounded-xl border border-emerald-200 shadow-sm">
                    <h2 class="text-xl font-black text-emerald-800 border-b pb-2 border-emerald-200 mb-4">Course Synopsis</h2>
                    <p class="text-slate-600 leading-relaxed mb-4">{{ $course->C_Description ?? 'No description provided.' }}</p>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-gray-800">
                        <div class="flex items-center space-x-2 bg-white p-3 rounded-lg border border-emerald-100 shadow-sm">
                            <span class="text-xl">📚</span>
                            <p class="text-xs"><span class="font-bold">Credits:</span> {{ $course->C_Hour }}</p>
                        </div>
                        <div class="flex items-center space-x-2 bg-white p-3 rounded-lg border border-emerald-100 shadow-sm">
                            <span class="text-xl">👨‍🏫</span>
                            <p class="text-xs"><span class="font-bold">Coordinator:</span> {{ $course->coordinator->name ?? 'TBA' }}</p>
                        </div>
                        <div class="flex items-center space-x-2 bg-white p-3 rounded-lg border border-emerald-100 shadow-sm">
                            <span class="text-xl">🗓️</span>
                            <p class="text-xs"><span class="font-bold">Offered:</span> Sem {{ $course->C_SemOffered }}</p>
                        </div>
                    </div>
                </div>

                {{-- WEEKLY CONTENT - VERTICAL CATEGORY STACK --}}
                <div class="space-y-4">
                    <h2 class="text-2xl font-black text-emerald-950">Learning Materials</h2>

                    @for ($week = 1; $week <= 14; $week++)
                        @php
                            $isBreak = ($week === 8);
                            $headerClass = $isBreak ? 'bg-rose-50 border-rose-100' : 'bg-white border-gray-200';
                            $weekMaterials = $course->materials->where('week_number', $week)->groupBy('category');
                        @endphp
                        
                        <div x-data="{ open: {{ $week <= 1 ? 'true' : 'false' }} }" class="border rounded-2xl overflow-hidden transition-all duration-300 {{ $headerClass }} hover:shadow-md mb-2">
                            <div @click="open = !open" class="p-5 flex justify-between items-center cursor-pointer hover:bg-emerald-50/10">
                                <div class="flex items-center gap-4">
                                    <div class="w-8 h-8 rounded-lg {{ $isBreak ? 'bg-rose-500' : 'bg-emerald-600' }} text-white flex items-center justify-center text-xs font-black shadow-sm">
                                        W{{ $week }}
                                    </div>
                                    <h3 class="font-bold text-lg {{ $isBreak ? 'text-rose-700' : 'text-emerald-900' }}">
                                        {{ $isBreak ? 'Mid-Term Break' : 'Week ' . $week . ' Materials' }}
                                    </h3>
                                </div>
                                <svg class="w-5 h-5 transition-transform duration-300" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </div>

                            <div x-show="open" x-collapse class="px-5 pb-5 pt-2 border-t border-emerald-50 bg-emerald-50/5">
                                @if($isBreak)
                                    <p class="text-sm text-rose-600 italic">Rest well and catch up on your readings!</p>
                                @elseif($weekMaterials->isEmpty())
                                    <p class="text-sm text-slate-400 italic text-center py-4">Materials will be uploaded by the lecturer soon.</p>
                                @else
                                    <div class="space-y-6 mt-2">
                                        @foreach($weekMaterials as $category => $materials)
                                            <div class="bg-white p-5 rounded-xl border border-emerald-100 shadow-sm">
                                                <div class="flex items-center gap-2 mb-4 border-b border-emerald-50 pb-2">
                                                    <span class="text-xs font-black text-emerald-600 uppercase tracking-widest">{{ $category }}</span>
                                                </div>
                                                
                                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                                    @foreach($materials as $mat)
                                                        <div class="flex items-center justify-between p-3 bg-emerald-50/30 rounded-lg border border-emerald-100 group">
                                                            <div class="flex items-center gap-3 overflow-hidden">
                                                                <div class="p-2 bg-white rounded-md shadow-sm text-emerald-500">
                                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                                                                </div>
                                                                <span class="text-xs font-bold text-gray-700 truncate" title="{{ $mat->title }}">{{ $mat->title }}</span>
                                                            </div>
                                                            <a href="{{ route('student.materials.download', $mat->id) }}" class="ml-4 p-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 shadow-md transition-transform active:scale-90" title="Download">
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
            
        {{-- 2. PARTICIPANTS TAB (VIEW CLASSMATES) --}}
        @elseif ($activeTab === 'participants')
            <div x-data="{ searchClass: '' }">
                <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
                    <h2 class="text-2xl font-black text-emerald-950 tracking-tight">Your Classmates</h2>
                    <div class="relative w-full md:w-64">
                        <input type="text" x-model="searchClass" placeholder="Find a classmate..." class="w-full text-sm p-2 pl-8 border border-emerald-200 rounded-lg focus:ring-emerald-500 shadow-sm">
                        <svg class="w-4 h-4 absolute left-2.5 top-3 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    @forelse ($courseParticipants as $participant)
                        <div x-show="searchClass === '' || '{{ strtolower($participant['full_name']) }}'.includes(searchClass.toLowerCase())" 
                             class="p-4 bg-white border border-emerald-100 rounded-2xl shadow-sm flex items-center gap-4 transition-all hover:border-emerald-300">
                            <div class="w-10 h-10 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-700 font-bold text-xs">
                                {{ substr($participant['full_name'], 0, 1) }}
                            </div>
                            <div>
                                <p class="text-sm font-bold text-slate-800 leading-none">{{ $participant['full_name'] }}</p>
                                <p class="text-[10px] font-medium text-slate-400 mt-1 uppercase">{{ $participant['matric_id'] }}</p>
                            </div>
                        </div>
                    @empty
                        <p class="col-span-full text-center text-slate-400 italic py-8">No records found.</p>
                    @endforelse
                </div>
            </div>

        {{-- 3. GRADE TAB (PRIVATE PERFORMANCE CARD) --}}
        @elseif ($activeTab === 'grade')
            <div class="max-w-2xl mx-auto">
                <h2 class="text-2xl font-black text-emerald-950 mb-6">Your Academic Performance</h2>
                
                @if($myGrade)
                    <div class="bg-white border-2 border-emerald-100 rounded-[2.5rem] overflow-hidden shadow-2xl shadow-emerald-900/5">
                        {{-- Score Summary Header --}}
                        <div class="bg-emerald-600 p-8 text-center text-white">
                            <p class="text-[10px] font-black uppercase tracking-[0.2em] opacity-80 mb-2">Overall Course Progress</p>
                            <h3 class="text-6xl font-black">{{ $myGrade['total'] }}<span class="text-2xl">%</span></h3>
                        </div>

                        {{-- Individual Breakdown --}}
                        <div class="p-8 space-y-4">
                            @php
                                $items = [
                                    ['label' => 'Quiz 1', 'weight' => '10%', 'score' => $myGrade['quiz1']],
                                    ['label' => 'Quiz 2', 'weight' => '10%', 'score' => $myGrade['quiz2']],
                                    ['label' => 'Individual Assignment', 'weight' => '30%', 'score' => $myGrade['ia']],
                                    ['label' => 'Group Project', 'weight' => '50%', 'score' => $myGrade['gp']],
                                ];
                            @endphp

                            @foreach($items as $item)
                            <div class="flex items-center justify-between p-4 bg-emerald-50/50 rounded-2xl border border-emerald-100/50">
                                <div>
                                    <p class="text-sm font-bold text-slate-800">{{ $item['label'] }}</p>
                                    <p class="text-[10px] font-black text-emerald-500 uppercase tracking-widest">Assessment Weight: {{ $item['weight'] }}</p>
                                </div>
                                <div class="text-right">
                                    <span class="text-lg font-black text-slate-700">{{ $item['score'] }}%</span>
                                </div>
                            </div>
                            @endforeach
                        </div>

                        {{-- Final Verdict Status --}}
                        <div class="px-8 pb-8">
                            <div class="p-4 rounded-2xl text-center {{ $myGrade['total'] >= 50 ? 'bg-green-100 text-green-700' : 'bg-rose-100 text-rose-700' }}">
                                <p class="text-xs font-black uppercase tracking-widest">
                                    Current Status: {{ $myGrade['total'] >= 50 ? 'Passing' : 'Below 50%' }}
                                </p>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="p-12 text-center bg-slate-50 rounded-[2rem] border-2 border-dashed border-slate-200">
                        <svg class="w-12 h-12 text-slate-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        <p class="text-slate-400 font-bold text-sm italic">Marks for this course haven't been released yet.</p>
                    </div>
                @endif
            </div>

        {{-- 4. ASSESSMENT INFO TAB --}}
        @elseif ($activeTab === 'assessment')
            <h2 class="text-2xl font-black text-emerald-950 mb-6">Course Assessment Scheme</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach (['Quiz 1' => '10%', 'Quiz 2' => '10%', 'Individual Assignment' => '30%', 'Group Project' => '50%'] as $name => $weight)
                    <div class="flex items-center justify-between p-5 bg-white rounded-2xl border border-emerald-100 shadow-sm">
                        <div>
                            <p class="font-bold text-slate-800">{{ $name }}</p>
                            <p class="text-[10px] text-emerald-500 font-bold uppercase tracking-widest mt-1">Weightage</p>
                        </div>
                        <span class="text-2xl font-black text-emerald-600">{{ $weight }}</span>
                    </div>
                @endforeach
            </div>
            <div class="mt-8 p-6 bg-slate-50 rounded-2xl border border-slate-200">
                <h3 class="text-xs font-black uppercase text-slate-400 mb-2">Note to Student</h3>
                <p class="text-xs text-slate-500 leading-relaxed">The marks displayed in the 'My Grades' tab are cumulative. Please contact your course coordinator if there are discrepancies in your awarded marks.</p>
            </div>
        @endif
    </div>
</div>
@endsection