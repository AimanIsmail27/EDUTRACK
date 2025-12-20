@extends('layout.administrator')

@section('title', 'Register Lecturer')

@section('content')

{{-- Main Container: Using a strong gradient blend... --}}
<div class="min-h-screen p-12 bg-gradient-to-br from-indigo-200/80 to-teal-200/80">

    {{-- Main Content Card: Lecturer List and Controls --}}
    <div class="bg-white p-8 rounded-2xl shadow-xl shadow-gray-400/30 border border-gray-100/80">
        
        {{-- Header row: title + controls --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-6 pb-4 border-b border-gray-200">
            <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight mb-4 sm:mb-0">
                Lecturer Registration
            </h1>

            <div class="flex items-center gap-3">
                {{-- CSV Input/Upload Group --}}
                <div class="flex items-center space-x-2 p-1.5 bg-gray-50 border border-gray-300 rounded-lg shadow-inner" 
                     style="position: relative;">
                    <input type="text"
                           id="csvFileName"
                           value="No file chosen"
                           readonly
                           class="px-2 py-1 border-none bg-transparent w-32 text-sm text-gray-700 pointer-events-none">
                    
                    {{-- Hidden file input --}}
                    <input type="file" 
                           id="csvFileInput" 
                           accept=".csv"
                           style="display: none;">
                    
                    {{-- Icon (upload) button --}}
                    <button type="button" 
                            id="csvUploadButton"
                            class="text-gray-600 hover:text-teal-600 transition p-1 cursor-pointer"
                            style="pointer-events: auto;">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                        </svg>
                    </button>
                </div>

                {{-- Upload button (Only enabled when CSV file is selected) --}}
                <button type="button" 
                        id="uploadCsvButton"
                        disabled
                        class="px-6 py-2 bg-gray-400 text-white rounded-lg font-bold cursor-not-allowed transition shadow-md text-sm">
                    Upload
                </button>
            </div>
        </div>

        {{-- Lecturer Table --}}
        <div class="overflow-x-auto shadow-xl rounded-xl border border-gray-200/80">
            <table class="w-full">
                <thead>
                    <tr class="bg-gray-100 text-gray-600 text-xs uppercase tracking-widest">
                        <th class="px-6 py-3 text-left font-extrabold">Staff ID</th>
                        <th class="px-6 py-3 text-left font-extrabold">Name</th>
                        <th class="px-6 py-3 text-left font-extrabold">Email</th>
                        <th class="px-6 py-3 text-center font-extrabold">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @if(isset($lecturers) && $lecturers->count() > 0)
                        @foreach($lecturers as $index => $lecturer)
                        <tr class="{{ $index % 2 ? 'bg-white' : 'bg-gray-50' }} hover:bg-teal-50 transition duration-150">
                            <td class="px-6 py-4 font-semibold text-gray-900 text-sm">{{ $lecturer->staff_id ?? 'N/A' }}</td>
                            <td class="px-6 py-4 text-gray-700 text-sm">{{ $lecturer->name }}</td>
                            <td class="px-6 py-4 text-gray-700 text-sm">{{ $lecturer->email }}</td>
                            <td class="px-6 py-4 text-center space-x-2" style="position: relative; z-index: 10;">
                                <button type="button"
                                        class="open-edit-lecturer-modal px-3 py-1 text-xs bg-indigo-500 text-white rounded-full font-bold hover:bg-indigo-600 transition shadow-sm cursor-pointer"
                                        data-id="{{ $lecturer->id }}"
                                        data-staff="{{ $lecturer->staff_id }}" 
                                        data-name="{{ $lecturer->name }}" 
                                        data-email="{{ $lecturer->email }}"
                                        style="position: relative; z-index: 20; pointer-events: auto;">
                                    Edit
                                </button>
                                <button type="button"
                                        class="open-delete-lecturer-modal px-3 py-1 text-xs bg-red-500 text-white rounded-full font-bold hover:bg-red-600 transition shadow-sm cursor-pointer"
                                        data-id="{{ $lecturer->id }}"
                                        data-staff="{{ $lecturer->staff_id }}"
                                        style="position: relative; z-index: 20; pointer-events: auto;">
                                    Delete
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="4" class="px-6 py-8 text-center text-gray-500 text-sm">
                                No lecturers registered yet. 
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
        
        {{-- Pagination Buttons --}}
        @if(isset($lecturers) && $lecturers->hasPages())
        <div class="flex justify-center gap-4 mt-6">
            @if($lecturers->onFirstPage())
                <button type="button" disabled
                        class="px-6 py-2 bg-gray-100 border border-gray-300 rounded-lg text-gray-400 font-medium cursor-not-allowed flex items-center gap-2">
                    <span>←</span>
                    <span>Previous</span>
                </button>
            @else
                <a href="{{ $lecturers->previousPageUrl() }}" 
                   class="px-6 py-2 bg-teal-50 border border-teal-500 rounded-lg text-teal-600 font-medium hover:bg-teal-100 transition flex items-center gap-2">
                    <span>←</span>
                    <span>Previous</span>
                </a>
            @endif

            @if($lecturers->hasMorePages())
                <a href="{{ $lecturers->nextPageUrl() }}" 
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

{{-- ------------------------------------------------ --}}
{{-- MODALS (Hidden by default)                       --}}
{{-- ------------------------------------------------ --}}

{{-- Create Lecturer Modal Overlay --}}
<div id="createLecturerOverlay"
     class="hidden fixed inset-0 bg-black bg-opacity-25 z-[1000] items-center justify-center">
    <div class="bg-white w-[520px] max-w-[90%] rounded-xl p-8 shadow-2xl">
        <h2 class="text-center mb-6 text-2xl font-bold text-gray-800">
            Add New Lecturer
        </h2>

        <form method="POST" action="{{ route('register.lecturer.store') ?? '#' }}" class="space-y-4">
            @csrf

            {{-- Staff ID --}}
            <div class="flex items-center">
                <label class="w-32 text-sm text-gray-600">Staff ID</label>
                <input type="text" name="staff_id" required
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


            {{-- Buttons --}}
            <div class="pt-4 text-center space-y-3">
                <button type="submit"
                        class="bg-indigo-600 text-white rounded-full px-10 py-2.5 font-semibold text-sm hover:bg-indigo-700 transition shadow-lg">
                    Add Lecturer
                </button>
                <button type="button" class="close-create-lecturer-modal bg-gray-200 text-gray-700 rounded-full px-8 py-2 font-medium text-sm hover:bg-gray-300 transition">
                    Cancel
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Edit Lecturer Modal Overlay --}}
<div id="editLecturerOverlay"
     class="hidden fixed inset-0 bg-black bg-opacity-25 z-[1000] flex items-center justify-center">
    <div class="bg-white w-[520px] max-w-[90%] rounded-xl p-8 shadow-2xl">
        <h2 class="text-center mb-6 text-2xl font-bold text-gray-800">
            Edit Lecturer Information
        </h2>

        <form id="editLecturerForm" action="/update-lecturer-placeholder" method="POST" class="space-y-4">
            @csrf
            @method('PUT')

            {{-- Staff ID (Readonly) --}}
            <div class="flex items-center">
                <label class="w-32 text-sm text-gray-600">Staff ID</label>
                <input type="text" name="staff_id" id="editStaffId" readonly
                       class="flex-1 px-3 py-2 border border-gray-300 rounded-lg text-sm bg-gray-100 cursor-not-allowed">
            </div>

            {{-- Name --}}
            <div class="flex items-center">
                <label class="w-32 text-sm text-gray-600">Name</label>
                <input type="text" name="name" id="editLecturerName" required
                       class="flex-1 px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-teal-500 focus:border-teal-500">
            </div>

            {{-- Email --}}
            <div class="flex items-center mb-6">
                <label class="w-32 text-sm text-gray-600">Email</label>
                <input type="email" name="email" id="editLecturerEmail" required
                       class="flex-1 px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-teal-500 focus:border-teal-500">
            </div>

            {{-- Buttons --}}
            <div class="pt-4 text-center space-y-3">
                <button type="submit"
                        class="bg-indigo-600 text-white rounded-full px-10 py-2.5 font-semibold text-sm hover:bg-indigo-700 transition shadow-lg">
                    Update Information
                </button>
                <button type="button" class="close-edit-lecturer-modal bg-gray-200 text-gray-700 rounded-full px-8 py-2 font-medium text-sm hover:bg-gray-300 transition">
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

{{-- Delete Lecturer Confirmation Modal Overlay --}}
<div id="deleteLecturerOverlay"
     class="hidden fixed inset-0 bg-black bg-opacity-25 z-[1000] items-center justify-center">
    <div class="bg-white w-[450px] max-w-[90%] rounded-xl shadow-2xl overflow-hidden">
        {{-- Title Bar --}}
        <div class="bg-gray-50/50 p-4 border-b border-gray-200 flex items-center justify-between">
            <h2 id="deleteLecturerTitle" class="font-bold text-lg text-gray-800">
                Delete User (2111)
            </h2>
            <button class="close-delete-lecturer-modal text-xl text-gray-500 hover:text-gray-700 transition">
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
                <button type="button" id="confirmDeleteLecturer"
                        class="bg-red-600 text-white rounded-lg px-8 py-2 font-semibold text-sm hover:bg-red-700 transition shadow-md">
                    Delete
                </button>
                <button type="button" class="close-delete-lecturer-modal bg-gray-200 text-gray-700 rounded-lg px-6 py-2 font-semibold text-sm hover:bg-gray-300 transition">
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

    // =========================
    // CSV Upload
    // =========================
    const csvFileInput   = document.getElementById('csvFileInput');
    const csvUploadIcon  = document.getElementById('csvUploadButton');
    const csvFileName    = document.getElementById('csvFileName');
    const uploadCsvBtn   = document.getElementById('uploadCsvButton');

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
                alert('Please select a CSV file first.');
                return;
            }

            const formData = new FormData();
            formData.append('csv_file', file);
            formData.append('_token', '{{ csrf_token() }}');

            uploadCsvBtn.disabled = true;
            uploadCsvBtn.textContent = 'Uploading...';

            try {
                const response = await fetch('{{ route("register.lecturer.upload") }}', {
                    method: 'POST',
                    body: formData,
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });

                // If backend returns HTML/redirect, this avoids JSON crash
                const contentType = response.headers.get('content-type') || '';
                if (!contentType.includes('application/json')) {
                    // fallback: reload (backend may redirect)
                    location.reload();
                    return;
                }

                const data = await response.json();

                if (data.success) {
                    // Show success modal
                    const successModal = document.getElementById('uploadSuccessModal');
                    const successMessage = document.getElementById('uploadSuccessMessage');
                    if (successModal && successMessage) {
                        successMessage.textContent = data.message || 'Successfully registered!';
                        successModal.classList.remove('hidden');
                        successModal.classList.add('flex');
                    } else {
                        alert(data.message || 'Successfully registered!');
                    }
                    // Reload after a short delay to show the success message
                    setTimeout(() => {
                        location.reload();
                    }, 1500);
                } else {
                    // Only show error if no records were registered at all
                    if (data.success_count === 0) {
                        alert('Upload failed: ' + (data.message || 'Unknown error'));
                        uploadCsvBtn.disabled = false;
                        uploadCsvBtn.textContent = 'Upload';
                    } else {
                        // If some records succeeded, show success message
                        const successModal = document.getElementById('uploadSuccessModal');
                        const successMessage = document.getElementById('uploadSuccessMessage');
                        if (successModal && successMessage) {
                            successMessage.textContent = data.message || 'Upload completed with some errors.';
                            successModal.classList.remove('hidden');
                            successModal.classList.add('flex');
                            setTimeout(() => {
                                location.reload();
                            }, 1500);
                        } else {
                            alert(data.message);
                            location.reload();
                        }
                    }
                }
            } catch (err) {
                console.error(err);
                alert('An error occurred during upload. Please try again.');
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
    // Create Modal (if you have button)
    // =========================
    const createOverlay = document.getElementById('createLecturerOverlay');
    document.querySelectorAll('.open-create-lecturer-modal').forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            openModal(createOverlay);
        });
    });
    document.querySelectorAll('.close-create-lecturer-modal').forEach(btn => {
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
    const editOverlay = document.getElementById('editLecturerOverlay');
    const editForm    = document.getElementById('editLecturerForm');
    const inputStaff  = document.getElementById('editStaffId');
    const inputName   = document.getElementById('editLecturerName');
    const inputEmail  = document.getElementById('editLecturerEmail');

    let editLecturerId = '';

    // open edit (delegation)
    document.body.addEventListener('click', function (e) {
        const editBtn = e.target.closest('.open-edit-lecturer-modal');
        if (!editBtn) return;

        e.preventDefault();

        editLecturerId = editBtn.dataset.id || '';
        if (!editLecturerId) {
            alert('Error: Lecturer ID not found.');
            return;
        }

        if (inputStaff) inputStaff.value = editBtn.dataset.staff || '';
        if (inputName)  inputName.value  = editBtn.dataset.name  || '';
        if (inputEmail) inputEmail.value = editBtn.dataset.email || '';

        if (editForm) {
            // MUST match your route
            editForm.action = '/administrator/register-lecturer/' + editLecturerId;
        }

        openModal(editOverlay);
    });

    // close edit
    document.querySelectorAll('.close-edit-lecturer-modal').forEach(btn => {
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

    // submit edit (AJAX)
    if (editForm) {
        editForm.addEventListener('submit', async function (e) {
            e.preventDefault();

            try {
                const formData = new FormData(editForm);

                const response = await fetch(editForm.action, {
                    method: 'POST', // Laravel will read _method=PUT from formData
                    body: formData,
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });

                // if backend redirects, follow it
                if (response.redirected) {
                    window.location.href = response.url;
                    return;
                }

                const contentType = response.headers.get('content-type') || '';
                if (contentType.includes('application/json')) {
                    const data = await response.json();
                    alert(data.message || 'Lecturer updated successfully!');
                } else {
                    alert('Lecturer updated successfully!');
                }

                location.reload();
            } catch (err) {
                console.error(err);
                alert('An error occurred while updating the lecturer.');
            }
        });
    }

    // =========================
    // Delete Modal
    // =========================
    const deleteOverlay   = document.getElementById('deleteLecturerOverlay');
    const deleteTitle     = document.getElementById('deleteLecturerTitle');
    const confirmDeleteBtn= document.getElementById('confirmDeleteLecturer');

    let deleteLecturerId = '';

    // open delete (delegation)
    document.body.addEventListener('click', function (e) {
        const deleteBtn = e.target.closest('.open-delete-lecturer-modal');
        if (!deleteBtn) return;

        e.preventDefault();

        deleteLecturerId = deleteBtn.dataset.id || '';
        const staffId = deleteBtn.dataset.staff || 'N/A';

        if (!deleteLecturerId) {
            alert('Error: Lecturer ID not found.');
            return;
        }

        if (deleteTitle) deleteTitle.textContent = 'Delete Lecturer (' + staffId + ')';
        openModal(deleteOverlay);
    });

    // close delete
    document.querySelectorAll('.close-delete-lecturer-modal').forEach(btn => {
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

    // confirm delete
    if (confirmDeleteBtn) {
        confirmDeleteBtn.addEventListener('click', async function (e) {
            e.preventDefault();

            if (!deleteLecturerId) {
                alert('Error: Lecturer ID not found.');
                return;
            }

            try {
                const formData = new FormData();
                formData.append('_token', '{{ csrf_token() }}');
                formData.append('_method', 'DELETE');

                const response = await fetch('/administrator/register-lecturer/' + deleteLecturerId, {
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
                    alert(data.message || 'Lecturer deleted successfully!');
                } else {
                    alert('Lecturer deleted successfully!');
                }

                location.reload();
            } catch (err) {
                console.error(err);
                alert('An error occurred while deleting the lecturer.');
            }
        });
    }

});
</script>
@endpush


@endsection
