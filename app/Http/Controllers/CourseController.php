<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Course;
use App\Models\Student;

class CourseController extends Controller
{
    /**
     * Display all courses (Admin)
     */
    public function index(Request $request)
    {
        $query = Course::orderBy('C_Code');

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
        return view('M2.administrator.addCourse');
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
        ], [
             'C_SemOffered.required' => 'The Semester Offered field requires at least one selection.',
        ], [
            'C_Code' => 'Course Code',
            'C_Name' => 'Course Name',
            'C_Hour' => 'Credit Hour',
        ]);
        
        $semestersString = implode(',', $request->C_SemOffered);

        Course::create([
            'C_Code' => $request->C_Code,
            'C_Name' => $request->C_Name,
            'C_Hour' => $request->C_Hour,
            'C_Prerequisites' => $request->C_Prerequisites, 
            'C_SemOffered' => $semestersString,
            'C_Instructor' => $request->C_Instructor,
            'C_Description' => $request->C_Description,
        ]);

        return redirect()
            ->route('admin.viewAllCourse')
            ->with('success', 'Course added successfully');
    }

    /**
     * Display a specific course with real participants and grades
     */
    public function show($code)
    {
        $course = Course::findOrFail($code);

        // Fetch all enrolled participants
        $courseParticipants = $course->participants()->get()->map(function($student) {
            return [
                'matric_id' => $student->MatricID,
                'full_name' => $student->Name,
                'semester'  => $student->pivot->semester,
                'year'      => $student->pivot->year,
            ];
        });

        // --- GRADE LOGIC ---
        // We base the grades list on the participants list so everyone shows up.
        // In a real scenario, you would join with a 'grades' table here.
        $studentGrades = $course->participants()->get()->map(function($student) {
            // Placeholder: Attempt to find grades in your database
            // $grades = DB::table('grades')->where('student_matric', $student->MatricID)->where('course_code', $course->C_Code)->first();

            return [
                'matric_id' => $student->MatricID,
                'full_name' => $student->Name,
                'semester'  => $student->pivot->semester,
                // If grades found, use them, otherwise default to 0
                'quiz1'     => $student->grade->q1 ?? 0, 
                'quiz2'     => $student->grade->q2 ?? 0,
                'ia'        => $student->grade->ia ?? 0,
                'gp'        => $student->grade->gp ?? 0,
                'total'     => ($student->grade->q1 ?? 0) + ($student->grade->q2 ?? 0) + ($student->grade->ia ?? 0) + ($student->grade->gp ?? 0),
            ];
        });

        return view('M2.administrator.viewSpecificCourse', compact('course', 'courseParticipants', 'studentGrades'));  
    }

    /**
     * Show form to edit course
     */
    public function edit($code)
    {
        $course = Course::findOrFail($code);
        return view('M2.administrator.editCourse', compact('course'));
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
        ], [
             'C_SemOffered.required' => 'The Semester Offered field requires at least one selection.',
        ]);
        
        $semestersString = implode(',', $request->C_SemOffered);

        $course->update([
            'C_Name' => $request->C_Name,
            'C_Hour' => $request->C_Hour,
            'C_Prerequisites' => $request->C_Prerequisites,
            'C_SemOffered' => $semestersString,
            'C_Instructor' => $request->C_Instructor,
            'C_Description' => $request->C_Description,
        ]);

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

    /**
     * Live search students from the database
     */
    public function searchStudents(Request $request)
    {
        try {
            $term = $request->get('term');
            $courseCode = $request->get('course_code'); // Get the current course context

            // 1. Get a list of MatricIDs already enrolled in this specific course
            $enrolledMatrics = DB::table('course_student')
                ->where('course_code', $courseCode)
                ->pluck('student_matric');

            // 2. Query 'student' table: Match search term AND exclude enrolled students
            $students = DB::table('student')
                ->where(function($query) use ($term) {
                    $query->where('MatricID', 'LIKE', "%{$term}%")
                        ->orWhere('Name', 'LIKE', "%{$term}%");
                })
                ->whereNotIn('MatricID', $enrolledMatrics) // The "Magic" line: Exclude existing participants
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

    /**
     * Add a student to the course using the pivot table
     */
    public function addParticipant(Request $request, $courseCode)
    {
        $request->validate([
            'matric_id' => 'required|exists:student,MatricID', 
            'semester'  => 'required|integer|in:1,2,3',
        ], [
            'matric_id.exists' => 'The selected student does not exist in the records.',
        ]);

        try {
            $course = Course::findOrFail($courseCode);

            // Check if student is already in the course to prevent duplicate entries
            if ($course->participants()->where('student_matric', $request->matric_id)->exists()) {
                return redirect()->back()->with('error', 'This student is already registered for this course.');
            }

            // Attach to pivot table 'course_student'
            $course->participants()->attach($request->matric_id, [
                'semester' => $request->semester,
                'year' => date('Y') 
            ]);

            return redirect()
                ->route('admin.courses.show', ['code' => $courseCode, 'tab' => 'participants'])
                ->with('success', "Student added successfully.");

        } catch (\Exception $e) {
            Log::error("Failed to add participant: " . $e->getMessage());
            return redirect()->back()->with('error', 'An error occurred while adding the student.');
        }
    }

    public function removeParticipant($courseCode, $matricId)
    {
        try {
            $course = Course::findOrFail($courseCode);
            // This removes the entry from the 'course_student' pivot table
            $course->participants()->detach($matricId);

            return redirect()
                ->route('admin.courses.show', ['code' => $courseCode, 'tab' => 'participants'])
                ->with('success', "Student {$matricId} removed from course successfully.");
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to remove student.');
        }
    }
}