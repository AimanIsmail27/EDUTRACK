<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Course;
use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function studentDashboard()
    {
        $student = Auth::user()->student;

        if (!$student) {
            return redirect()->route('dashboard.student')->with('error', 'Student record not found.');
        }

        // 1. Enrolled courses
        $courses = $student->courses ?? collect();

        // 2. Get all assignment IDs for enrolled courses
        $assignmentIds = Assignment::whereIn('course_code', $courses->pluck('C_Code'))->pluck('id');

        // 3. Completed assignments for this student
        $completedAssignments = $assignmentIds->isNotEmpty()
            ? AssignmentSubmission::where('student_id', $student->id)
                ->where('status', 'Graded')
                ->whereIn('assignment_id', $assignmentIds)
                ->count()
            : 0;

        // 4. Total assignments for enrolled courses
        $totalAssignments = $assignmentIds->count();

        // 5. Average grade across all graded submissions
        $averageGrade = $assignmentIds->isNotEmpty()
            ? AssignmentSubmission::where('student_id', $student->id)
                ->where('status', 'Graded')
                ->whereIn('assignment_id', $assignmentIds)
                ->avg('score') ?? 0
            : 0;

        // 6. Course progress per course
        $courseProgress = $courses->map(function ($course) use ($student) {
            $totalAssignments = $course->assignments->count();

            $completedAssignments = $totalAssignments > 0
                ? AssignmentSubmission::where('student_id', $student->id)
                    ->where('status', 'Graded')
                    ->whereIn('assignment_id', $course->assignments->pluck('id'))
                    ->count()
                : 0;

            return [
                'name' => $course->C_Name,
                'progress' => $totalAssignments > 0 ? round(($completedAssignments / $totalAssignments) * 100) : 0,
            ];
        });

        // 7. Upcoming deadlines (next 5)
        $upcomingDeadlines = Assignment::whereIn('course_code', $courses->pluck('C_Code'))
            ->where('due_at', '>=', Carbon::now())
            ->orderBy('due_at', 'asc')
            ->take(5)
            ->get();

        return view('dashboard.student', compact(
            'student',
            'courses',
            'completedAssignments',
            'totalAssignments',
            'averageGrade',
            'courseProgress',
            'upcomingDeadlines'
        ));
    }

    public function lecturerDashboard()
{
    $user = Auth::user();
    $lecturer = $user->lecturer;

    if (!$lecturer) {
        abort(403, 'Lecturer profile not found.');
    }

    $courses = $user->teachingCourses()->with(['participants', 'assignments.submissions'])->get();
    $totalStudents = $courses->sum(fn($course) => $course->participants->count());
    $ungradedItems = $courses->flatMap(fn($course) => 
        $course->assignments->flatMap(fn($assignment) => 
            $assignment->submissions->where('status', 'Submitted')
        )
    )->count();

    // --- Upcoming Assignment Deadlines ---
    $upcomingAssignments = $courses->flatMap(fn($course) =>
        $course->assignments->filter(fn($assignment) =>
            Carbon::parse($assignment->due_at)->gte(Carbon::now())
        )->map(fn($assignment) => [
            'course_name' => $course->C_Name,
            'title' => $assignment->title,
            'due_at' => $assignment->due_at,
        ])
    )->sortBy('due_at')->take(10); // top 10 upcoming deadlines

    return view('dashboard.lecturer', compact(
        'lecturer',
        'courses',
        'totalStudents',
        'ungradedItems',
        'upcomingAssignments'
    ));
}


}
