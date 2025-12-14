<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Course;

class CourseController extends Controller
{
    /**
     * Display all courses (Admin)
     */
    public function index(Request $request)
    {
        // Start building the query
        $query = Course::orderBy('C_Code');

        // Check if a search criteria has been submitted
        if ($request->filled('criteria')) {
            $filterBy = $request->filter_by;
            $criteria = $request->criteria;

            // Apply the filter dynamically based on $filterBy
            if ($filterBy == 'C_Code' || $filterBy == 'C_Name') {
                // Use 'like' for partial text matching on Code and Name
                $query->where($filterBy, 'like', '%' . $criteria . '%');
            } elseif ($filterBy == 'C_SemOffered') {
                // *** FIX APPLIED HERE ***
                // Now uses LIKE to find the criteria within the comma-separated string.
                // e.g., criteria '3' will match '1,2,3', '3', or '3,1'.
                $query->where($filterBy, 'like', '%' . $criteria . '%');
            }
        }

        // Execute the final query and retrieve the courses
        $courses = $query->get();

        // Pass the filtered (or unfiltered) courses to the view
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
        // 1. VALIDATION RULES
        $request->validate([
            // Enforce uniqueness for both Code and Name
            'C_Code' => 'required|string|unique:courses,C_Code',
            'C_Name' => 'required|string|unique:courses,C_Name',
            'C_Hour' => 'required|integer',
            
            // C_SemOffered MUST be an array from checkboxes, must have min 1 selection, 
            // and all items must be valid integers (1, 2, or 3)
            'C_SemOffered' => 'required|array|min:1', 
            'C_SemOffered.*' => 'integer|in:1,2,3',
        ],

        // 2. CUSTOM MESSAGES (For array specific errors)
        [
             'C_SemOffered.required' => 'The Semester Offered field requires at least one selection.',
             'C_SemOffered.min' => 'The Semester Offered field requires at least one selection.',
             'C_SemOffered.*.in' => 'Selected semester(s) must be 1, 2, or 3.',
        ], 

        // 3. CUSTOM ATTRIBUTES (Fixes user-friendly names in error messages)
        [
            'C_Code' => 'Course Code',
            'C_Name' => 'Course Name',
            'C_Hour' => 'Credit Hour',
            'C_SemOffered' => 'Semester Offered',
            'C_Prerequisites' => 'Prerequisites',
            'C_Instructor' => 'Instructor Name',
            'C_Description' => 'Course Description',
        ]);
        
        // CONVERSION STEP: Convert the array of semester IDs into a comma-separated string 
        // for storage in the VARCHAR column.
        $semestersString = implode(',', $request->C_SemOffered);

        Course::create([
            'C_Code' => $request->C_Code,
            'C_Name' => $request->C_Name,
            'C_Hour' => $request->C_Hour,
            // Assuming C_Prerequisites and others are nullable if not provided
            'C_Prerequisites' => $request->C_Prerequisites, 
            'C_SemOffered' => $semestersString, // Store the CSV string
            'C_Instructor' => $request->C_Instructor,
            'C_Description' => $request->C_Description,
        ]);

        return redirect()
            ->route('admin.viewAllCourse')
            ->with('success', 'Course added successfully');
    }

    /**
     * Display a specific course
     */
    public function show($code)
    {
        $course = Course::findOrFail($code);

return view('M2.administrator.viewSpecificCourse', compact('course'));  
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
    
    // 1. VALIDATION RULES
    $request->validate([
        // C_Name must be unique to all OTHER courses (it ignores the current course ID/Code)
        'C_Name' => 'required|string|unique:courses,C_Name,' . $course->C_Code . ',C_Code',
        'C_Hour' => 'required|integer',
        // Update C_SemOffered validation to handle array/checkboxes
        'C_SemOffered' => 'required|array|min:1', 
        'C_SemOffered.*' => 'integer|in:1,2,3',
        // Include optional fields for validation if necessary, but keep as simple 'string' if data exists
        'C_Prerequisites' => 'nullable|string', 
        'C_Instructor' => 'nullable|string',
        'C_Description' => 'nullable|string',
    ],
    // 2. CUSTOM MESSAGES (For array specific errors, Array 2)
    [
         'C_SemOffered.required' => 'The Semester Offered field requires at least one selection.',
         'C_SemOffered.min' => 'The Semester Offered field requires at least one selection.',
         'C_SemOffered.*.in' => 'Selected semester(s) must be 1, 2, or 3.',
    ], 
    // 3. CUSTOM ATTRIBUTES (Fixes user-friendly names, Array 3)
    [
        'C_Name' => 'Course Name',
        'C_Hour' => 'Credit Hour',
        'C_SemOffered' => 'Semester Offered',
        'C_Prerequisites' => 'Prerequisites',
        'C_Instructor' => 'Instructor Name',
        'C_Description' => 'Course Description',
    ]);
    
    // CONVERSION STEP for update
    $semestersString = implode(',', $request->C_SemOffered);

    $course->update([
        'C_Name' => $request->C_Name,
        'C_Hour' => $request->C_Hour,
        'C_Prerequisites' => $request->C_Prerequisites,
        'C_SemOffered' => $semestersString, // Store the CSV string
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
            ->route('admin.viewAllCourse') // Changed to viewAllCourse for consistency
            ->with('success', 'Course deleted successfully');
    }


}