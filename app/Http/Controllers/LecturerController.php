<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class LecturerController extends Controller
{
    /**
     * Display a listing of lecturers.
     */
    public function index()
    {
        $lecturers = User::where('role', 'lecturer')->get();
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
            'password' => 'required|string|min:8|confirmed',
        ]);

        User::create([
            'staff_id' => $validated['staff_id'],
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
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
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        $updateData = [
            'staff_id' => $validated['staff_id'],
            'name' => $validated['name'],
            'email' => $validated['email'],
        ];

        if (!empty($validated['password'])) {
            $updateData['password'] = Hash::make($validated['password']);
        }

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
}