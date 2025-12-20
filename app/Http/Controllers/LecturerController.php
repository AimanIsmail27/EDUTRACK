<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Lecturer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class LecturerController extends Controller
{
    /**
     * Display a listing of lecturers.
     */
    public function index()
    {
        $lecturers = User::where('role', 'lecturer')
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        return view('M1.RegisterLecturer', compact('lecturers'));
    }

    /**
     * Show the form for creating a new lecturer.
     */
    public function create()
    {
        return view('M1.RegisterLecturer');
    }

    /**
     * Store a newly created lecturer in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'staff_id' => 'required|string|unique:users,staff_id|max:255',
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
        ]);

        // Auto-generate default password
        $defaultPassword = 'password123';

        User::create([
            'staff_id' => $validated['staff_id'],
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($defaultPassword),
            'role' => 'lecturer',
        ]);

        return redirect()->route('register.lecturer')
            ->with('success', 'Lecturer created successfully.');
    }

    /**
     * Display the specified lecturer.
     */
    public function show(User $lecturer)
    {
        // Ensure the user is a lecturer
        if ($lecturer->role !== 'lecturer') {
            abort(404);
        }

        return view('M1.RegisterLecturer', compact('lecturer'));
    }

    /**
     * Show the form for editing the specified lecturer.
     */
    public function edit(User $lecturer)
    {
        // Ensure the user is a lecturer
        if ($lecturer->role !== 'lecturer') {
            abort(404);
        }

        return view('M1.RegisterLecturer', compact('lecturer'));
    }

    /**
     * Update the specified lecturer in storage.
     */
    public function update(Request $request, User $lecturer)
    {
        // Ensure the user is a lecturer
        if ($lecturer->role !== 'lecturer') {
            abort(404);
        }

        $validated = $request->validate([
            'staff_id' => 'required|string|max:255|unique:users,staff_id,' . $lecturer->id,
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $lecturer->id,
        ]);

        $updateData = [
            'staff_id' => $validated['staff_id'],
            'name' => $validated['name'],
            'email' => $validated['email'],
        ];

        $lecturer->update($updateData);

        return redirect()->route('register.lecturer')
            ->with('success', 'Lecturer updated successfully.');
    }

    /**
     * Remove the specified lecturer from storage.
     */
    public function destroy(User $lecturer)
    {
        // Ensure the user is a lecturer
        if ($lecturer->role !== 'lecturer') {
            abort(404);
        }

        $lecturer->delete();

        return redirect()->route('register.lecturer')
            ->with('success', 'Lecturer deleted successfully.');
    }

    /**
     * Upload lecturers from CSV file.
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
                if (count($row) < 3) {
                    $errors[] = "Row " . ($index + 2) . ": Insufficient columns (expected at least 3: StaffID, Name, Email)";
                    $errorCount++;
                    continue;
                }

                // Map CSV columns (adjust based on your CSV format)
                // Expected format: StaffID, Name, Email
                $staffId = trim($row[0] ?? '');
                $name = trim($row[1] ?? '');
                $email = trim($row[2] ?? '');
                
                // Auto-generate default password
                $password = 'password123';

                // Validate required fields with specific error messages
                $missingFields = [];
                if (empty($staffId)) $missingFields[] = 'StaffID';
                if (empty($name)) $missingFields[] = 'Name';
                if (empty($email)) $missingFields[] = 'Email';

                if (!empty($missingFields)) {
                    $errors[] = "Row " . ($index + 2) . ": Missing required fields (" . implode(', ', $missingFields) . ")";
                    $errorCount++;
                    continue;
                }

                // Check if user already exists
                if (User::where('staff_id', $staffId)->orWhere('email', $email)->exists()) {
                    $errors[] = "Row " . ($index + 2) . ": Staff ID or Email already exists";
                    $errorCount++;
                    continue;
                }

                DB::beginTransaction();

                // Create User
                $user = User::create([
                    'staff_id' => $staffId,
                    'name' => $name,
                    'email' => $email,
                    'password' => Hash::make($password),
                    'role' => 'lecturer',
                ]);

                // Create Lecturer record
                Lecturer::create([
                    'StaffID' => $staffId,
                    'Name' => $name,
                    'Email' => $email,
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
            $message = "Successfully registered {$successCount} lecturer(s)!";
            if ($errorCount > 0) {
                $message .= " ({$errorCount} record(s) skipped due to errors)";
            }
        } else {
            $message = "Upload failed. No lecturers were registered.";
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