<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Course;
use App\Models\Student;
use App\Models\User; // Added to fetch lecturers

class CourseController extends Controller
{
    /**
     * Display all courses (Admin)
     */
    public function index(Request $request)
    {
        // Added eager loading 'coordinator' to prevent N+1 query issues
        $query = Course::with('coordinator')->orderBy('C_Code');

        if ($request->filled('criteria')) {
            $filterBy = $request->filter_by;
            $criteria = $request->criteria;

            if ($filterBy == 'C_Code' || $filterBy == 'C_Name') {
                $query->where($filterBy, 'like', '%' . $criteria . '%');
            } elseif ($filterBy == 'C_SemOffered') {
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
        // Fetch all users with the role 'lecturer' for the dropdown and multi-select
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
            'coordinator_id' => 'required|exists:users,id', // Validate Coordinator
            'lecturer_ids' => 'nullable|array', // Validate Teaching Team
            'lecturer_ids.*' => 'exists:users,id',
        ], [
             'C_SemOffered.required' => 'The Semester Offered field requires at least one selection.',
             'coordinator_id.required' => 'You must assign a Course Coordinator.',
        ]);
        
        $semestersString = implode(',', $request->C_SemOffered);

        // 1. Create the course record
        $course = Course::create([
            'C_Code' => $request->C_Code,
            'C_Name' => $request->C_Name,
            'C_Hour' => $request->C_Hour,
            'C_Prerequisites' => $request->C_Prerequisites, 
            'C_SemOffered' => $semestersString,
            'coordinator_id' => $request->coordinator_id, // New Field
            'C_Description' => $request->C_Description,
        ]);

        // 2. Attach involved lecturers to the pivot table
        if ($request->has('lecturer_ids')) {
            $course->lecturers()->attach($request->lecturer_ids);
        }

        return redirect()
            ->route('admin.viewAllCourse')
            ->with('success', 'Course added successfully with teaching team');
    }

    /**
     * Display a specific course
     */
    /**
     * Display a specific course (Shared by Admin and Lecturer)
     */
    public function show($code)
    {
        // 1. Eager load relationships to ensure we have coordinator and teaching team data
        $course = Course::with(['participants', 'lecturers', 'coordinator'])->findOrFail($code);

        // 2. Map participants data
        $courseParticipants = $course->participants->map(function($student) {
            return [
                'matric_id' => $student->MatricID,
                'full_name' => $student->Name,
                'semester'  => $student->pivot->semester,
                'year'      => $student->pivot->year,
            ];
        });

        // 3. Map grade data (Assuming 'grade' relationship exists on Student model)
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

        // 4. Dynamic View Logic: Switch between Admin and Lecturer layouts
        // If the user is an administrator, show the admin view (with enroll/delete buttons)
        // Otherwise, show the lecturer view (cleaner, no enroll/delete buttons)
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

        // Sync the involved lecturers (removes old ones, adds new ones)
        $course->lecturers()->sync($request->lecturer_ids ?? []);

        return redirect()
            ->route('admin.viewAllCourse')
            ->with('success', 'Course updated successfully');
    }

    /**
     * Delete course
     */
    public function destroy($code)
    {
        Course::findOrFail($code)->delete();
        return redirect()
            ->route('admin.viewAllCourse')
            ->with('success', 'Course deleted successfully');
    }

    // --- Student Participant Management (Existing Logic) ---

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

            $formatted = $students->map(function($s) {
                return [
                    'matric_id' => $s->MatricID,
                    'name'      => $s->Name
                ];
            });

            return response()->json($formatted);
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

            return redirect()
                ->route('admin.courses.show', ['code' => $courseCode, 'tab' => 'participants'])
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
            return redirect()
                ->route('admin.courses.show', ['code' => $courseCode, 'tab' => 'participants'])
                ->with('success', "Student removed.");
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to remove student.');
        }
    }

    /**
     * Real Data Fetching for Lecturer Dashboard
     */
    public function lecturerCourses()
    {
        $lecturerId = auth()->id();

        // Fetch courses where user is Coordinator OR in the Teaching Team
        // Use withCount to get participant statistics automatically
        $courses = Course::withCount('participants')
            ->where('coordinator_id', $lecturerId)
            ->orWhereHas('lecturers', function($query) use ($lecturerId) {
                $query->where('user_id', $lecturerId);
            })
            ->get();

        return view('M2.lecturer.myCourses', compact('courses'));
    }

    public function studentCourses()
    {
        return view('M2.student.myCourses');
    }

    public function studentAssessments()
    {
        return view('M2.student.assessments');
    }
}