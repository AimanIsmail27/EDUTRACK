<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class AssignmentController extends Controller
{
    public function index()
    {
        Assignment::closeExpired(Auth::id());

        $assignments = Assignment::with('course')
            ->withCount('submissions')
            ->where('lecturer_id', Auth::id())
            ->orderBy('due_at')
            ->get();

        return view('M3.lecturer.assignmentDashboard', compact('assignments'));
    }

    public function create()
    {
        $courses = Course::orderBy('C_Name')->get(['C_Code', 'C_Name']);
        $assignment = new Assignment([
            'status' => Assignment::STATUS_DRAFT,
            'total_marks' => 100,
        ]);

        $statuses = Assignment::editableStatuses();

        return view('M3.lecturer.createAssignment', compact('courses', 'assignment', 'statuses'));
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
        $courses = Course::orderBy('C_Name')->get(['C_Code', 'C_Name']);
        $statuses = Assignment::editableStatuses();

        return view('M3.lecturer.editAssignment', compact('assignment', 'courses', 'statuses'));
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

    protected function validatedData(Request $request): array
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'instructions' => ['nullable', 'string'],
            'course_code' => ['required', 'exists:courses,C_Code'],
            'due_at' => ['nullable', 'date'],
            'total_marks' => ['required', 'integer', 'min:1', 'max:1000'],
            'status' => ['required', Rule::in(Assignment::editableStatuses())],
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

        return $validated;
    }

    protected function handleAttachment(Request $request, array $data, ?Assignment $assignment = null): array
    {
        if ($request->hasFile('attachment')) {
            if ($assignment && $assignment->attachment_path) {
                Storage::disk('public')->delete($assignment->attachment_path);
            }

            $data['attachment_path'] = $request->file('attachment')->store('assignments', 'public');
        }

        return $data;
    }

    protected function deleteAttachment(Assignment $assignment): void
    {
        if ($assignment->attachment_path) {
            Storage::disk('public')->delete($assignment->attachment_path);
        }
    }

    protected function authorizeAssignment(Assignment $assignment): void
    {
        abort_unless($assignment->lecturer_id === Auth::id(), 403);
    }
}
