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
        $studentId = Auth::id();

        $assignments = Assignment::with([
                'course',
                'submissions' => function ($query) use ($studentId) {
                    $query->where('student_id', $studentId);
                }
            ])
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
        $studentId = Auth::id();

        $assignments = Assignment::with([
                'course',
                'submissions' => function ($query) use ($studentId) {
                    $query->where('student_id', $studentId);
                }
            ])
            ->whereNotNull('due_at')
            ->orderBy('due_at')
            ->get(['id', 'title', 'course_code', 'due_at', 'total_marks']);

        $events = $assignments->map(function (Assignment $assignment) {
            $courseCode = $assignment->course_code;
            $title = $courseCode ? $assignment->title . ' · ' . $courseCode : $assignment->title;

            $submission = $assignment->submissions->first();
            $suffix = $submission ? ' (' . $submission->status . ')' : '';

            return [
                'id' => (string) $assignment->id,
                'title' => $title . $suffix,
                'start' => $assignment->due_at?->toIso8601String(),
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
