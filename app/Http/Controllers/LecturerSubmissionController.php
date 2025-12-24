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

        if (!$submission->file_path) {
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

    protected function authorizeAssignment(Assignment $assignment): void
    {
        abort_unless($assignment->lecturer_id === Auth::id(), 403);
    }
}
