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

        $lecturers = User::where('role', 'lecturer')
            ->leftJoin('lecturer', 'users.email', '=', 'lecturer.Email')
            ->when($search, function ($query) use ($search) {
                $query->where(function ($inner) use ($search) {
                    $inner->whereRaw('LOWER(lecturer.StaffID) LIKE ?', ['%' . strtolower($search) . '%'])
                          ->orWhereRaw('LOWER(users.name) LIKE ?', ['%' . strtolower($search) . '%'])
                          ->orWhereRaw('LOWER(users.email) LIKE ?', ['%' . strtolower($search) . '%']);
                });
            })
            ->select(
                'users.id',
                'lecturer.StaffID as staff_id',
                'users.name',
                'users.email as user_email',
                'lecturer.Email as lecturer_email',
                'users.created_at',
                'users.updated_at'
            )
            ->orderBy('users.created_at', 'desc')
            ->paginate(10);

        $lecturers->appends($request->only('search'));

        $lecturers->getCollection()->transform(function ($lecturer) {
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
            'staff_id' => 'required|string|max:255|unique:lecturer,StaffID',
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
        ]);

        $defaultPassword = 'password123';

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($defaultPassword),
            'role' => 'lecturer',
        ]);

        Lecturer::create([
            'StaffID' => $validated['staff_id'],
            'Name' => $validated['name'],
            'Email' => $validated['email'],
        ]);

        try {
            $mailService = new MailService();
            $mailService->sendAccountEmail(
                $validated['email'],
                $validated['name'],
                $defaultPassword,
                'lecturer',
                null,
                $validated['staff_id'],
                null,
                null
            );
        } catch (\Exception $e) {
            \Log::error('Failed to send registration email: ' . $e->getMessage());
        }

        return redirect()->route('register.lecturer')
            ->with('success', 'Lecturer created successfully.');
    }

    /**
     * Display the specified lecturer.
     */
    public function show(User $lecturer)
    {
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
        if ($lecturer->role !== 'lecturer') {
            abort(404);
        }

        $validated = $request->validate([
            'staff_id' => 'required|string|max:255|unique:lecturer,StaffID,' . $request->staff_id . ',StaffID',
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $lecturer->id,
        ]);

        $lecturer->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
        ]);

        Lecturer::updateOrCreate(
            ['StaffID' => $validated['staff_id']],
            [
                'Name' => $validated['name'],
                'Email' => $validated['email'],
            ]
        );

        return redirect()->route('register.lecturer')
            ->with('success', 'Lecturer updated successfully.');
    }

    /**
     * Remove the specified lecturer from storage.
     */
    public function destroy(User $lecturer)
    {
        if ($lecturer->role !== 'lecturer') {
            abort(404);
        }

        Lecturer::where('Email', $lecturer->email)->delete();
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
            ['csv_file' => 'required|file|mimes:csv,txt|max:2048']
        );

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        $data = array_map('str_getcsv', file($request->file('csv_file')->getRealPath()));
        $header = array_map('strtolower', array_shift($data));

        $staffIdIndex = array_search('staffid', $header);
        $nameIndex = array_search('name', $header);
        $emailIndex = array_search('email', $header);

        $successCount = 0;
        $errors = [];

        foreach ($data as $i => $row) {
            if (empty(array_filter($row))) continue;

            try {
                DB::beginTransaction();

                User::create([
                    'name' => trim($row[$nameIndex]),
                    'email' => trim($row[$emailIndex]),
                    'password' => Hash::make('password123'),
                    'role' => 'lecturer',
                ]);

                Lecturer::create([
                    'StaffID' => trim($row[$staffIdIndex]),
                    'Name' => trim($row[$nameIndex]),
                    'Email' => trim($row[$emailIndex]),
                ]);

                DB::commit();
                $successCount++;
            } catch (\Exception $e) {
                DB::rollBack();
                $errors[] = "Row " . ($i + 2) . ": " . $e->getMessage();
            }
        }

        return response()->json([
            'success' => $successCount > 0,
            'message' => "Successfully registered {$successCount} lecturer(s)",
            'errors' => $errors,
        ]);
    }
}
