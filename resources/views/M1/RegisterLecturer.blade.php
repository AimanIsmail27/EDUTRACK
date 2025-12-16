@extends('layout.administrator') {{-- remove if you don't use a layout --}}

@section('content')
<div style="min-height:100vh; background:#d9d9d9; font-family:Arial, sans-serif;">

    {{-- Top bar --}}
    <div style="background:#f5f5f5; padding:15px 30px; border-bottom:1px solid #ccc;">
        <span style="font-size:22px; font-weight:bold;">EduTrack</span>
    </div>

    <div style="display:flex;">

        {{-- Left sidebar (same style as other pages) --}}
        <div style="width:220px; background:#f5f5f5; border-right:1px solid #ccc; padding:20px 15px;">

            {{-- Dashboard item --}}
            <div style="display:flex; align-items:center; margin-bottom:25px; font-size:15px;">
                <span style="margin-right:8px;">🏠</span>
                <span>Dashboard</span>
            </div>

            {{-- Register User dropdown title --}}
            <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:8px; font-size:15px;">
                <div style="display:flex; align-items:center;">
                    <span style="margin-right:8px;">👤</span>
                    <span>Register User</span>
                </div>
                <span>▼</span>
            </div>

            {{-- Student option --}}
            <a href="{{ route('register.student') ?? '#' }}"
               style="display:block; background:#ffffff; border:1px solid #c0c0c0; padding:5px 10px; font-size:14px; text-decoration:none; color:#000; margin-bottom:5px;">
                Student
            </a>

            {{-- Lecturer option (highlighted) --}}
            <a href="{{ route('register.lecturer') ?? '#' }}"
               style="display:block; background:#e0e0e0; border:1px solid #c0c0c0; padding:5px 10px; font-size:14px; text-decoration:none; color:#000;">
                Lecturer
            </a>
        </div>

        {{-- Main content --}}
        <div style="flex:1; padding:40px;">

            {{-- Header area --}}
            <div style="background:#f5f5f5; padding:25px 30px; border:1px solid #cccccc;">

                <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:20px;">
                    <div style="font-size:22px; font-weight:bold;">Lecturer</div>

                    <div style="display:flex; align-items:center; gap:10px;">
                        {{-- Document.csv input --}}
                        <input type="text"
                               value="Document.csv"
                               style="padding:5px 10px; border:1px solid #a0a0a0; width:150px; font-size:13px;">

                        {{-- Icon (upload) --}}
                        <button style="border:1px solid #a0a0a0; background:#ffffff; padding:5px 10px; cursor:pointer;">
                            📁
                        </button>

                        {{-- Add button --}}
                        <button type="button" class="open-create-lecturer-modal" style="border:none; background:#e0e0e0; padding:6px 25px; font-size:14px; cursor:pointer;">
                            Add
                        </button>
                    </div>
                </div>

                {{-- Table --}}
                <table style="width:100%; border-collapse:collapse; font-size:14px; background:#ffffff;">
                    <thead>
                        <tr style="background:#e0e0e0;">
                            <th style="padding:10px; text-align:left; border-bottom:1px solid #c0c0c0;">Staff ID</th>
                            <th style="padding:10px; text-align:left; border-bottom:1px solid #c0c0c0;">Name</th>
                            <th style="padding:10px; text-align:left; border-bottom:1px solid #c0c0c0;">Email</th>
                            <th style="padding:10px; text-align:left; border-bottom:1px solid #c0c0c0;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                      {{-- data will go here later, e.g.
                      @foreach ($lecturers as $lecturer)
                          <tr>
                              <td>{{ $lecturer->staff_id }}</td>
                              <td>{{ $lecturer->name }}</td>
                              <td>{{ $lecturer->email }}</td>
                              <td>
                                  <button
                                      class="open-edit-lecturer-modal"
                                      data-staff="{{ $lecturer->staff_id }}"
                                      data-name="{{ $lecturer->name }}"
                                      data-email="{{ $lecturer->email }}">
                                      Edit
                                  </button>
                                  <button
                                      class="open-delete-lecturer-modal"
                                      data-staff="{{ $lecturer->staff_id }}"
                                      style="background:#dc3545; color:#ffffff; border:none; padding:5px 12px; cursor:pointer;">
                                      Delete
                                  </button>
                              </td>
                          </tr>
                      @endforeach
                      --}}
                    </tbody>
                </table>

            </div>
        </div>
    </div>
</div>

{{-- Create Lecturer Modal Overlay --}}
<div id="createLecturerOverlay"
     style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.25); z-index:1000; align-items:center; justify-content:center;">
    <div style="background:#ffffff; width:520px; max-width:90%; border-radius:10px; padding:30px 40px; box-shadow:0 10px 30px rgba(0,0,0,0.2);">
        <h2 style="text-align:center; margin-bottom:25px; font-size:22px; font-weight:bold;">
            Add New Lecturer
        </h2>

        <form method="POST" action="{{ route('register.lecturer.store') }}">
            @csrf

            {{-- Staff ID --}}
            <div style="margin-bottom:15px; display:flex; align-items:center;">
                <label style="width:120px; font-size:13px;">Staff ID</label>
                <input type="text" name="staff_id" required
                       style="flex:1; padding:7px 10px; border:1px solid #c0c0c0; border-radius:3px; font-size:13px;">
            </div>

            {{-- Name --}}
            <div style="margin-bottom:15px; display:flex; align-items:center;">
                <label style="width:120px; font-size:13px;">Name</label>
                <input type="text" name="name" required
                       style="flex:1; padding:7px 10px; border:1px solid #c0c0c0; border-radius:3px; font-size:13px;">
            </div>

            {{-- Email --}}
            <div style="margin-bottom:15px; display:flex; align-items:center;">
                <label style="width:120px; font-size:13px;">Email</label>
                <input type="email" name="email" required
                       style="flex:1; padding:7px 10px; border:1px solid #c0c0c0; border-radius:3px; font-size:13px;">
            </div>

            {{-- Password --}}
            <div style="margin-bottom:15px; display:flex; align-items:center;">
                <label style="width:120px; font-size:13px;">Password</label>
                <input type="password" name="password" required
                       style="flex:1; padding:7px 10px; border:1px solid #c0c0c0; border-radius:3px; font-size:13px;">
            </div>

            {{-- Password Confirmation --}}
            <div style="margin-bottom:25px; display:flex; align-items:center;">
                <label style="width:120px; font-size:13px;">Confirm Password</label>
                <input type="password" name="password_confirmation" required
                       style="flex:1; padding:7px 10px; border:1px solid #c0c0c0; border-radius:3px; font-size:13px;">
            </div>

            {{-- Buttons --}}
            <div style="text-align:center; margin-bottom:10px;">
                <button type="submit"
                        style="background:#2878ff; color:#ffffff; border:none; border-radius:20px; padding:8px 40px; font-size:14px; cursor:pointer;">
                    Add Lecturer
                </button>
            </div>
            <div style="text-align:center;">
                <button type="button" class="close-create-lecturer-modal"
                        style="background:#e0e0e0; color:#000000; border:none; border-radius:20px; padding:6px 26px; font-size:13px; cursor:pointer;">
                    Cancel
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Edit Lecturer Modal Overlay --}}
<div id="editLecturerOverlay"
     style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.25); z-index:1000; align-items:center; justify-content:center;">
    <div style="background:#ffffff; width:520px; max-width:90%; border-radius:10px; padding:30px 40px; box-shadow:0 10px 30px rgba(0,0,0,0.2);">
        <h2 style="text-align:center; margin-bottom:25px; font-size:22px; font-weight:bold;">
            Edit Lecturer Information
        </h2>

        <form>
            {{-- Staff ID --}}
            <div style="margin-bottom:15px; display:flex; align-items:center;">
                <label style="width:120px; font-size:13px;">Staff ID</label>
                <input type="text" name="staff_id" id="editStaffId"
                       value="2111"
                       style="flex:1; padding:7px 10px; border:1px solid #c0c0c0; border-radius:3px; font-size:13px;">
            </div>

            {{-- Name --}}
            <div style="margin-bottom:15px; display:flex; align-items:center;">
                <label style="width:120px; font-size:13px;">Name</label>
                <input type="text" name="name" id="editLecturerName"
                       value="TS DR KAMIL"
                       style="flex:1; padding:7px 10px; border:1px solid #c0c0c0; border-radius:3px; font-size:13px;">
            </div>

            {{-- Email --}}
            <div style="margin-bottom:25px; display:flex; align-items:center;">
                <label style="width:120px; font-size:13px;">Email</label>
                <input type="email" name="email" id="editLecturerEmail"
                       value="kamil12@gmail.com"
                       style="flex:1; padding:7px 10px; border:1px solid #c0c0c0; border-radius:3px; font-size:13px;">
            </div>

            {{-- Buttons --}}
            <div style="text-align:center; margin-bottom:10px;">
                <button type="button"
                        style="background:#2878ff; color:#ffffff; border:none; border-radius:20px; padding:8px 40px; font-size:14px; cursor:pointer;">
                    Update Information
                </button>
            </div>
            <div style="text-align:center;">
                <button type="button" class="close-edit-lecturer-modal"
                        style="background:#e0e0e0; color:#000000; border:none; border-radius:20px; padding:6px 26px; font-size:13px; cursor:pointer;">
                    Cancel
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Delete Lecturer Confirmation Modal Overlay --}}
<div id="deleteLecturerOverlay"
     style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.25); z-index:1000; align-items:center; justify-content:center;">
    <div style="background:#ffffff; width:450px; max-width:90%; border-radius:8px; box-shadow:0 10px 30px rgba(0,0,0,0.2); overflow:hidden;">
        {{-- Title Bar --}}
        <div style="background:#ffffff; padding:15px 20px; border-bottom:1px solid #e0e0e0; display:flex; align-items:center; justify-content:space-between;">
            <h2 id="deleteLecturerTitle" style="margin:0; font-size:18px; font-weight:bold; color:#000;">
                Delete User (2111)
            </h2>
            <button class="close-delete-lecturer-modal"
                    style="background:none; border:none; font-size:20px; cursor:pointer; color:#666; padding:0; width:24px; height:24px; display:flex; align-items:center; justify-content:center;">
                ×
            </button>
        </div>

        {{-- Modal Body --}}
        <div style="padding:30px 20px;">
            <p style="margin:0 0 30px 0; font-size:14px; color:#333; text-align:center;">
                This action cannot be undo once confirm. Do you still want to proceed?
            </p>

            {{-- Buttons --}}
            <div style="display:flex; justify-content:center; gap:15px;">
                <button type="button" class="close-delete-lecturer-modal"
                        style="background:#e0e0e0; color:#000000; border:none; border-radius:4px; padding:8px 30px; font-size:14px; cursor:pointer;">
                    Cancel
                </button>
                <button type="button" id="confirmDeleteLecturer"
                        style="background:#dc3545; color:#ffffff; border:none; border-radius:4px; padding:8px 30px; font-size:14px; cursor:pointer;">
                    Delete
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Create Modal Logic
        const createOverlay = document.getElementById('createLecturerOverlay');
        const openCreateButtons = document.querySelectorAll('.open-create-lecturer-modal');
        const closeCreateButtons = document.querySelectorAll('.close-create-lecturer-modal');

        function openCreateModal() {
            if (createOverlay) {
                createOverlay.style.display = 'flex';
            }
        }

        function closeCreateModal() {
            if (createOverlay) {
                createOverlay.style.display = 'none';
            }
        }

        openCreateButtons.forEach(function (btn) {
            btn.addEventListener('click', function (event) {
                event.preventDefault();
                openCreateModal();
            });
        });

        closeCreateButtons.forEach(function (btn) {
            btn.addEventListener('click', function (event) {
                event.preventDefault();
                closeCreateModal();
            });
        });

        if (createOverlay) {
            createOverlay.addEventListener('click', function (event) {
                if (event.target === createOverlay) {
                    closeCreateModal();
                }
            });
        }

        // Edit modal
        const overlay = document.getElementById('editLecturerOverlay');
        const openButtons = document.querySelectorAll('.open-edit-lecturer-modal');
        const closeButtons = document.querySelectorAll('.close-edit-lecturer-modal');
        const inputStaff = document.getElementById('editStaffId');
        const inputName = document.getElementById('editLecturerName');
        const inputEmail = document.getElementById('editLecturerEmail');

        function openModal() {
            if (overlay) {
                overlay.style.display = 'flex';
            }
        }

        function closeModal() {
            if (overlay) {
                overlay.style.display = 'none';
            }
        }

        openButtons.forEach(function (btn) {
            btn.addEventListener('click', function (event) {
                event.preventDefault();

                if (inputStaff && this.dataset.staff) {
                    inputStaff.value = this.dataset.staff;
                }
                if (inputName && this.dataset.name) {
                    inputName.value = this.dataset.name;
                }
                if (inputEmail && this.dataset.email) {
                    inputEmail.value = this.dataset.email;
                }

                openModal();
            });
        });

        closeButtons.forEach(function (btn) {
            btn.addEventListener('click', function (event) {
                event.preventDefault();
                closeModal();
            });
        });

        if (overlay) {
            overlay.addEventListener('click', function (event) {
                if (event.target === overlay) {
                    closeModal();
                }
            });
        }

        // Delete modal
        const deleteOverlay = document.getElementById('deleteLecturerOverlay');
        const deleteTitle = document.getElementById('deleteLecturerTitle');
        const openDeleteButtons = document.querySelectorAll('.open-delete-lecturer-modal');
        const closeDeleteButtons = document.querySelectorAll('.close-delete-lecturer-modal');
        const confirmDeleteBtn = document.getElementById('confirmDeleteLecturer');
        let currentStaffId = '';

        function openDeleteModal() {
            if (deleteOverlay) {
                deleteOverlay.style.display = 'flex';
            }
        }

        function closeDeleteModal() {
            if (deleteOverlay) {
                deleteOverlay.style.display = 'none';
            }
        }

        openDeleteButtons.forEach(function (btn) {
            btn.addEventListener('click', function (event) {
                event.preventDefault();
                const staffId = this.dataset.staff || '2111';
                currentStaffId = staffId;
                if (deleteTitle) {
                    deleteTitle.textContent = 'Delete User (' + staffId + ')';
                }
                openDeleteModal();
            });
        });

        closeDeleteButtons.forEach(function (btn) {
            btn.addEventListener('click', function (event) {
                event.preventDefault();
                closeDeleteModal();
            });
        });

        if (confirmDeleteBtn) {
            confirmDeleteBtn.addEventListener('click', function (event) {
                event.preventDefault();
                // TODO: implement actual delete logic (form submit / AJAX)
                alert('Delete functionality for ' + currentStaffId + ' - implement your delete logic here');
                closeDeleteModal();
            });
        }

        if (deleteOverlay) {
            deleteOverlay.addEventListener('click', function (event) {
                if (event.target === deleteOverlay) {
                    closeDeleteModal();
                }
            });
        }
    });
</script>

@endsection