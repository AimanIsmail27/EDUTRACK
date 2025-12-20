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
        Assignment::closeExpired();

        $studentId = Auth::id();
        $assignments = Assignment::with(['course', 'submissions' => function ($query) use ($studentId) {
                $query->where('student_id', $studentId);
            }])
            ->whereIn('status', [
                Assignment::STATUS_PUBLISHED,
                Assignment::STATUS_SCHEDULED,
                Assignment::STATUS_CLOSED,
            ])
            ->orderBy('due_at')
            ->get();

        return view('M3.student.assignmentIndex', compact('assignments'));
    }

    public function show(Assignment $assignment)
    {
        $this->abortIfHidden($assignment);

        Assignment::closeExpired();

        $submission = $assignment->submissions()
            ->where('student_id', Auth::id())
            ->first();

        return view('M3.student.assignmentShow', compact('assignment', 'submission'));
    }

    public function submit(Request $request, Assignment $assignment)
    {
        $this->abortIfHidden($assignment);

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
            Storage::disk('public')->delete($submission->file_path);
        }

        $filePath = $request->file('attachment')->store('submissions', 'public');
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

    protected function abortIfHidden(Assignment $assignment): void
    {
        abort_if($assignment->status === Assignment::STATUS_DRAFT, 404);
    }
}
