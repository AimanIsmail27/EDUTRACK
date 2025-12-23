<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Student;
use App\Services\MailService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class StudentController extends Controller
{
    /**
     * Display a listing of students.
     */
    public function index(Request $request)
    {
        $search = trim($request->get('search', ''));

        // Get students from users table and join with student table to ensure correct data
        $students = User::where('role', 'student')
            ->leftJoin('student', 'users.matric_id', '=', 'student.MatricID')
            ->when($search, function ($query) use ($search) {
                $lowerSearch = strtolower($search);
                $query->where(function ($inner) use ($lowerSearch) {
                    $inner->whereRaw('LOWER(users.matric_id) LIKE ?', ['%' . $lowerSearch . '%'])
                          ->orWhereRaw('LOWER(student.MatricID) LIKE ?', ['%' . $lowerSearch . '%']);
                });
            })
            ->select(
                'users.id',
                'users.matric_id',
                'users.name',
                'users.email as user_email',
                'users.course as user_course',
                'users.year as user_year',
                'student.Course as student_course',
                'student.Year as student_year',
                'student.Email as student_email',
                'users.created_at',
                'users.updated_at'
            )
            ->orderBy('users.created_at', 'desc')
            ->paginate(10);

        $students->appends($request->only('search'));
        
        // Map the data to use student table values if available, otherwise use users table
        $students->getCollection()->transform(function ($student) {
            // Use student table data if available, otherwise fall back to users table
            $student->email = $student->student_email ?? $student->user_email ?? strtolower($student->matric_id) . '@student.edu';
            $student->course = $student->student_course ?? $student->user_course ?? 'N/A';
            $student->year = $student->student_year ?? $student->user_year ?? 'N/A';
            return $student;
        });
        
        return view('M1.RegisterStudent', compact('students', 'search'));
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

        $user = User::create([
            'matric_id' => $validated['matric_id'],
            'name' => $validated['name'],
            'email' => $email,
            'course' => $validated['course'],
            'year' => $validated['year'],
            'password' => Hash::make($defaultPassword),
            'role' => 'student',
        ]);

        // Create Student record in student table
        Student::create([
            'MatricID' => $validated['matric_id'],
            'Name' => $validated['name'],
            'Email' => $email,
            'Course' => $validated['course'],
            'Year' => $validated['year'],
        ]);

        // Send email notification using PHPMailer
        try {
            $mailService = new MailService();
            $mailService->sendAccountEmail($email, $validated['name'], $defaultPassword, 'student', $validated['matric_id'], null, $validated['course'], $validated['year']);
        } catch (\Exception $mailException) {
            \Log::error('Failed to send registration email: ' . $mailException->getMessage());
        }

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

        // Update or create the student table record
        $studentRecord = Student::where('MatricID', $validated['matric_id'])->first();
        if ($studentRecord) {
            $studentRecord->update([
                'Name' => $validated['name'],
                'Email' => $email,
                'Course' => $validated['course'],
                'Year' => $validated['year'],
            ]);
        } else {
            // If record doesn't exist in student table, create it
            Student::create([
                'MatricID' => $validated['matric_id'],
                'Name' => $validated['name'],
                'Email' => $email,
                'Course' => $validated['course'],
                'Year' => $validated['year'],
            ]);
        }

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
        $validator = Validator::make(
            $request->all(),
            ['csv_file' => 'required|file|mimes:csv,txt|max:2048'],
            ['csv_file.mimes' => 'Invalid file format. Only CSV files are allowed.']
        );

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first('csv_file') ?? 'Invalid file format. Only CSV files are allowed.',
                'success_count' => 0,
                'error_count' => 0,
                'errors' => $validator->errors()->all(),
            ], 422);
        }

        $file = $request->file('csv_file');
        $path = $file->getRealPath();
        
        $data = array_map('str_getcsv', file($path));
        $header = array_shift($data); // Get header row
        
        // Normalize header: trim and convert to lowercase for matching
        $header = array_map(function($h) {
            return strtolower(trim($h));
        }, $header);
        
        // Find column indices by header name (case-insensitive)
        $matricIdIndex = array_search('matricid', $header);
        $nameIndex = array_search('name', $header);
        $courseIndex = array_search('course', $header);
        $yearIndex = array_search('year', $header);
        
        // Validate that all required columns exist
        if ($matricIdIndex === false || $nameIndex === false || $courseIndex === false || $yearIndex === false) {
            return response()->json([
                'success' => false,
                'message' => 'CSV file must contain columns: MatricID, Name, Course, Year',
                'success_count' => 0,
                'error_count' => 0,
                'errors' => ['Missing required columns in CSV header'],
            ]);
        }
        
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
                $maxIndex = max($matricIdIndex, $nameIndex, $courseIndex, $yearIndex);
                if (count($row) < $maxIndex + 1) {
                    $errors[] = "Row " . ($index + 2) . ": Insufficient columns";
                    $errorCount++;
                    continue;
                }

                // Map CSV columns using header indices
                $matricId = trim($row[$matricIdIndex] ?? '');
                $name = trim($row[$nameIndex] ?? '');
                $course = trim($row[$courseIndex] ?? '');
                $year = trim($row[$yearIndex] ?? '');
                
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
                    'Email' => $email,
                    'Course' => $course,
                    'Year' => $year,
                ]);

                DB::commit();
                
                // Send email notification with plain text password using PHPMailer
                try {
                    $mailService = new MailService();
                    $mailService->sendAccountEmail($email, $name, $password, 'student', $matricId, null, $course, $year);
                } catch (\Exception $mailException) {
                    // Log email error but don't fail the registration
                    \Log::error('Failed to send registration email: ' . $mailException->getMessage());
                }
                
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