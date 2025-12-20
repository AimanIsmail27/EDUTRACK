<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class StudentController extends Controller
{
    /**
     * Display a listing of students.
     */
    public function index()
    {
        $students = User::where('role', 'student')
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        return view('M1.RegisterStudent', compact('students'));
    }

    /**
     * Show the form for creating a new student.
     */
    public function create()
    {
        return view('M1.RegisterStudent');
    }

    /**
     * Store a newly created student in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'matric_id' => 'required|string|unique:users,matric_id|max:255',
            'name' => 'required|string|max:255',
            'course' => 'required|string|max:255',
            'year' => 'required|string|max:255',
        ]);

        // Generate email from matric_id (students don't need email input)
        $email = strtolower($validated['matric_id']) . '@student.edu';
        
        // Auto-generate default password
        $defaultPassword = 'password123';

        User::create([
            'matric_id' => $validated['matric_id'],
            'name' => $validated['name'],
            'email' => $email,
            'course' => $validated['course'],
            'year' => $validated['year'],
            'password' => Hash::make($defaultPassword),
            'role' => 'student',
        ]);

        return redirect()->route('register.student')
            ->with('success', 'Student created successfully.');
    }

    /**
     * Display the specified student.
     */
    public function show(User $student)
    {
        // Ensure the user is a student
        if ($student->role !== 'student') {
            abort(404);
        }

        return view('M1.RegisterStudent', compact('student'));
    }

    /**
     * Show the form for editing the specified student.
     */
    public function edit(User $student)
    {
        // Ensure the user is a student
        if ($student->role !== 'student') {
            abort(404);
        }

        return view('M1.RegisterStudent', compact('student'));
    }

    /**
     * Update the specified student in storage.
     */
    public function update(Request $request, User $student)
    {
        // Ensure the user is a student
        if ($student->role !== 'student') {
            abort(404);
        }

        $validated = $request->validate([
            'matric_id' => 'required|string|max:255|unique:users,matric_id,' . $student->id,
            'name' => 'required|string|max:255',
            'course' => 'required|string|max:255',
            'year' => 'required|string|max:255',
        ]);

        // Generate email from matric_id (students don't need email input)
        $email = strtolower($validated['matric_id']) . '@student.edu';

        $updateData = [
            'matric_id' => $validated['matric_id'],
            'name' => $validated['name'],
            'course' => $validated['course'],
            'year' => $validated['year'],
            'email' => $email,
        ];

        $student->update($updateData);

        return redirect()->route('register.student')
            ->with('success', 'Student updated successfully.');
    }

    /**
     * Remove the specified student from storage.
     */
    public function destroy(User $student)
    {
        // Ensure the user is a student
        if ($student->role !== 'student') {
            abort(404);
        }

        $student->delete();

        return redirect()->route('register.student')
            ->with('success', 'Student deleted successfully.');
    }

    /**
     * Upload students from CSV file.
     */
    public function uploadCsv(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:2048',
        ]);

        $file = $request->file('csv_file');
        $path = $file->getRealPath();
        
        $data = array_map('str_getcsv', file($path));
        $header = array_shift($data); // Remove header row
        
        $successCount = 0;
        $errorCount = 0;
        $errors = [];

        foreach ($data as $index => $row) {
            try {
                // Skip empty rows
                if (empty(array_filter($row))) {
                    continue;
                }

                // Ensure row has enough columns
                if (count($row) < 4) {
                    $errors[] = "Row " . ($index + 2) . ": Insufficient columns (expected 4: MatricID, Name, Course, Year)";
                    $errorCount++;
                    continue;
                }

                // Map CSV columns (adjust based on your CSV format)
                // Expected format: MatricID, Name, Course, Year
                $matricId = trim($row[0] ?? '');
                $name = trim($row[1] ?? '');
                $course = trim($row[2] ?? '');
                $year = trim($row[3] ?? '');
                
                // Generate email from matric_id (students don't need email input)
                $email = strtolower($matricId) . '@student.edu';
                
                // Auto-generate default password
                $password = 'password123';

                // Validate required fields with specific error messages
                $missingFields = [];
                if (empty($matricId)) $missingFields[] = 'MatricID';
                if (empty($name)) $missingFields[] = 'Name';
                if (empty($course)) $missingFields[] = 'Course';
                if (empty($year)) $missingFields[] = 'Year';

                if (!empty($missingFields)) {
                    $errors[] = "Row " . ($index + 2) . ": Missing required fields (" . implode(', ', $missingFields) . ")";
                    $errorCount++;
                    continue;
                }

                // Check if user already exists (by matric_id only, email is optional)
                if (User::where('matric_id', $matricId)->exists()) {
                    $errors[] = "Row " . ($index + 2) . ": Matric ID already exists";
                    $errorCount++;
                    continue;
                }

                DB::beginTransaction();

                // Create User
                $user = User::create([
                    'matric_id' => $matricId,
                    'name' => $name,
                    'email' => $email,
                    'course' => $course,
                    'year' => $year,
                    'password' => Hash::make($password),
                    'role' => 'student',
                ]);

                // Create Student record
                Student::create([
                    'MatricID' => $matricId,
                    'Name' => $name,
                    'Course' => $course,
                    'Year' => $year,
                ]);

                DB::commit();
                $successCount++;

            } catch (\Exception $e) {
                DB::rollBack();
                $errors[] = "Row " . ($index + 2) . ": " . $e->getMessage();
                $errorCount++;
            }
        }

        // Show success message if any records were successfully added
        if ($successCount > 0) {
            $message = "Successfully registered {$successCount} student(s)!";
            if ($errorCount > 0) {
                $message .= " ({$errorCount} record(s) skipped due to errors)";
            }
        } else {
            $message = "Upload failed. No students were registered.";
            if (!empty($errors)) {
                $message .= " " . count($errors) . " error(s) occurred.";
            }
        }

        return response()->json([
            'success' => $successCount > 0,
            'message' => $message,
            'success_count' => $successCount,
            'error_count' => $errorCount,
            'errors' => $errors,
        ]);
    }
}