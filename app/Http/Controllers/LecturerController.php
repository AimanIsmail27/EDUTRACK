<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Lecturer;
use App\Services\MailService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class LecturerController extends Controller
{
    /**
     * Display a listing of lecturers.
     */
    public function index(Request $request)
    {
        $search = trim($request->get('search', ''));

        // Get lecturers from users table and join with lecturer table to ensure correct data
        $lecturers = User::where('role', 'lecturer')
            ->leftJoin('lecturer', 'users.staff_id', '=', 'lecturer.StaffID')
            ->when($search, function ($query) use ($search) {
                $lowerSearch = strtolower($search);
                $query->where(function ($inner) use ($lowerSearch) {
                    $inner->whereRaw('LOWER(users.staff_id) LIKE ?', ['%' . $lowerSearch . '%'])
                          ->orWhereRaw('LOWER(lecturer.StaffID) LIKE ?', ['%' . $lowerSearch . '%']);
                });
            })
            ->select(
                'users.id',
                'users.staff_id',
                'users.name',
                'users.email as user_email',
                'lecturer.Email as lecturer_email',
                'users.created_at',
                'users.updated_at'
            )
            ->orderBy('users.created_at', 'desc')
            ->paginate(10);

        $lecturers->appends($request->only('search'));
        
        // Map the data to use lecturer table values if available, otherwise use users table
        $lecturers->getCollection()->transform(function ($lecturer) {
            // Use lecturer table data if available, otherwise fall back to users table
            $lecturer->email = $lecturer->lecturer_email ?? $lecturer->user_email ?? 'N/A';
            return $lecturer;
        });
        
        return view('M1.RegisterLecturer', compact('lecturers', 'search'));
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

        $user = User::create([
            'staff_id' => $validated['staff_id'],
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($defaultPassword),
            'role' => 'lecturer',
        ]);

        // Create Lecturer record in lecturer table
        Lecturer::create([
            'StaffID' => $validated['staff_id'],
            'Name' => $validated['name'],
            'Email' => $validated['email'],
        ]);

        // Send email notification using PHPMailer
        try {
            $mailService = new MailService();
            $mailService->sendAccountEmail($validated['email'], $validated['name'], $defaultPassword, 'lecturer', null, $validated['staff_id'], null, null);
        } catch (\Exception $mailException) {
            \Log::error('Failed to send registration email: ' . $mailException->getMessage());
        }

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

        // Update or create the lecturer table record
        $lecturerRecord = Lecturer::where('StaffID', $validated['staff_id'])->first();
        if ($lecturerRecord) {
            $lecturerRecord->update([
                'Name' => $validated['name'],
                'Email' => $validated['email'],
            ]);
        } else {
            // If record doesn't exist in lecturer table, create it
            Lecturer::create([
                'StaffID' => $validated['staff_id'],
                'Name' => $validated['name'],
                'Email' => $validated['email'],
            ]);
        }

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

        // Delete from lecturer table if exists
        if ($lecturer->staff_id) {
            Lecturer::where('StaffID', $lecturer->staff_id)->delete();
        }

        // Delete from users table
        $lecturer->delete();

        return redirect()->route('register.lecturer')
            ->with('success', 'Lecturer deleted successfully.');
    }

    /**
     * Upload lecturers from CSV file.
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
        $staffIdIndex = array_search('staffid', $header);
        $nameIndex = array_search('name', $header);
        $emailIndex = array_search('email', $header);
        
        // Validate that all required columns exist
        if ($staffIdIndex === false || $nameIndex === false || $emailIndex === false) {
            return response()->json([
                'success' => false,
                'message' => 'CSV file must contain columns: StaffID, Name, Email',
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
                if (count($row) < max($staffIdIndex, $nameIndex, $emailIndex) + 1) {
                    $errors[] = "Row " . ($index + 2) . ": Insufficient columns";
                    $errorCount++;
                    continue;
                }

                // Map CSV columns using header indices
                $staffId = trim($row[$staffIdIndex] ?? '');
                $name = trim($row[$nameIndex] ?? '');
                $email = trim($row[$emailIndex] ?? '');
                
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
                
                // Send email notification with plain text password using PHPMailer
                try {
                    $mailService = new MailService();
                    $mailService->sendAccountEmail($email, $name, $password, 'lecturer', null, $staffId, null, null);
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