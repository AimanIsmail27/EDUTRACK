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
                // Use '=' for exact matching on Semester number
                $query->where($filterBy, $criteria);
            }
            // Note: Validation is often done here to ensure $filterBy is safe
            // but for simplicity, we'll assume the select options are controlled.
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
        $request->validate([
            'C_Code' => 'required|string|unique:courses,C_Code',
            'C_Name' => 'required|string',
            'C_Hour' => 'required|integer',
            'C_SemOffered' => 'required|integer|in:1,2,3',
        ]);

        Course::create([
            'C_Code' => $request->C_Code,
            'C_Name' => $request->C_Name,
            'C_Hour' => $request->C_Hour,
            'C_Prerequisites' => $request->C_Prerequisites ?? [],
            'C_SemOffered' => $request->C_SemOffered,
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

        return view('M2.administrator.viewCourse', compact('course'));
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
            'C_Name' => 'required|string',
            'C_Hour' => 'required|integer',
            'C_SemOffered' => 'required|integer|in:1,2,3',
        ]);

        $course->update([
            'C_Name' => $request->C_Name,
            'C_Hour' => $request->C_Hour,
            'C_Prerequisites' => $request->C_Prerequisites ?? [],
            'C_SemOffered' => $request->C_SemOffered,
            'C_Instructor' => $request->C_Instructor,
            'C_Description' => $request->C_Description,
        ]);

        return redirect()
            ->route('admin.courses.index')
            ->with('success', 'Course updated successfully');
    }

    /**
     * Delete course
     */
    public function destroy($code)
    {
        Course::findOrFail($code)->delete();

        return redirect()
            ->route('admin.courses.index')
            ->with('success', 'Course deleted successfully');
    }
}
