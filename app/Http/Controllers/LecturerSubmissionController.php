<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class LecturerSubmissionController extends Controller
{
    public function index(Assignment $assignment)
    {
        $this->authorizeAssignment($assignment);

        $submissions = $assignment->submissions()
            ->with('student')
            ->orderByDesc('submitted_at')
            ->get();

        return view('M3.lecturer.assignmentSubmissions', compact('assignment', 'submissions'));
    }

    public function grade(Request $request, Assignment $assignment, AssignmentSubmission $submission)
    {
        $this->authorizeAssignment($assignment);
        abort_if($submission->assignment_id !== $assignment->id, 404);

        $data = $request->validate([
            'score' => ['required', 'integer', 'min:0', 'max:' . $assignment->total_marks],
            'feedback' => ['nullable', 'string'],
        ], [], [
            'score' => 'marks',
        ]);

        $submission->score = $data['score'];
        $submission->feedback = $data['feedback'];
        $submission->status = AssignmentSubmission::STATUS_GRADED;
        $submission->graded_at = now();
        $submission->save();

        return redirect()
            ->route('lecturer.assignments.submissions', $assignment)
            ->with('success', 'Grade updated successfully.');
    }

    public function download(Assignment $assignment, AssignmentSubmission $submission)
{
    $this->authorizeAssignment($assignment);
    abort_if($submission->assignment_id !== $assignment->id, 404);

    // Make sure there is a file
    if (!$submission->file_path) {
        return back()->with('error', 'Submission file not found.');
    }

    $path = $submission->file_path;

    // Determine which disk the file exists on
    if (Storage::disk('private')->exists($path)) {
        $disk = 'private';
    } elseif (Storage::disk('public')->exists($path)) {
        $disk = 'public';
    } else {
        return back()->with('error', 'File not found on server.');
    }

    // Use the original filename if stored, or fallback
    $filename = basename($path) ?: 'submission.pdf';

    // Return the file for download
    return Storage::disk($disk)->download($path, $filename);
}


    protected function authorizeAssignment(Assignment $assignment): void
    {
        abort_unless($assignment->lecturer_id === Auth::id(), 403);
    }
}
