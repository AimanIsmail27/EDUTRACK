@extends('layout.administrator')

@section('title', 'View All Courses')

@section('content')

{{-- Main Container: Using a strong gradient blend from a rich indigo/blue to a deep teal. 
     The central white card will pop with maximum contrast against this dynamic background. --}}
<div class="min-h-screen p-12 bg-gradient-to-br from-indigo-200/80 to-teal-200/80">

    {{-- Content Card: The entire view is wrapped in a large, elevated card --}}
    {{-- Main card shadow is softened and elevated to pop against the strong gradient --}}
    <div class="bg-white p-12 rounded-3xl shadow-2xl shadow-gray-700/30 border border-white/80">

        <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-6 mb-10 border-b pb-8 border-gray-100">

            <div>
                <h1 class="text-4xl font-extrabold text-gray-900 tracking-tight">
                    COURSE CATALOGUE
                </h1>
                <p class="text-lg text-gray-500 mt-2">Centralized management of all system course assets.</p>
            </div>

            {{-- Add New Course Button: Primary accent with stronger shadow --}}
            <a href="{{ route('admin.courses.create') }}" class="px-8 py-3 bg-teal-600 text-white rounded-xl font-bold hover:bg-teal-700 transition duration-300 shadow-xl shadow-teal-500/40 transform hover:scale-[1.01]">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block mr-2" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" />
                </svg>
                ADD NEW COURSE
            </a>
        </div>

        <div class="mb-10">
            {{-- Filter Bar Container: Subtle glassmorphism effect, separating from the white card with shadow --}}
            <form action="{{ route('admin.viewAllCourse') }}" method="GET" class="flex flex-wrap items-center gap-4 p-5 bg-gray-50/50 rounded-xl border border-gray-200 shadow-inner">

                {{-- Filter By --}}
                <div class="flex items-center gap-2">
                    <label for="filter_by" class="text-sm font-semibold text-gray-700">Filter By:</label>
                    <select name="filter_by" id="filter_by" class="px-4 py-2 border border-gray-300 rounded-lg bg-white text-gray-800 focus:ring-teal-500 focus:border-teal-500 transition shadow-inner text-sm">
                        <option value="C_Code" {{ request('filter_by') == 'C_Code' ? 'selected' : '' }}>CODE</option>
                        <option value="C_Name" {{ request('filter_by') == 'C_Name' ? 'selected' : '' }}>NAME</option>
                        <option value="C_SemOffered" {{ request('filter_by') == 'C_SemOffered' ? 'selected' : '' }}>SEMESTER</option>
                    </select>
                </div>

                {{-- Criteria --}}
                <div class="flex items-center gap-2 flex-grow">
                    <label for="criteria" class="text-sm font-semibold text-gray-700 sr-only">Search Criteria:</label>
                    <input type="text"
                           name="criteria"
                           id="criteria"
                           value="{{ request('criteria') }}"
                           placeholder="Search by Code, Name, or Semester..."
                           class="flex-grow min-w-[200px] px-4 py-2 border border-gray-300 rounded-lg bg-white text-gray-800 placeholder-gray-400 focus:ring-teal-500 focus:border-teal-500 transition shadow-inner">
                </div>

                {{-- Search Button --}}
                <button type="submit" class="px-6 py-2 bg-teal-500 text-white rounded-lg font-bold hover:bg-teal-600 transition shadow-md text-sm">
                    SEARCH
                </button>
                
                {{-- Clear Filter Button --}}
                @if (request()->filled('criteria'))
                <a href="{{ route('admin.viewAllCourse') }}" class="px-5 py-2 bg-white text-gray-600 rounded-lg font-bold border border-gray-300 hover:bg-gray-100 transition shadow-sm text-sm">
                    CLEAR
                </a>
                @endif

            </form>
        </div>

        <div class="overflow-x-auto shadow-xl rounded-xl border border-gray-200/80">
            <table class="w-full">

                {{-- Table Header: Darker header for contrast --}}
                <thead>
                    <tr class="bg-gray-800 text-white text-xs uppercase tracking-widest">
                        <th class="px-6 py-4 text-left font-extrabold">Code</th>
                        <th class="px-6 py-4 text-left font-extrabold">Course Name</th>
                        <th class="px-6 py-4 text-left font-extrabold">Credits</th>
                        <th class="px-6 py-4 text-left font-extrabold">Semester</th>
                        <th class="px-6 py-4 text-center font-extrabold">ACTIONS</th>
                    </tr>
                </thead>

                {{-- Table Body: Clean, subtle zebra-striping, and distinct hover --}}
                <tbody class="divide-y divide-gray-100">
                    @if ($courses->isEmpty())
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-gray-500 bg-white">
                                <span class="text-xl font-semibold text-red-500">No course records found.</span>
                                @if (request()->filled('criteria'))
                                    <p class="text-md mt-2">Refine your criteria: "<strong>{{ request('criteria') }}</strong>".</p>
                                @endif
                            </td>
                        </tr>
                    @else
                        @foreach ($courses as $index => $course)
                        <tr class="{{ $index % 2 ? 'bg-white' : 'bg-gray-50' }} hover:bg-teal-50 transition duration-150">
                            {{-- Data Cells --}}
                            <td class="px-6 py-4 font-semibold text-gray-900 text-sm tracking-wide">{{ $course->C_Code }}</td>
                            <td class="px-6 py-4 text-gray-700 text-sm">{{ $course->C_Name }}</td>
                            <td class="px-6 py-4 text-gray-700 text-sm">{{ $course->C_Hour }}</td>
                            <td class="px-6 py-4 text-gray-700 text-sm">{{ $course->C_SemOffered }}</td>
                            
                            {{-- Action Buttons: Updated color palette --}}
                            <td class="px-6 py-4 text-center space-x-2">
                                {{-- View (Indigo/Action color) --}}
                                <a href="#" class="px-3 py-1 text-xs bg-indigo-500 text-white rounded-full font-bold hover:bg-indigo-600 transition shadow-sm">VIEW</a>
                                {{-- Edit (Subtle Gray/Secondary action) --}}
                                <a href="#" class="px-3 py-1 text-xs bg-gray-500 text-white rounded-full font-bold hover:bg-gray-600 transition shadow-sm">EDIT</a>
                                {{-- Delete (Danger Color) --}}
                                <button type="button" class="px-3 py-1 text-xs bg-red-500 text-white rounded-full font-bold hover:bg-red-600 transition shadow-sm">DELETE</button>
                            </td>
                        </tr>
                        @endforeach
                    @endif
                </tbody>

            </table>
        </div>

        <div class="flex justify-between items-center mt-10 text-gray-600">
            <p class="text-sm font-medium border-l-4 border-teal-500 pl-3">Record Count: {{ $courses->count() }} results</p>
            
            <div class="flex items-center gap-3 font-semibold">
                {{-- Previous Button --}}
                <button class="flex items-center gap-1.5 px-4 py-2 rounded-xl text-teal-600 border border-teal-300 bg-teal-50 hover:bg-teal-100 transition shadow-sm">
                    <span class="text-lg">←</span> Previous
                </button>

                {{-- Next Button --}}
                <button class="flex items-center gap-1.5 px-4 py-2 rounded-xl text-teal-600 border border-teal-300 bg-teal-50 hover:bg-teal-100 transition shadow-sm">
                    Next <span class="text-lg">→</span>
                </button>
            </div>
        </div>

    </div>

</div>

@endsection