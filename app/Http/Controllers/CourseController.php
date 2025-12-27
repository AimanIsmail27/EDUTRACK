<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Course;
use App\Models\Student;
use App\Models\User;
use App\Models\AssignmentSubmission;


class CourseController extends Controller
{
    /**
     * Display all courses (Admin)
     */
    public function index(Request $request)
    {
        $query = Course::with('coordinator')->orderBy('C_Code');

        if ($request->filled('criteria')) {
            $filterBy = $request->filter_by;
            $criteria = $request->criteria;

            if ($filterBy == 'C_Code' || $filterBy == 'C_Name' || $filterBy == 'C_SemOffered') {
                $query->where($filterBy, 'like', '%' . $criteria . '%');
            }
        }

        $courses = $query->get();
        return view('M2.administrator.viewAllCourse', compact('courses'));
    }

    /**
     * Show form to create a new course
     */
    public function create()
    {
        $lecturers = User::where('role', 'lecturer')->orderBy('name')->get();
        return view('M2.administrator.addCourse', compact('lecturers'));
    }

    /**
     * Store a newly created course
     */
    public function store(Request $request)
    {
        $request->validate([
            'C_Code' => 'required|string|unique:courses,C_Code',
            'C_Name' => 'required|string|unique:courses,C_Name',
            'C_Hour' => 'required|integer',
            'C_SemOffered' => 'required|array|min:1', 
            'C_SemOffered.*' => 'integer|in:1,2,3',
            'coordinator_id' => 'required|exists:users,id',
            'lecturer_ids' => 'nullable|array',
            'lecturer_ids.*' => 'exists:users,id',
        ], [
            'C_SemOffered.required' => 'The Semester Offered field requires at least one selection.',
            'coordinator_id.required' => 'You must assign a Course Coordinator.',
        ]);

        $semestersString = implode(',', $request->C_SemOffered);

        $course = Course::create([
            'C_Code' => $request->C_Code,
            'C_Name' => $request->C_Name,
            'C_Hour' => $request->C_Hour,
            'C_Prerequisites' => $request->C_Prerequisites,
            'C_SemOffered' => $semestersString,
            'coordinator_id' => $request->coordinator_id,
            'C_Description' => $request->C_Description,
        ]);

        if ($request->has('lecturer_ids')) {
            $course->lecturers()->attach($request->lecturer_ids);
        }

        return redirect()->route('admin.viewAllCourse')
                         ->with('success', 'Course added successfully with teaching team');
    }

    /**
     * Display a specific course
     */
    public function show($code)
    {
        $course = Course::with(['participants', 'lecturers', 'coordinator'])->findOrFail($code);

        $courseParticipants = $course->participants->map(function($student) {
            return [
                'matric_id' => $student->MatricID,
                'full_name' => $student->Name,
                'semester'  => $student->pivot->semester,
                'year'      => $student->pivot->year,
            ];
        });

        $studentGrades = $course->participants->map(function($student) {
            return [
                'matric_id' => $student->MatricID,
                'full_name' => $student->Name,
                'semester'  => $student->pivot->semester,
                'quiz1'     => $student->grade->q1 ?? 0, 
                'quiz2'     => $student->grade->q2 ?? 0,
                'ia'        => $student->grade->ia ?? 0,
                'gp'        => $student->grade->gp ?? 0,
                'total'     => ($student->grade->q1 ?? 0) + ($student->grade->q2 ?? 0) + ($student->grade->ia ?? 0) + ($student->grade->gp ?? 0),
            ];
        });

        $viewPath = (auth()->user()->role === 'administrator') 
                    ? 'M2.administrator.viewSpecificCourse' 
                    : 'M2.lecturer.viewSpecificCourse';

        return view($viewPath, compact('course', 'courseParticipants', 'studentGrades'));  
    }

    /**
     * Show form to edit course
     */
    public function edit($code)
    {
        $course = Course::with('lecturers')->findOrFail($code);
        $lecturers = User::where('role', 'lecturer')->orderBy('name')->get();
        return view('M2.administrator.editCourse', compact('course', 'lecturers'));
    }

    /**
     * Update course
     */
    public function update(Request $request, $code)
    {
        $course = Course::findOrFail($code);

        $request->validate([
            'C_Name' => 'required|string|unique:courses,C_Name,' . $course->C_Code . ',C_Code',
            'C_Hour' => 'required|integer',
            'C_SemOffered' => 'required|array|min:1', 
            'C_SemOffered.*' => 'integer|in:1,2,3',
            'coordinator_id' => 'required|exists:users,id',
            'lecturer_ids' => 'nullable|array',
            'lecturer_ids.*' => 'exists:users,id',
        ]);

        $semestersString = implode(',', $request->C_SemOffered);

        $course->update([
            'C_Name' => $request->C_Name,
            'C_Hour' => $request->C_Hour,
            'C_Prerequisites' => $request->C_Prerequisites,
            'C_SemOffered' => $semestersString,
            'coordinator_id' => $request->coordinator_id,
            'C_Description' => $request->C_Description,
        ]);

        $course->lecturers()->sync($request->lecturer_ids ?? []);

        return redirect()->route('admin.viewAllCourse')
                         ->with('success', 'Course updated successfully');
    }

    /**
     * Delete course
     */
    public function destroy($code)
    {
        Course::findOrFail($code)->delete();
        return redirect()->route('admin.viewAllCourse')
                         ->with('success', 'Course deleted successfully');
    }

    // --- Student Participant Management ---

    public function searchStudents(Request $request)
    {
        try {
            $term = $request->get('term');
            $courseCode = $request->get('course_code'); 

            $enrolledMatrics = DB::table('course_student')
                ->where('course_code', $courseCode)
                ->pluck('student_matric');

            $students = DB::table('student')
                ->where(function($query) use ($term) {
                    $query->where('MatricID', 'LIKE', "%{$term}%")
                          ->orWhere('Name', 'LIKE', "%{$term}%");
                })
                ->whereNotIn('MatricID', $enrolledMatrics)
                ->limit(10)
                ->get(['MatricID', 'Name']);

            return response()->json($students->map(function($s) {
                return [
                    'matric_id' => $s->MatricID,
                    'name'      => $s->Name
                ];
            }));

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function addParticipant(Request $request, $courseCode)
    {
        $request->validate([
            'matric_id' => 'required|exists:student,MatricID', 
            'semester'  => 'required|integer|in:1,2,3',
        ]);

        try {
            $course = Course::findOrFail($courseCode);

            if ($course->participants()->where('student_matric', $request->matric_id)->exists()) {
                return redirect()->back()->with('error', 'This student is already registered.');
            }

            $course->participants()->attach($request->matric_id, [
                'semester' => $request->semester,
                'year' => date('Y') 
            ]);

            return redirect()->route('admin.courses.show', ['code' => $courseCode, 'tab' => 'participants'])
                             ->with('success', "Student added successfully.");

        } catch (\Exception $e) {
            Log::error("Failed to add participant: " . $e->getMessage());
            return redirect()->back()->with('error', 'An error occurred.');
        }
    }

    public function removeParticipant($courseCode, $matricId)
    {
        try {
            $course = Course::findOrFail($courseCode);
            $course->participants()->detach($matricId);
            return redirect()->route('admin.courses.show', ['code' => $courseCode, 'tab' => 'participants'])
                             ->with('success', "Student removed.");
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to remove student.');
        }
    }

    /**
     * Lecturer Courses
     */
    public function lecturerCourses()
    {
        $lecturerId = auth()->id();

        $courses = Course::withCount('participants')
            ->where('coordinator_id', $lecturerId)
            ->orWhereHas('lecturers', function($query) use ($lecturerId) {
                $query->where('user_id', $lecturerId);
            })
            ->get();

        return view('M2.lecturer.myCourses', compact('courses'));
    }

    /**
     * Student Courses
     */
    public function studentCourses()
    {
        $matricId = auth()->user()->matric_id;

        $student = Student::where('MatricID', $matricId)->first();

        if (!$student) {
            return view('M2.student.myCourses', ['courses' => collect()]);
        }

        $courses = $student->courses()->with('coordinator')->get();

        return view('M2.student.myCourses', compact('courses'));
    }

    /**
     * Student Course Details
     */
    public function studentCourseShow($code)
{
    $course = Course::with([
        'materials',
        'lecturers',
        'coordinator',
        'participants'
    ])->findOrFail($code);

    // Logged-in student via auth user
    $studentUser = auth()->user(); // This has id and matric_id
    $student = Student::where('MatricID', $studentUser->matric_id)->firstOrFail();

    // Enrollment check
    if (!$course->participants->contains('MatricID', $student->MatricID)) {
        abort(403, 'You are not enrolled in this course.');
    }

    /* ---------------------------------
       PARTICIPANTS TAB (UNCHANGED)
    --------------------------------- */
    $courseParticipants = $course->participants->map(function ($std) {
        return [
            'matric_id' => $std->MatricID,
            'full_name' => $std->Name,
        ];
    });

    /* ---------------------------------
       MY GRADES (REAL DATA)
    --------------------------------- */
    $submissions = AssignmentSubmission::with('assignment')
        ->where('student_id', $studentUser->id) // Use users.id, not students
        ->where('status', 'Graded')
        ->whereHas('assignment', function ($q) use ($course) {
            $q->where('course_code', $course->C_Code);
        })
        ->get();

    // Initialize grades
    $grades = [
        'quiz1' => 0,
        'quiz2' => 0,
        'ia'    => 0,
        'gp'    => 0,
    ];

    foreach ($submissions as $submission) {
        $assignment = $submission->assignment;
        if (!$assignment || $assignment->total_marks <= 0) continue;

        $percentage = ($submission->score / $assignment->total_marks) * 100;

        // Map assessment types based on title
        switch (strtolower($assignment->title)) {
            case 'quiz 1':
            case 'quiz1':
                $grades['quiz1'] = round($percentage, 2);
                break;

            case 'quiz 2':
            case 'quiz2':
                $grades['quiz2'] = round($percentage, 2);
                break;

            case 'individual assignment':
            case 'ia':
                $grades['ia'] = round($percentage, 2);
                break;

            case 'group project':
            case 'gp':
                $grades['gp'] = round($percentage, 2);
                break;
        }
    }

    // Weightage calculation
    $total =
        ($grades['quiz1'] * 0.10) +
        ($grades['quiz2'] * 0.10) +
        ($grades['ia']    * 0.30) +
        ($grades['gp']    * 0.50);

    // Structure for Blade
    $studentGrades = collect([
        [
            'matric_id' => $student->MatricID,
            'full_name' => $student->Name,
            'quiz1'     => $grades['quiz1'],
            'quiz2'     => $grades['quiz2'],
            'ia'        => $grades['ia'],
            'gp'        => $grades['gp'],
            'total'     => round($total, 2),
        ]
    ]);

    return view(
        'M2.student.viewSpecificCourse',
        compact('course', 'courseParticipants', 'studentGrades')
    );
}



    /**
     * Student Assessments
     */
    public function studentAssessments()
    {
        $matricId = auth()->user()->matric_id;

        $student = Student::where('MatricID', $matricId)->first();

        $courses = $student ? $student->courses()->withPivot('semester', 'year')->get() : collect();

        return view('M2.student.assessments', compact('courses'));
    }
}
