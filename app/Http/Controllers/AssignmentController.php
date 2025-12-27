<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AssignmentController extends Controller
{
    public function index()
    {
        $lecturerId = Auth::id();

        $assignments = Assignment::with('course')
            ->withCount('submissions')
            ->whereHas('course', function($query) use ($lecturerId) {
                $query->where('coordinator_id', $lecturerId)
                    ->orWhereHas('lecturers', fn($q) => $q->where('user_id', $lecturerId));
            })
            ->orderBy('due_at')
            ->get();


        return view('M3.lecturer.assignmentDashboard', compact('assignments'));
    }

    public function create()
    {
        $lecturerId = Auth::id();
        $courses = Course::where('coordinator_id', $lecturerId)
            ->orWhereHas('lecturers', fn($q) => $q->where('user_id', $lecturerId))
            ->orderBy('C_Name')
            ->get(['C_Code', 'C_Name']);

        $assignment = new Assignment([
            'total_marks' => 100,
        ]);

        return view('M3.lecturer.createAssignment', compact('courses', 'assignment'));
    }

    public function store(Request $request)
    {
        $data = $this->validatedData($request);
        $data['lecturer_id'] = Auth::id();
        $data = $this->handleAttachment($request, $data);

        Assignment::create($data);

        return redirect()
            ->route('lecturer.assignments.index')
            ->with('success', 'Assignment created successfully.');
    }

    public function edit(Assignment $assignment)
    {
        $this->authorizeAssignment($assignment);
        $lecturerId = Auth::id();
        $courses = Course::where('coordinator_id', $lecturerId)
            ->orWhereHas('lecturers', fn($q) => $q->where('user_id', $lecturerId))
            ->orderBy('C_Name')
            ->get(['C_Code', 'C_Name']);


        return view('M3.lecturer.editAssignment', compact('assignment', 'courses'));
    }

    public function update(Request $request, Assignment $assignment)
    {
        $this->authorizeAssignment($assignment);
        $data = $this->validatedData($request);
        $data = $this->handleAttachment($request, $data, $assignment);
        $assignment->update($data);

        return redirect()
            ->route('lecturer.assignments.index')
            ->with('success', 'Assignment updated successfully.');
    }

    public function destroy(Assignment $assignment)
    {
        $this->authorizeAssignment($assignment);
        $this->deleteAttachment($assignment);
        $assignment->delete();

        return redirect()
            ->route('lecturer.assignments.index')
            ->with('success', 'Assignment deleted successfully.');
    }

    public function calendar()
    {
        return view('M3.lecturer.assignmentCalendar');
    }

    public function calendarEvents()
{
    $lecturerId = Auth::id();
    $assignments = Assignment::with('course')
        ->whereHas('course', fn($q) => $q->where('coordinator_id', $lecturerId)
            ->orWhereHas('lecturers', fn($q2) => $q2->where('user_id', $lecturerId)))
        ->whereNotNull('due_at')
        ->get(['id', 'title', 'course_code', 'due_at', 'total_marks']);

    $events = $assignments->map(function (Assignment $assignment) {
        $courseCode = $assignment->course_code;
        $title = $courseCode ? $assignment->title . ' · ' . $courseCode : $assignment->title;
        $start = $assignment->due_at ? $assignment->due_at->format('Y-m-d\TH:i:s') : null;

        return [
            'id' => (string) $assignment->id,
            'title' => $title,
            'start' => $start,
            'end' => $start, // Back to zero duration to avoid confusion
            'allDay' => false,
            // 'list-item' helps it show up in time-grids as a dot + text 
            // instead of a stretching block
            'display' => 'list-item', 
            'extendedProps' => [
                'assignmentTitle' => $assignment->title,
                'courseCode' => $courseCode,
                'courseName' => optional($assignment->course)->C_Name,
                'totalMarks' => $assignment->total_marks,
                'editUrl' => route('lecturer.assignments.edit', $assignment),
                'submissionsUrl' => route('lecturer.assignments.submissions', $assignment),
            ],
        ];
    })->values();

    return response()->json($events);
}

    public function downloadBrief(Assignment $assignment)
    {
        $this->authorizeAssignment($assignment);

        if (!$assignment->attachment_path) {
            abort(404);
        }

        $path = $assignment->attachment_path;
        $disk = Storage::disk('private')->exists($path) ? 'private' : (Storage::disk('public')->exists($path) ? 'public' : null);
        if (!$disk) {
            abort(404);
        }

        $filename = basename($path) ?: 'assignment.pdf';
        return Storage::disk($disk)->download($path, $filename);
    }

    protected function validatedData(Request $request): array
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'instructions' => ['nullable', 'string'],
            'course_code' => ['required', 'exists:courses,C_Code'],
            'due_at' => ['nullable', 'date'],
            'total_marks' => ['required', 'integer', 'min:1', 'max:1000'],
            'attachment' => ['nullable', 'file', 'mimes:pdf', 'max:5120'],
        ], [], [
            'course_code' => 'course',
            'due_at' => 'due date',
            'total_marks' => 'total marks',
            'attachment' => 'assignment brief',
        ]);

        $validated['due_at'] = $validated['due_at']
            ? Carbon::parse($validated['due_at'])
            : null;

        // Always treat new/updated assignments as published.
        $validated['status'] = 'Published';

        return $validated;
    }

    protected function handleAttachment(Request $request, array $data, ?Assignment $assignment = null): array
    {
        if ($request->hasFile('attachment')) {
            if ($assignment && $assignment->attachment_path) {
                if (!Storage::disk('private')->delete($assignment->attachment_path)) {
                    Storage::disk('public')->delete($assignment->attachment_path);
                }
            }

            $data['attachment_path'] = $request->file('attachment')->store('assignments', 'private');
        }

        return $data;
    }

    protected function deleteAttachment(Assignment $assignment): void
    {
        if ($assignment->attachment_path) {
            if (!Storage::disk('private')->delete($assignment->attachment_path)) {
                Storage::disk('public')->delete($assignment->attachment_path);
            }
        }
    }

    protected function authorizeAssignment(Assignment $assignment): void
    {
        abort_unless($assignment->lecturer_id === Auth::id(), 403);
    }
}
