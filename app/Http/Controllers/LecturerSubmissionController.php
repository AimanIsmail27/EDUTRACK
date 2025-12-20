<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

    protected function authorizeAssignment(Assignment $assignment): void
    {
        abort_unless($assignment->lecturer_id === Auth::id(), 403);
    }
}
