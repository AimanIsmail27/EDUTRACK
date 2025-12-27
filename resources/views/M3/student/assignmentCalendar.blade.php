@extends('layout.student')

@section('title', 'Assignment Calendar')

@section('content')
<div class="max-w-6xl mx-auto space-y-8">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <p class="text-xs font-semibold tracking-widest text-emerald-500 uppercase">Assessment Module · M3</p>
            <h1 class="text-3xl font-black text-emerald-900">Assignment Calendar</h1>
            <p class="text-emerald-900/70">See all your assignment due dates across the calendar.</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('student.assignments.index') }}" class="px-5 py-3 text-sm font-bold text-emerald-700 bg-white border border-emerald-100 rounded-2xl shadow-sm hover:shadow transition">
                Back to List
            </a>
        </div>
    </div>

    <div class="bg-white rounded-3xl shadow-lg border border-emerald-50 overflow-hidden">
        <div class="px-6 py-4 border-b border-emerald-50">
            <h2 class="text-xl font-black text-emerald-900">Calendar View</h2>
            <p class="text-sm text-gray-500">Assignments without due dates are not shown here.</p>
        </div>
        <div class="p-4 sm:p-6">
            <div id="student-assignment-calendar"></div>
        </div>
    </div>

    <div id="student-assignment-event-modal" class="fixed inset-0 z-50 hidden">
        <div id="student-assignment-event-modal-overlay" class="absolute inset-0 bg-emerald-950/40 backdrop-blur-sm"></div>
        <div class="relative mx-auto mt-24 max-w-lg px-4">
            <div class="bg-white rounded-3xl overflow-hidden border border-emerald-100 shadow-xl">
                <div class="px-6 py-4 border-b border-emerald-50 flex items-start justify-between gap-4">
                    <div>
                        <p class="text-xs font-semibold tracking-widest text-emerald-500 uppercase">Assignment Details</p>
                        <h3 id="student-modal-title" class="text-xl font-black text-emerald-900"></h3>
                        <p id="student-modal-course" class="text-sm text-gray-500"></p>
                    </div>
                    <button id="student-assignment-event-modal-close" type="button" class="px-3 py-2 text-sm font-bold text-emerald-700 bg-emerald-50 rounded-2xl border border-emerald-100 hover:bg-white">Close</button>
                </div>

                <div class="px-6 py-5 space-y-3 text-sm">
                    <div class="flex items-center justify-between">
                        <span class="font-semibold text-gray-600">Due</span>
                        <span id="student-modal-due" class="font-bold text-emerald-900"></span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="font-semibold text-gray-600">Total Marks</span>
                        <span id="student-modal-marks" class="font-bold text-emerald-900"></span>
                    </div>
                </div>

                <div class="px-6 py-5 border-t border-emerald-50 flex items-center justify-end gap-2">
                    <a id="student-modal-view" href="#" class="inline-flex items-center justify-center px-5 py-3 text-sm font-bold text-white bg-emerald-600 rounded-2xl shadow-lg shadow-emerald-300/60 hover:bg-emerald-700">View Assignment</a>
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
            const calendarEl = document.getElementById('student-assignment-calendar');
            if (!calendarEl) return;

            const modalEl = document.getElementById('student-assignment-event-modal');
            const modalOverlayEl = document.getElementById('student-assignment-event-modal-overlay');
            const modalCloseEl = document.getElementById('student-assignment-event-modal-close');
            const modalTitleEl = document.getElementById('student-modal-title');
            const modalCourseEl = document.getElementById('student-modal-course');
            const modalDueEl = document.getElementById('student-modal-due');
            const modalMarksEl = document.getElementById('student-modal-marks');
            const modalViewEl = document.getElementById('student-modal-view');

            const closeModal = () => {
                if (modalEl) modalEl.classList.add('hidden');
            };

            const openModal = (event) => {
                if (!modalEl) return;

                const props = event.extendedProps || {};

                if (modalTitleEl) modalTitleEl.textContent = props.assignmentTitle || event.title || 'Assignment';
                if (modalCourseEl) {
                    const courseBits = [props.courseName, props.courseCode].filter(Boolean);
                    modalCourseEl.textContent = courseBits.length ? courseBits.join(' • ') : '';
                }
                
                if (modalDueEl) {
                    // Match the UTC string from the controller exactly
                    modalDueEl.textContent = event.start
                        ? event.start.toLocaleString('en-GB', { 
                            year: 'numeric', month: 'short', day: '2-digit', 
                            hour: '2-digit', minute: '2-digit', hour12: true,
                            timeZone: 'UTC' 
                        })
                        : '—';
                }
                
                if (modalMarksEl) modalMarksEl.textContent = props.totalMarks ? `${props.totalMarks} marks` : '—';
                if (modalViewEl) modalViewEl.href = props.viewUrl || '#';

                modalEl.classList.remove('hidden');
            };

            if (modalCloseEl) modalCloseEl.addEventListener('click', closeModal);
            if (modalOverlayEl) modalOverlayEl.addEventListener('click', closeModal);
            document.addEventListener('keydown', function (e) {
                if (e.key === 'Escape') closeModal();
            });

            const calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                timeZone: 'UTC', // Treat all strings as wall-clock time
                nextDayThreshold: '00:00:00', // Prevents 11:30 PM from showing on the next day
                height: 'auto',
                dayMaxEventRows: true,
                
                // Allow zero-duration deadlines to show as a marker in Week/Day views
                forceEventDuration: true, 
                defaultTimedEventDuration: '00:01:00',
                displayEventEnd: false, // Only show "11:30 PM", not "11:30-11:31"

                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay'
                },
                events: {
                    url: "{{ route('student.assignments.calendar.events') }}",
                    method: 'GET',
                    failure: function () {
                        calendarEl.innerHTML = '<div class="p-4 text-sm font-semibold text-rose-700 bg-rose-50 border border-rose-100 rounded-2xl">Failed to load calendar events.</div>';
                    }
                },
                eventDidMount: function (info) {
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
                    meridiem: 'short',
                    timeZone: 'UTC'
                }
            });

            calendar.render();
        });
    </script>
@endpush