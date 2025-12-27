<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class StudentAssignmentController extends Controller
{
    public function index()
{
    // Get the logged-in student's MatricID from the authenticated user
    $matricId = Auth::user()->matric_id;

    // Find the student record
    $student = \App\Models\Student::where('MatricID', $matricId)->first();

    if (!$student) {
        abort(403, 'Student profile not found.');
    }

    $assignments = Assignment::with([
            'course',
            'submissions' => fn($query) => $query->where('student_id', $student->id)
        ])
        ->whereHas('course.participants', fn($q) => $q->where('student_matric', $matricId))
        ->orderBy('due_at')
        ->get();

    return view('M3.student.assignmentIndex', compact('assignments'));
}


    public function show(Assignment $assignment)
    {
        $submission = $assignment->submissions()
            ->where('student_id', Auth::id())
            ->first();

        $pastDue = $assignment->due_at && Carbon::now()->greaterThan($assignment->due_at);

        return view('M3.student.assignmentShow', compact('assignment', 'submission', 'pastDue'));
    }

    public function submit(Request $request, Assignment $assignment)
    {
        if ($assignment->due_at && Carbon::now()->greaterThan($assignment->due_at)) {
            return redirect()
                ->route('student.assignments.show', $assignment)
                ->with('error', 'Submission closed. The due date has passed.');
        }

        $request->validate([
            'attachment' => ['required', 'file', 'mimes:pdf', 'max:10240'],
        ], [], [
            'attachment' => 'submission file',
        ]);

        $studentId = Auth::id();

        $submission = AssignmentSubmission::firstOrNew([
            'assignment_id' => $assignment->id,
            'student_id' => $studentId,
        ]);

        if ($submission->exists && $submission->file_path) {
            // Prefer removing from private, fall back to public in case of legacy files.
            if (!Storage::disk('private')->delete($submission->file_path)) {
                Storage::disk('public')->delete($submission->file_path);
            }
        }

        $filePath = $request->file('attachment')->store('submissions', 'private');
        $isLate = $assignment->due_at && Carbon::now()->greaterThan($assignment->due_at);

        $submission->fill([
            'file_path' => $filePath,
            'status' => $isLate ? AssignmentSubmission::STATUS_LATE : AssignmentSubmission::STATUS_SUBMITTED,
            'submitted_at' => now(),
            'score' => null,
            'feedback' => null,
            'graded_at' => null,
        ]);
        $submission->save();

        return redirect()
            ->route('student.assignments.show', $assignment)
            ->with('success', 'Assignment submitted successfully.');
    }

    public function destroySubmission(Assignment $assignment)
    {
        $submission = $assignment->submissions()
            ->where('student_id', Auth::id())
            ->first();

        if (!$submission) {
            return redirect()
                ->route('student.assignments.show', $assignment)
                ->with('error', 'You have no submission to delete.');
        }

        if ($submission->score !== null) {
            return redirect()
                ->route('student.assignments.show', $assignment)
                ->with('error', 'This submission has already been graded and cannot be removed.');
        }

        if ($assignment->due_at && Carbon::now()->greaterThan($assignment->due_at)) {
            return redirect()
                ->route('student.assignments.show', $assignment)
                ->with('error', 'Submission window closed. You cannot delete it now.');
        }

        if ($submission->file_path) {
            if (!Storage::disk('private')->delete($submission->file_path)) {
                Storage::disk('public')->delete($submission->file_path);
            }
        }

        $submission->delete();

        return redirect()
            ->route('student.assignments.show', $assignment)
            ->with('success', 'Submission removed. You can upload a new file.');
    }

    public function downloadBrief(Assignment $assignment)
    {
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

    public function downloadSubmission(Assignment $assignment)
    {
        $submission = $assignment->submissions()
            ->where('student_id', Auth::id())
            ->first();

        if (!$submission || !$submission->file_path) {
            abort(404);
        }

        $path = $submission->file_path;
        $disk = Storage::disk('private')->exists($path) ? 'private' : (Storage::disk('public')->exists($path) ? 'public' : null);
        if (!$disk) {
            abort(404);
        }

        $filename = basename($path) ?: 'submission.pdf';
        return Storage::disk($disk)->download($path, $filename);
    }

    public function calendar()
    {
        return view('M3.student.assignmentCalendar');
    }

    public function calendarEvents()
{
    // 1. Get the matric_id from the Auth user
    $user = Auth::user();
    $matricId = $user->matric_id;

    // 2. Find the student record to get the internal ID for submissions
    $student = \App\Models\Student::where('MatricID', $matricId)->first();

    if (!$student) {
        return response()->json([]); // Return empty if student doesn't exist
    }

    // 3. Query assignments based on course participants (enrollment)
    $assignments = Assignment::with([
            'course',
            'submissions' => function ($query) use ($student) {
                $query->where('student_id', $student->id);
            }
        ])
        // Filter: Only assignments from courses where this student is a participant
        ->whereHas('course.participants', function($q) use ($matricId) {
            $q->where('student_matric', $matricId);
        })
        ->whereNotNull('due_at')
        ->get(['id', 'title', 'course_code', 'due_at', 'total_marks']);

    // 4. Map events for FullCalendar
    $events = $assignments->map(function (Assignment $assignment) {
        $courseCode = $assignment->course_code;
        $title = $courseCode ? $assignment->title . ' · ' . $courseCode : $assignment->title;

        $submission = $assignment->submissions->first();
        $suffix = $submission ? ' (' . $submission->status . ')' : '';

        // FIX: Use 'T' format to match the wall-clock time in DB (prevents timezone shifts)
        $start = $assignment->due_at ? $assignment->due_at->format('Y-m-d\TH:i:s') : null;

        return [
            'id' => (string) $assignment->id,
            'title' => $title . $suffix,
            'start' => $start,
            'end' => $start, // Forces point-in-time deadline
            'allDay' => false,
            'extendedProps' => [
                'assignmentTitle' => $assignment->title,
                'courseCode' => $courseCode,
                'courseName' => optional($assignment->course)->C_Name,
                'totalMarks' => $assignment->total_marks,
                'viewUrl' => route('student.assignments.show', $assignment),
            ],
        ];
    })->values();

    return response()->json($events);
}
}
