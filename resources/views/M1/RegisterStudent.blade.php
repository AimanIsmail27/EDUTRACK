@extends('layout.administrator')

@section('title', 'Register Student')

@section('content')

{{-- Main Content Area: Centering Container --}}
<div class="max-w-7xl mx-auto mt-8 p-4 md:p-8">

    {{-- Main Content Card: Student List and Controls --}}
    <div class="bg-white p-8 rounded-2xl shadow-xl shadow-gray-400/30 border border-gray-100/80">
        
        {{-- Header row: title + controls --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-6 pb-4 border-b border-gray-200">
            <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight mb-4 sm:mb-0">
                Manage Student Registration
            </h1>

            <div class="flex items-center gap-3">
                {{-- CSV Input/Upload Group --}}
                <div class="flex items-center space-x-2 p-1.5 bg-gray-50 border border-gray-300 rounded-lg shadow-inner">
                    <input type="text"
                           value="Document.csv"
                           readonly
                           class="px-2 py-1 border-none bg-transparent w-32 text-sm text-gray-700 cursor-default">
                    
                    {{-- Icon (upload) --}}
                    <button class="text-gray-600 hover:text-teal-600 transition p-1 cursor-pointer">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                        </svg>
                    </button>
                </div>

                {{-- Add button (Triggers Modal) --}}
                <button type="button" 
                        class="open-create-student-modal px-6 py-2 bg-teal-600 text-white rounded-lg font-bold hover:bg-teal-700 transition shadow-md text-sm">
                    Add
                </button>
            </div>
        </div>

        {{-- Student Table --}}
        <div class="overflow-x-auto shadow-xl rounded-xl border border-gray-200/80">
            <table class="w-full">
                <thead>
                    <tr class="bg-gray-100 text-gray-600 text-xs uppercase tracking-widest">
                        <th class="px-6 py-3 text-left font-extrabold">Matric ID</th>
                        <th class="px-6 py-3 text-left font-extrabold">Name</th>
                        <th class="px-6 py-3 text-left font-extrabold">Course</th>
                        <th class="px-6 py-3 text-left font-extrabold">Year</th>
                        <th class="px-6 py-3 text-center font-extrabold">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    {{-- DUMMY DATA FOR DEMONSTRATION --}}
                    @for ($i = 1; $i <= 5; $i++)
                        <tr class="{{ $i % 2 ? 'bg-white' : 'bg-gray-50' }} hover:bg-teal-50 transition duration-150">
                            <td class="px-6 py-4 font-semibold text-gray-900 text-sm">CB2200{{ $i }}</td>
                            <td class="px-6 py-4 text-gray-700 text-sm">Student Name {{ $i }}</td>
                            <td class="px-6 py-4 text-gray-700 text-sm">Course {{ 100 + $i }}</td>
                            <td class="px-6 py-4 text-gray-700 text-sm">202{{ $i }}-202{{ $i + 1 }}</td>
                            <td class="px-6 py-4 text-center space-x-2">
                                {{-- Edit Button (Triggers Modal) --}}
                                <button type="button"
                                        class="open-edit-student-modal px-3 py-1 text-xs bg-indigo-500 text-white rounded-full font-bold hover:bg-indigo-600 transition shadow-sm"
                                        data-matric="CB2200{{ $i }}" data-name="Student Name {{ $i }}" 
                                        data-course="Course {{ 100 + $i }}" data-year="202{{ $i }}-202{{ $i + 1 }}">
                                    Edit
                                </button>
                                {{-- Delete Button (Triggers Modal) --}}
                                <button type="button"
                                        class="open-delete-student-modal px-3 py-1 text-xs bg-red-500 text-white rounded-full font-bold hover:bg-red-600 transition shadow-sm"
                                        data-matric="CB2200{{ $i }}">
                                    Delete
                                </button>
                            </td>
                        </tr>
                    @endfor
                    {{-- END DUMMY DATA --}}
                </tbody>
            </table>
        </div>
        
    </div>
</div>

{{-- ------------------------------------------------ --}}
{{-- MODALS (Hidden by default)                       --}}
{{-- ------------------------------------------------ --}}

{{-- Create Student Modal Overlay --}}
<div id="createStudentOverlay"
     class="hidden fixed inset-0 bg-black bg-opacity-25 z-[1000] items-center justify-center">
    <div class="bg-white w-[520px] max-w-[90%] rounded-xl p-8 shadow-2xl">
        <h2 class="text-center mb-6 text-2xl font-bold text-gray-800">
            Add New Student
        </h2>

        {{-- Route action placeholder used for simplicity. You should define 'register.student.store' --}}
        <form method="POST" action="{{ route('register.student.store') ?? '#' }}" class="space-y-4">
            @csrf

            {{-- Matric ID --}}
            <div class="flex items-center">
                <label class="w-32 text-sm text-gray-600">Matric ID</label>
                <input type="text" name="matric_id" required
                       class="flex-1 px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-teal-500 focus:border-teal-500">
            </div>

            {{-- Name --}}
            <div class="flex items-center">
                <label class="w-32 text-sm text-gray-600">Name</label>
                <input type="text" name="name" required
                       class="flex-1 px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-teal-500 focus:border-teal-500">
            </div>

            {{-- Email --}}
            <div class="flex items-center">
                <label class="w-32 text-sm text-gray-600">Email</label>
                <input type="email" name="email" required
                       class="flex-1 px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-teal-500 focus:border-teal-500">
            </div>

            {{-- Course --}}
            <div class="flex items-center">
                <label class="w-32 text-sm text-gray-600">Course</label>
                <select name="course" required
                        class="flex-1 px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-teal-500 focus:border-teal-500">
                    <option value="">Select Course</option>
                    <option value="Software Engineering">Software Engineering</option>
                    <option value="Network">Network</option>
                    <option value="Cybersecurity">Cybersecurity</option>
                </select>
            </div>

            {{-- Year --}}
            <div class="flex items-center">
                <label class="w-32 text-sm text-gray-600">Year</label>
                <input type="text" name="year" required
                       class="flex-1 px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-teal-500 focus:border-teal-500">
            </div>

            {{-- Password --}}
            <div class="flex items-center">
                <label class="w-32 text-sm text-gray-600">Password</label>
                <input type="password" name="password" required
                       class="flex-1 px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-teal-500 focus:border-teal-500">
            </div>

            {{-- Password Confirmation --}}
            <div class="flex items-center mb-6">
                <label class="w-32 text-sm text-gray-600">Confirm Password</label>
                <input type="password" name="password_confirmation" required
                       class="flex-1 px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-teal-500 focus:border-teal-500">
            </div>

            {{-- Buttons --}}
            <div class="pt-4 text-center space-y-3">
                <button type="submit"
                        class="bg-indigo-600 text-white rounded-full px-10 py-2.5 font-semibold text-sm hover:bg-indigo-700 transition shadow-lg">
                    Add Student
                </button>
                <button type="button" class="close-create-student-modal bg-gray-200 text-gray-700 rounded-full px-8 py-2 font-medium text-sm hover:bg-gray-300 transition">
                    Cancel
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Edit Student Modal Overlay --}}
<div id="editStudentOverlay"
     class="hidden fixed inset-0 bg-black bg-opacity-25 z-[1000] items-center justify-center">
    <div class="bg-white w-[520px] max-w-[90%] rounded-xl p-8 shadow-2xl">
        <h2 class="text-center mb-6 text-2xl font-bold text-gray-800">
            Edit Student Information
        </h2>

        {{-- URL GENERATION FIX: action is set dynamically by JS when the modal opens --}}
        <form id="editStudentForm" action="/update-student-placeholder" method="POST" class="space-y-4">
            @csrf
            @method('PUT')

            {{-- Matric ID (Readonly) --}}
            <div class="flex items-center">
                <label class="w-32 text-sm text-gray-600">Matric ID</label>
                <input type="text" name="matric_id" id="editMatricId" readonly
                       class="flex-1 px-3 py-2 border border-gray-300 rounded-lg text-sm bg-gray-100 cursor-not-allowed">
            </div>

            {{-- Name --}}
            <div class="flex items-center">
                <label class="w-32 text-sm text-gray-600">Name</label>
                <input type="text" name="name" id="editName" required
                       class="flex-1 px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-teal-500 focus:border-teal-500">
            </div>

            {{-- Course --}}
            <div class="flex items-center">
                <label class="w-32 text-sm text-gray-600">Course</label>
                <select name="course" id="editCourse" required
                        class="flex-1 px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-teal-500 focus:border-teal-500">
                    <option value="Software Engineering">Software Engineering</option>
                    <option value="Network">Network</option>
                    <option value="Cybersecurity">Cybersecurity</option>
                </select>
            </div>

            {{-- Year --}}
            <div class="flex items-center mb-6">
                <label class="w-32 text-sm text-gray-600">Year</label>
                <input type="text" name="year" id="editYear" required
                       class="flex-1 px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-teal-500 focus:border-teal-500">
            </div>

            {{-- Buttons --}}
            <div class="pt-4 text-center space-y-3">
                <button type="submit"
                        class="bg-indigo-600 text-white rounded-full px-10 py-2.5 font-semibold text-sm hover:bg-indigo-700 transition shadow-lg">
                    Update Information
                </button>
                <button type="button" class="close-edit-student-modal bg-gray-200 text-gray-700 rounded-full px-8 py-2 font-medium text-sm hover:bg-gray-300 transition">
                    Cancel
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Delete Student Confirmation Modal Overlay --}}
<div id="deleteStudentOverlay"
     class="hidden fixed inset-0 bg-black bg-opacity-25 z-[1000] items-center justify-center">
    <div class="bg-white w-[450px] max-w-[90%] rounded-xl shadow-2xl overflow-hidden">
        {{-- Title Bar --}}
        <div class="bg-gray-50/50 p-4 border-b border-gray-200 flex items-center justify-between">
            <h2 id="deleteModalTitle" class="font-bold text-lg text-gray-800">
                Delete User (CB22174)
            </h2>
            <button class="close-delete-student-modal text-xl text-gray-500 hover:text-gray-700 transition">
                &times;
            </button>
        </div>

        {{-- Modal Body --}}
        <div class="p-6">
            <p class="mb-6 text-sm text-gray-700 text-center">
                This action cannot be undone once confirmed. Do you still want to proceed?
            </p>

            {{-- Buttons --}}
            <div class="flex justify-center gap-4">
                <button type="button" id="confirmDeleteStudent"
                        class="bg-red-600 text-white rounded-lg px-8 py-2 font-semibold text-sm hover:bg-red-700 transition shadow-md">
                    Delete
                </button>
                <button type="button" class="close-delete-student-modal bg-gray-200 text-gray-700 rounded-lg px-6 py-2 font-semibold text-sm hover:bg-gray-300 transition">
                    Cancel
                </button>
            </div>
        </div>
    </div>
</div>

{{-- ------------------------------------------------ --}}
{{-- JAVASCRIPT FOR MODALS (Adjusted for Tailwind Classes) --}}
{{-- ------------------------------------------------ --}}

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // --- Utility Functions for Modals ---
        const getOverlay = (id) => document.getElementById(id);
        const openModal = (overlay) => overlay.classList.replace('hidden', 'flex');
        const closeModal = (overlay) => overlay.classList.replace('flex', 'hidden');

        // --- Create Modal Logic ---
        const createOverlay = getOverlay('createStudentOverlay');
        const openCreateButtons = document.querySelectorAll('.open-create-student-modal');
        const closeCreateButtons = document.querySelectorAll('.close-create-student-modal');

        openCreateButtons.forEach(btn => btn.addEventListener('click', (e) => {
            e.preventDefault();
            if (createOverlay) openModal(createOverlay);
        }));
        closeCreateButtons.forEach(btn => btn.addEventListener('click', (e) => {
            e.preventDefault();
            if (createOverlay) closeModal(createOverlay);
        }));
        if (createOverlay) createOverlay.addEventListener('click', (e) => {
            if (e.target === createOverlay) closeModal(createOverlay);
        });

        // --- Edit Modal Logic ---
        const editOverlay = getOverlay('editStudentOverlay');
        const editStudentForm = document.getElementById('editStudentForm'); // Get the form element
        const openEditButtons = document.querySelectorAll('.open-edit-student-modal');
        const closeEditButtons = document.querySelectorAll('.close-edit-student-modal');
        const inputMatric = document.getElementById('editMatricId');
        const inputName = document.getElementById('editName');
        const selectCourse = document.getElementById('editCourse');
        const inputYear = document.getElementById('editYear');
        
        // Base path for the update route (adjust this to your actual route structure)
        const updateRouteBase = '/administrator/register-student/'; 

        openEditButtons.forEach(btn => btn.addEventListener('click', (e) => {
            e.preventDefault();
            // Grab the Matric ID from the clicked button
            const matricId = btn.dataset.matric; 

            // 1. Populate form fields
            if (inputMatric && matricId) inputMatric.value = matricId;
            if (inputName && btn.dataset.name) inputName.value = btn.dataset.name;
            if (selectCourse && btn.dataset.course) selectCourse.value = btn.dataset.course;
            if (inputYear && btn.dataset.year) inputYear.value = btn.dataset.year;
            
            // 2. DYNAMICALLY SET THE FORM ACTION URL (Fixes the UrlGenerationException)
            if (editStudentForm && matricId) {
                 // Example: Sets action to /administrator/register-student/CB22001
                 editStudentForm.action = updateRouteBase + matricId;
            }

            if (editOverlay) openModal(editOverlay);
        }));

        closeEditButtons.forEach(btn => btn.addEventListener('click', (e) => {
            e.preventDefault();
            if (editOverlay) closeModal(editOverlay);
        }));
        if (editOverlay) editOverlay.addEventListener('click', (e) => {
            if (e.target === editOverlay) closeModal(editOverlay);
        });

        // --- Delete Modal Logic ---
        const deleteOverlay = getOverlay('deleteStudentOverlay');
        const openDeleteButtons = document.querySelectorAll('.open-delete-student-modal');
        const closeDeleteButtons = document.querySelectorAll('.close-delete-student-modal');
        const deleteTitle = document.getElementById('deleteModalTitle');
        const confirmDeleteBtn = document.getElementById('confirmDeleteStudent');
        let currentMatricId = '';

        openDeleteButtons.forEach(btn => btn.addEventListener('click', (e) => {
            e.preventDefault();
            const matricId = btn.dataset.matric || 'CB22174';
            currentMatricId = matricId;
            if (deleteTitle) deleteTitle.textContent = 'Delete User (' + matricId + ')';
            
            if (deleteOverlay) openModal(deleteOverlay);
        }));

        closeDeleteButtons.forEach(btn => btn.addEventListener('click', (e) => {
            e.preventDefault();
            if (deleteOverlay) closeModal(deleteOverlay);
        }));
        
        if (confirmDeleteBtn) {
            confirmDeleteBtn.addEventListener('click', (e) => {
                e.preventDefault();
                // TODO: Add actual delete functionality here (e.g., submit a form via AJAX)
                alert('Delete functionality for ' + currentMatricId + ' - Implement your delete logic here');
                closeModal(deleteOverlay);
            });
        }
        
        if (deleteOverlay) deleteOverlay.addEventListener('click', (e) => {
            if (e.target === deleteOverlay) closeModal(deleteOverlay);
        });
    });
</script>
@endpush