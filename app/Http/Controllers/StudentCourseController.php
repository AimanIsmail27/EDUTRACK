<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Student;
use Illuminate\Support\Facades\Auth;

class StudentCourseController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $matricId = $user?->matric_id;

        $studentProfile = null;
        $courses = collect();

        if ($matricId) {
            $studentProfile = Student::where('MatricID', $matricId)->first();
        }

        if ($studentProfile) {
            $courses = $studentProfile->courses()
                ->withCount(['participants', 'assignments'])
                ->orderBy('C_Name')
                ->get();
        } else {
            $courses = Course::query()
                ->withCount(['participants', 'assignments'])
                ->orderBy('C_Name')
                ->limit(6)
                ->get();
        }

        $stats = [
            'total_courses' => $courses->count(),
            'total_assignments' => (int) $courses->sum('assignments_count'),
            'avg_hours' => $courses->isEmpty() ? 0 : round($courses->avg('C_Hour') ?? 0, 1),
        ];

        $personalized = $studentProfile && $courses->isNotEmpty();

        return view('student.courses.index', [
            'courses' => $courses,
            'studentProfile' => $studentProfile,
            'stats' => $stats,
            'personalized' => $personalized,
        ]);
    }
}
