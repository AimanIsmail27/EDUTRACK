@extends('layout.administrator')

@section('title', 'Register Student')

@section('content')

<div class="min-h-screen p-12 bg-gradient-to-br from-indigo-200/80 to-teal-200/80">

    <div class="bg-white p-8 rounded-2xl shadow-xl shadow-gray-400/30 border border-gray-100/80">
        
        <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-6 pb-4 border-b border-gray-200">
            <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight mb-4 sm:mb-0">
                Student Registration
            </h1>

            <div class="flex flex-col sm:flex-row sm:items-center gap-3">
                <form method="GET" action="{{ route('register.student') }}" class="flex items-center gap-2">
                    <input type="text"
                           name="search"
                           value="{{ request('search') }}"
                           placeholder="Search Matric ID"
                           class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-teal-500 focus:border-teal-500 w-48">
                    @if(request('search'))
                        <a href="{{ route('register.student') }}"
                           class="px-3 py-2 text-sm text-gray-700 bg-gray-100 border border-gray-300 rounded-lg hover:bg-gray-200 transition">
                            Clear
                        </a>
                    @endif
                    <button type="submit"
                            class="px-4 py-2 bg-teal-600 text-white rounded-lg text-sm font-semibold hover:bg-teal-700 transition shadow-md">
                        Search
                    </button>
                </form>

                <div class="flex items-center space-x-2 p-1.5 bg-gray-50 border border-gray-300 rounded-lg shadow-inner" style="position: relative;">
                    <input type="text"
                           id="csvFileName"
                           value="No file chosen"
                           readonly
                           class="px-2 py-1 border-none bg-transparent w-32 text-sm text-gray-700 pointer-events-none">

                    <input type="file"
                           id="csvFileInput"
                           accept=".csv"
                           style="display: none;">

                    <button type="button"
                            id="csvUploadButton"
                            class="text-gray-600 hover:text-teal-600 transition p-1 cursor-pointer"
                            style="pointer-events: auto;">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                        </svg>
                    </button>
                </div>

                <button type="button"
                        id="uploadCsvButton"
                        disabled
                        class="px-6 py-2 bg-gray-400 text-white rounded-lg font-bold cursor-not-allowed transition shadow-md text-sm">
                    Upload
                </button>
            </div>
        </div>

        <div class="overflow-x-auto shadow-xl rounded-xl border border-gray-200/80">
            <table class="w-full">
                <thead>
                    <tr class="bg-gray-100 text-gray-600 text-xs uppercase tracking-widest">
                        <th class="px-6 py-3 text-left font-extrabold">Matric ID</th>
                        <th class="px-6 py-3 text-left font-extrabold">Name</th>
                        <th class="px-6 py-3 text-left font-extrabold">Email</th>
                        <th class="px-6 py-3 text-left font-extrabold">Course</th>
                        <th class="px-6 py-3 text-left font-extrabold">Year</th>
                        <th class="px-6 py-3 text-center font-extrabold">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @if(isset($students) && $students->count() > 0)
                        @foreach($students as $index => $student)
                        <tr class="{{ $index % 2 ? 'bg-white' : 'bg-gray-50' }} hover:bg-teal-50 transition duration-150">
                            <td class="px-6 py-4 font-semibold text-gray-900 text-sm">{{ $student->matric_id ?? 'N/A' }}</td>
                            <td class="px-6 py-4 text-gray-700 text-sm">{{ $student->name }}</td>
                            <td class="px-6 py-4 text-gray-700 text-sm">{{ $student->email ?? 'N/A' }}</td>
                            <td class="px-6 py-4 text-gray-700 text-sm">{{ $student->course ?? 'N/A' }}</td>
                            <td class="px-6 py-4 text-gray-700 text-sm">{{ $student->year ?? 'N/A' }}</td>
                            <td class="px-6 py-4 text-center space-x-2" style="position: relative; z-index: 10;">
                                <button type="button"
                                        class="open-edit-student-modal px-3 py-1 text-xs bg-indigo-500 text-white rounded-full font-bold hover:bg-indigo-600 transition shadow-sm cursor-pointer"
                                        data-id="{{ $student->id }}"
                                        data-matric="{{ $student->matric_id }}"
                                        data-name="{{ $student->name }}"
                                        data-course="{{ $student->course }}"
                                        data-year="{{ $student->year }}"
                                        style="position: relative; z-index: 20; pointer-events: auto;">
                                    Edit
                                </button>
                                <button type="button"
                                        class="open-delete-student-modal px-3 py-1 text-xs bg-red-500 text-white rounded-full font-bold hover:bg-red-600 transition shadow-sm cursor-pointer"
                                        data-id="{{ $student->id }}"
                                        data-matric="{{ $student->matric_id }}"
                                        style="position: relative; z-index: 20; pointer-events: auto;">
                                    Delete
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-gray-500 text-sm">
                                @if(request('search'))
                                    No students found for "{{ request('search') }}".
                                @else
                                    No students registered yet.
                                @endif
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>

        {{-- Pagination Buttons --}}
        @if(isset($students) && $students->hasPages())
        <div class="flex justify-center gap-4 mt-6">
            @if($students->onFirstPage())
                <button type="button" disabled
                        class="px-6 py-2 bg-gray-100 border border-gray-300 rounded-lg text-gray-400 font-medium cursor-not-allowed flex items-center gap-2">
                    <span>←</span>
                    <span>Previous</span>
                </button>
            @else
                <a href="{{ $students->previousPageUrl() }}" 
                   class="px-6 py-2 bg-teal-50 border border-teal-500 rounded-lg text-teal-600 font-medium hover:bg-teal-100 transition flex items-center gap-2">
                    <span>←</span>
                    <span>Previous</span>
                </a>
            @endif

            @if($students->hasMorePages())
                <a href="{{ $students->nextPageUrl() }}" 
                   class="px-6 py-2 bg-teal-50 border border-teal-500 rounded-lg text-teal-600 font-medium hover:bg-teal-100 transition flex items-center gap-2">
                    <span>Next</span>
                    <span>→</span>
                </a>
            @else
                <button type="button" disabled
                        class="px-6 py-2 bg-gray-100 border border-gray-300 rounded-lg text-gray-400 font-medium cursor-not-allowed flex items-center gap-2">
                    <span>Next</span>
                    <span>→</span>
                </button>
            @endif
        </div>
        @endif

    </div>
</div>

{{-- Create Student Modal --}}
<div id="createStudentOverlay" class="hidden fixed inset-0 bg-black bg-opacity-25 z-[1000] items-center justify-center">
    <div class="bg-white w-[520px] max-w-[90%] rounded-xl p-8 shadow-2xl">
        <h2 class="text-center mb-6 text-2xl font-bold text-gray-800">
            Add New Student
        </h2>

        <form method="POST" action="{{ route('register.student.store') ?? '#' }}" class="space-y-4">
            @csrf

            <div class="flex items-center">
                <label class="w-32 text-sm text-gray-600">Matric ID</label>
                <input type="text" name="matric_id" required
                       class="flex-1 px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-teal-500 focus:border-teal-500">
            </div>

            <div class="flex items-center">
                <label class="w-32 text-sm text-gray-600">Name</label>
                <input type="text" name="name" required
                       class="flex-1 px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-teal-500 focus:border-teal-500">
            </div>

            <div class="flex items-center">
                <label class="w-32 text-sm text-gray-600">Course</label>
                <select name="course" required
                        class="flex-1 px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-teal-500 focus:border-teal-500">
                    <option value="">Select Course</option>
                    <option value="SOFTWARE ENGINEERING">SOFTWARE ENGINEERING</option>
                    <option value="NETWORKING">NETWORKING</option>
                    <option value="GRAPHIC MULTIMEDIA">GRAPHIC MULTIMEDIA</option>
                    <option value="CYBERSECURITY">CYBERSECURITY</option>
                </select>
            </div>

            <div class="flex items-center">
                <label class="w-32 text-sm text-gray-600">Year</label>
                <input type="text" name="year" required
                       class="flex-1 px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-teal-500 focus:border-teal-500">
            </div>


            <div class="pt-4 text-center space-y-3">
                <button type="submit"
                        class="bg-indigo-600 text-white rounded-full px-10 py-2.5 font-semibold text-sm hover:bg-indigo-700 transition shadow-lg">
                    Add Student
                </button>
                <button type="button"
                        class="close-create-student-modal bg-gray-200 text-gray-700 rounded-full px-8 py-2 font-medium text-sm hover:bg-gray-300 transition">
                    Cancel
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Edit Student Modal --}}
<div id="editStudentOverlay" class="hidden fixed inset-0 bg-black bg-opacity-25 z-[1000] flex items-center justify-center">
    <div class="bg-white w-[520px] max-w-[90%] rounded-xl p-8 shadow-2xl">
        <h2 class="text-center mb-6 text-2xl font-bold text-gray-800">
            Edit Student Information
        </h2>

        <form id="editStudentForm" action="/update-student-placeholder" method="POST" class="space-y-4">
            @csrf
            @method('PUT')

            <div class="flex items-center">
                <label class="w-32 text-sm text-gray-600">Matric ID</label>
                <input type="text" name="matric_id" id="editMatricId" readonly
                       class="flex-1 px-3 py-2 border border-gray-300 rounded-lg text-sm bg-gray-100 cursor-not-allowed">
            </div>

            <div class="flex items-center">
                <label class="w-32 text-sm text-gray-600">Name</label>
                <input type="text" name="name" id="editName" required
                       class="flex-1 px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-teal-500 focus:border-teal-500">
            </div>

            <div class="flex items-center">
                <label class="w-32 text-sm text-gray-600">Course</label>
                <select name="course" id="editCourse" required
                        class="flex-1 px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-teal-500 focus:border-teal-500">
                    <option value="SOFTWARE ENGINEERING">SOFTWARE ENGINEERING</option>
                    <option value="NETWORKING">NETWORKING</option>
                    <option value="GRAPHIC MULTIMEDIA">GRAPHIC MULTIMEDIA</option>
                    <option value="CYBERSECURITY">CYBERSECURITY</option>
                </select>
            </div>

            <div class="flex items-center mb-6">
                <label class="w-32 text-sm text-gray-600">Year</label>
                <input type="text" name="year" id="editYear" required
                       class="flex-1 px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-teal-500 focus:border-teal-500">
            </div>

            <div class="pt-4 text-center space-y-3">
                <button type="submit"
                        class="bg-indigo-600 text-white rounded-full px-10 py-2.5 font-semibold text-sm hover:bg-indigo-700 transition shadow-lg">
                    Update Information
                </button>
                <button type="button"
                        class="close-edit-student-modal bg-gray-200 text-gray-700 rounded-full px-8 py-2 font-medium text-sm hover:bg-gray-300 transition">
                    Cancel
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Success Upload Modal --}}
<div id="uploadSuccessModal" class="hidden fixed inset-0 bg-black bg-opacity-25 z-[1000] flex items-center justify-center">
    <div class="bg-white w-[450px] max-w-[90%] rounded-xl shadow-2xl overflow-hidden">
        <div class="bg-green-50 p-6 border-b border-green-200">
            <div class="flex items-center justify-center mb-4">
                <svg class="w-16 h-16 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <h2 class="text-center text-2xl font-bold text-green-800">Successfully Registered!</h2>
        </div>
        <div class="p-6">
            <p id="uploadSuccessMessage" class="text-center text-gray-700 mb-6"></p>
            <div class="flex justify-center">
                <button type="button" id="closeSuccessModal"
                        class="px-8 py-2 bg-green-600 text-white rounded-lg font-semibold text-sm hover:bg-green-700 transition shadow-md">
                    OK
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Error Upload Modal --}}
<div id="uploadErrorModal" class="hidden fixed inset-0 bg-black bg-opacity-25 z-[1000] flex items-center justify-center">
    <div class="bg-white w-[450px] max-w-[90%] rounded-xl shadow-2xl overflow-hidden">
        <div class="bg-red-50 p-6 border-b border-red-200">
            <div class="flex items-center justify-center mb-4">
                <svg class="w-16 h-16 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <h2 class="text-center text-2xl font-bold text-red-800">Upload Failed</h2>
        </div>
        <div class="p-6">
            <p id="uploadErrorMessage" class="text-center text-gray-700 mb-6"></p>
            <div class="flex justify-center">
                <button type="button" id="closeErrorModal"
                        class="px-8 py-2 bg-red-600 text-white rounded-lg font-semibold text-sm hover:bg-red-700 transition shadow-md">
                    OK
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Delete Student Modal --}}
<div id="deleteStudentOverlay" class="hidden fixed inset-0 bg-black bg-opacity-25 z-[1000] items-center justify-center">
    <div class="bg-white w-[450px] max-w-[90%] rounded-xl shadow-2xl overflow-hidden">
        <div class="bg-gray-50/50 p-4 border-b border-gray-200 flex items-center justify-between">
            <h2 id="deleteModalTitle" class="font-bold text-lg text-gray-800">
                Delete Student
            </h2>
            <button class="close-delete-student-modal text-xl text-gray-500 hover:text-gray-700 transition">
                &times;
            </button>
        </div>

        <div class="p-6">
            <p class="mb-6 text-sm text-gray-700 text-center">
                This action cannot be undone once confirmed. Do you still want to proceed?
            </p>

            <div class="flex justify-center gap-4">
                <button type="button" id="confirmDeleteStudent"
                        class="bg-red-600 text-white rounded-lg px-8 py-2 font-semibold text-sm hover:bg-red-700 transition shadow-md">
                    Delete
                </button>
                <button type="button"
                        class="close-delete-student-modal bg-gray-200 text-gray-700 rounded-lg px-6 py-2 font-semibold text-sm hover:bg-gray-300 transition">
                    Cancel
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    // =========================
    // CSV Upload
    // =========================
    const csvFileInput  = document.getElementById('csvFileInput');
    const csvUploadIcon = document.getElementById('csvUploadButton');
    const csvFileName   = document.getElementById('csvFileName');
    const uploadCsvBtn  = document.getElementById('uploadCsvButton');

    if (csvUploadIcon && csvFileInput) {
        csvUploadIcon.addEventListener('click', function (e) {
            e.preventDefault();
            csvFileInput.click();
        });
    }

    if (csvFileInput && csvFileName && uploadCsvBtn) {
        csvFileInput.addEventListener('change', function () {
            const file = csvFileInput.files[0];

            if (file) {
                csvFileName.value = file.name;
                uploadCsvBtn.disabled = false;
                uploadCsvBtn.classList.remove('bg-gray-400', 'cursor-not-allowed');
                uploadCsvBtn.classList.add('bg-teal-600', 'hover:bg-teal-700', 'cursor-pointer');
            } else {
                csvFileName.value = 'No file chosen';
                uploadCsvBtn.disabled = true;
                uploadCsvBtn.classList.remove('bg-teal-600', 'hover:bg-teal-700', 'cursor-pointer');
                uploadCsvBtn.classList.add('bg-gray-400', 'cursor-not-allowed');
            }
        });

        uploadCsvBtn.addEventListener('click', async function (e) {
            e.preventDefault();

            const file = csvFileInput.files[0];
            if (!file) {
                showError('Please select a CSV file first.');
                return;
            }

            const formData = new FormData();
            formData.append('csv_file', file);
            formData.append('_token', '{{ csrf_token() }}');

            uploadCsvBtn.disabled = true;
            uploadCsvBtn.textContent = 'Uploading...';

            try {
                const response = await fetch('{{ route("register.student.upload") }}', {
                    method: 'POST',
                    body: formData,
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });

                const contentType = response.headers.get('content-type') || '';
                const isJson = contentType.includes('application/json');
                const data = isJson ? await response.json() : {};

                if (!response.ok || data.success === false) {
                showError((data && data.message) ? data.message : 'Upload failed. Please check the file and try again.');
                    uploadCsvBtn.disabled = false;
                    uploadCsvBtn.textContent = 'Upload';
                    return;
                }

                const successModal = document.getElementById('uploadSuccessModal');
                const successMessage = document.getElementById('uploadSuccessMessage');
                if (successModal && successMessage) {
                    successMessage.textContent = data.message || 'Successfully registered!';
                    successModal.classList.remove('hidden');
                    successModal.classList.add('flex');
                } else {
                    alert(data.message || 'Successfully registered!');
                }
                setTimeout(() => {
                    location.reload();
                }, 1500);
            } catch (err) {
                console.error(err);
                showError('Upload failed. Please try again.');
                uploadCsvBtn.disabled = false;
                uploadCsvBtn.textContent = 'Upload';
            }
        });
    }

    // Close success modal
    const closeSuccessModal = document.getElementById('closeSuccessModal');
    const uploadSuccessModal = document.getElementById('uploadSuccessModal');
    if (closeSuccessModal && uploadSuccessModal) {
        closeSuccessModal.addEventListener('click', function() {
            uploadSuccessModal.classList.remove('flex');
            uploadSuccessModal.classList.add('hidden');
            location.reload();
        });
        uploadSuccessModal.addEventListener('click', function(e) {
            if (e.target === uploadSuccessModal) {
                uploadSuccessModal.classList.remove('flex');
                uploadSuccessModal.classList.add('hidden');
                location.reload();
            }
        });
    }

    // Error modal helpers
    const uploadErrorModal = document.getElementById('uploadErrorModal');
    const uploadErrorMessage = document.getElementById('uploadErrorMessage');
    const closeErrorModal = document.getElementById('closeErrorModal');
    function showError(message) {
        if (uploadErrorMessage) uploadErrorMessage.textContent = message || 'Upload failed. Please try again.';
        if (uploadErrorModal) {
            uploadErrorModal.classList.remove('hidden');
            uploadErrorModal.classList.add('flex');
        } else {
            alert(message);
        }
    }
    if (closeErrorModal && uploadErrorModal) {
        const hideError = () => {
            uploadErrorModal.classList.remove('flex');
            uploadErrorModal.classList.add('hidden');
        };
        closeErrorModal.addEventListener('click', hideError);
        uploadErrorModal.addEventListener('click', function(e) {
            if (e.target === uploadErrorModal) hideError();
        });
    }

    // =========================
    // Modal helpers
    // =========================
    function openModal(overlay) {
        if (!overlay) return;
        overlay.classList.remove('hidden');
        overlay.classList.add('flex');
    }

    function closeModal(overlay) {
        if (!overlay) return;
        overlay.classList.remove('flex');
        overlay.classList.add('hidden');
    }

    // =========================
    // Create Modal
    // =========================
    const createOverlay = document.getElementById('createStudentOverlay');

    document.querySelectorAll('.open-create-student-modal').forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            openModal(createOverlay);
        });
    });

    document.querySelectorAll('.close-create-student-modal').forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            closeModal(createOverlay);
        });
    });

    if (createOverlay) {
        createOverlay.addEventListener('click', (e) => {
            if (e.target === createOverlay) closeModal(createOverlay);
        });
    }

    // =========================
    // Edit Modal
    // =========================
    const editOverlay = document.getElementById('editStudentOverlay');
    const editForm    = document.getElementById('editStudentForm');

    const inputMatric = document.getElementById('editMatricId');
    const inputName   = document.getElementById('editName');
    const selectCourse= document.getElementById('editCourse');
    const inputYear   = document.getElementById('editYear');

    let editStudentId = '';

    document.body.addEventListener('click', function (e) {
        const editBtn = e.target.closest('.open-edit-student-modal');
        if (!editBtn) return;

        e.preventDefault();

        editStudentId = editBtn.dataset.id || '';
        if (!editStudentId) {
            alert('Error: Student ID not found.');
            return;
        }

        if (inputMatric) inputMatric.value = editBtn.dataset.matric || '';
        if (inputName)   inputName.value   = editBtn.dataset.name   || '';
        if (selectCourse)selectCourse.value= editBtn.dataset.course || 'SOFTWARE ENGINEERING';
        if (inputYear)   inputYear.value   = editBtn.dataset.year   || '';

        if (editForm) editForm.action = '/administrator/register-student/' + editStudentId;

        openModal(editOverlay);
    });

    document.querySelectorAll('.close-edit-student-modal').forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            closeModal(editOverlay);
        });
    });

    if (editOverlay) {
        editOverlay.addEventListener('click', (e) => {
            if (e.target === editOverlay) closeModal(editOverlay);
        });
    }

    if (editForm) {
        editForm.addEventListener('submit', async function (e) {
            e.preventDefault();

            try {
                const formData = new FormData(editForm); // includes _method=PUT

                const response = await fetch(editForm.action, {
                    method: 'POST',
                    body: formData,
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });

                if (response.redirected) {
                    window.location.href = response.url;
                    return;
                }

                const contentType = response.headers.get('content-type') || '';
                if (contentType.includes('application/json')) {
                    const data = await response.json();
                    alert(data.message || 'Student updated successfully!');
                } else {
                    alert('Student updated successfully!');
                }

                location.reload();
            } catch (err) {
                console.error(err);
                alert('An error occurred while updating the student.');
            }
        });
    }

    // =========================
    // Delete Modal
    // =========================
    const deleteOverlay    = document.getElementById('deleteStudentOverlay');
    const deleteTitle      = document.getElementById('deleteModalTitle');
    const confirmDeleteBtn = document.getElementById('confirmDeleteStudent');

    let deleteStudentId = '';

    document.body.addEventListener('click', function (e) {
        const deleteBtn = e.target.closest('.open-delete-student-modal');
        if (!deleteBtn) return;

        e.preventDefault();

        deleteStudentId = deleteBtn.dataset.id || '';
        const matricId = deleteBtn.dataset.matric || 'N/A';

        if (!deleteStudentId) {
            alert('Error: Student ID not found.');
            return;
        }

        if (deleteTitle) deleteTitle.textContent = 'Delete Student (' + matricId + ')';
        openModal(deleteOverlay);
    });

    document.querySelectorAll('.close-delete-student-modal').forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            closeModal(deleteOverlay);
        });
    });

    if (deleteOverlay) {
        deleteOverlay.addEventListener('click', (e) => {
            if (e.target === deleteOverlay) closeModal(deleteOverlay);
        });
    }

    if (confirmDeleteBtn) {
        confirmDeleteBtn.addEventListener('click', async function (e) {
            e.preventDefault();

            if (!deleteStudentId) {
                alert('Error: Student ID not found.');
                return;
            }

            try {
                const formData = new FormData();
                formData.append('_token', '{{ csrf_token() }}');
                formData.append('_method', 'DELETE');

                const response = await fetch('/administrator/register-student/' + deleteStudentId, {
                    method: 'POST',
                    body: formData,
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });

                if (response.redirected) {
                    window.location.href = response.url;
                    return;
                }

                const contentType = response.headers.get('content-type') || '';
                if (contentType.includes('application/json')) {
                    const data = await response.json();
                    alert(data.message || 'Student deleted successfully!');
                } else {
                    alert('Student deleted successfully!');
                }

                location.reload();
            } catch (err) {
                console.error(err);
                alert('An error occurred while deleting the student.');
            }
        });
    }

});
</script>
@endpush

@endsection
