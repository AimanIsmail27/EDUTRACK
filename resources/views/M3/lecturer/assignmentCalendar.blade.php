@extends('layout.lecturer')

@section('title', 'Assessment Calendar')

@section('content')
<div class="max-w-6xl mx-auto space-y-8">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <p class="text-xs font-semibold tracking-widest text-indigo-500 uppercase">Assessment Module · M3</p>
            <h1 class="text-3xl font-black text-indigo-900">Assessment Calendar</h1>
            <p class="text-indigo-900/60">See assessment due dates across the calendar to understand the distribution before creating a new assessment.</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('lecturer.assignments.index') }}" class="px-4 py-2 text-sm font-semibold text-indigo-600 bg-white border border-indigo-100 rounded-2xl shadow-sm hover:shadow transition">
                Back to List
            </a>
            <a href="{{ route('lecturer.assignments.create') }}" class="px-5 py-3 text-sm font-bold text-white bg-indigo-600 rounded-2xl shadow-lg shadow-indigo-400/40 hover:bg-indigo-700 transition">
                Create New Assessment
            </a>
        </div>
    </div>

    <div class="strong-card rounded-3xl overflow-hidden">
        <div class="px-6 py-4 border-b border-indigo-50">
            <h2 class="text-xl font-black text-indigo-900">Calendar View</h2>
            <p class="text-sm text-gray-500">Assignments without due dates are not shown here.</p>
        </div>
        <div class="p-4 sm:p-6">
            <div id="assignment-calendar"></div>
        </div>
    </div>

    <div id="assignment-event-modal" class="fixed inset-0 z-50 hidden">
        <div id="assignment-event-modal-overlay" class="absolute inset-0 bg-indigo-950/50 backdrop-blur-sm"></div>
        <div class="relative mx-auto mt-24 max-w-lg px-4">
            <div class="strong-card rounded-3xl overflow-hidden border border-indigo-100">
                <div class="px-6 py-4 border-b border-indigo-50 flex items-start justify-between gap-4">
                    <div>
                        <p class="text-xs font-semibold tracking-widest text-indigo-500 uppercase">Assessment Details</p>
                        <h3 id="modal-title" class="text-xl font-black text-indigo-900"></h3>
                        <p id="modal-course" class="text-sm text-gray-500"></p>
                    </div>
                    <button id="assignment-event-modal-close" type="button" class="px-3 py-2 text-sm font-bold text-indigo-700 bg-indigo-50 rounded-2xl border border-indigo-100 hover:bg-white">Close</button>
                </div>

                <div class="px-6 py-5 space-y-3 text-sm">
                    <div class="flex items-center justify-between">
                        <span class="font-semibold text-gray-600">Due</span>
                        <span id="modal-due" class="font-bold text-indigo-900"></span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="font-semibold text-gray-600">Total Marks</span>
                        <span id="modal-marks" class="font-bold text-indigo-900"></span>
                    </div>
                </div>

                <div class="px-6 py-5 border-t border-indigo-50 flex items-center justify-end gap-2">
                    <a id="modal-submissions" href="#" class="inline-flex items-center justify-center px-4 py-2 text-xs font-bold text-sky-600 bg-sky-50 rounded-2xl border border-sky-100 hover:bg-white">Submissions</a>
                    <a id="modal-edit" href="#" class="inline-flex items-center justify-center px-4 py-2 text-xs font-bold text-violet-600 bg-violet-50 rounded-2xl border border-violet-100 hover:bg-white">Edit</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.css">
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const calendarEl = document.getElementById('assignment-calendar');
            if (!calendarEl) return;

            const modalEl = document.getElementById('assignment-event-modal');
            const modalOverlayEl = document.getElementById('assignment-event-modal-overlay');
            const modalCloseEl = document.getElementById('assignment-event-modal-close');
            const modalTitleEl = document.getElementById('modal-title');
            const modalCourseEl = document.getElementById('modal-course');
            const modalDueEl = document.getElementById('modal-due');
            const modalMarksEl = document.getElementById('modal-marks');
            const modalEditEl = document.getElementById('modal-edit');
            const modalSubmissionsEl = document.getElementById('modal-submissions');

            const closeModal = () => {
                if (!modalEl) return;
                modalEl.classList.add('hidden');
            };

            const openModal = (event) => {
                if (!modalEl) return;

                const props = event.extendedProps || {};

                if (modalTitleEl) modalTitleEl.textContent = props.assignmentTitle || event.title || 'Assessment';
                if (modalCourseEl) {
                    const courseBits = [props.courseName, props.courseCode].filter(Boolean);
                    modalCourseEl.textContent = courseBits.length ? courseBits.join(' · ') : '';
                }
                if (modalDueEl) {
                    modalDueEl.textContent = event.start
                        ? event.start.toLocaleString(undefined, { year: 'numeric', month: 'short', day: '2-digit', hour: '2-digit', minute: '2-digit' })
                        : '—';
                }
                if (modalMarksEl) modalMarksEl.textContent = props.totalMarks ? `${props.totalMarks} marks` : '—';
                if (modalEditEl) modalEditEl.href = props.editUrl || '#';
                if (modalSubmissionsEl) modalSubmissionsEl.href = props.submissionsUrl || '#';

                modalEl.classList.remove('hidden');
            };

            if (modalCloseEl) modalCloseEl.addEventListener('click', closeModal);
            if (modalOverlayEl) modalOverlayEl.addEventListener('click', closeModal);
            document.addEventListener('keydown', function (e) {
                if (e.key === 'Escape') closeModal();
            });

            const calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                height: 'auto',
                dayMaxEventRows: true,
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay'
                },
                events: {
                    url: "{{ route('lecturer.assignments.calendar.events') }}",
                    method: 'GET',
                    failure: function () {
                        calendarEl.innerHTML = '<div class="p-4 text-sm font-semibold text-rose-700 bg-rose-50 border border-rose-100 rounded-2xl">Failed to load calendar events.</div>';
                    }
                },
                eventDidMount: function (info) {
                    // Native hover tooltip for long titles.
                    info.el.title = info.event.title;
                    info.el.style.cursor = 'pointer';
                },
                eventClick: function (info) {
                    info.jsEvent.preventDefault();
                    openModal(info.event);
                },
                eventTimeFormat: {
                    hour: '2-digit',
                    minute: '2-digit',
                    meridiem: true
                }
            });

            calendar.render();
        });
    </script>
@endpush
