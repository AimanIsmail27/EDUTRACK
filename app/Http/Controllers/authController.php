<?php

// app/Http/Controllers/AuthController.php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\RedirectResponse;

class AuthController extends Controller
{
    /**
     * Handle an authentication attempt.
     */
    public function authenticate(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
            'role' => ['required', 'string'], // Validate the role input
        ]);

        // Attempt to log in the user, verifying both credentials AND the role
        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            
            // SECURITY CHECK: Ensure the user's actual role matches the selected role
            if ($user->role !== $request->role) {
                Auth::logout(); // Log out the user immediately if the roles don't match
                return back()->withErrors([
                    'email' => 'The selected role does not match the account role.',
                ])->onlyInput('email');
            }

            $request->session()->regenerate();

            return $this->redirectToDashboard($user);
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    /**
     * Redirects the user based on their role after successful login.
     */
    public function redirectToDashboard(User $user)
    {
        return match ($user->role) {
            'administrator' => redirect()->route('dashboard.admin'),
            'lecturer' => redirect()->route('dashboard.lecturer'),
            'student' => redirect()->route('dashboard.student'),
            default => redirect()->route('login'), // Fallback
        };
    }

    /**
     * Log the user out of the application.
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    /**
     * Show the password reset form.
     */
    public function showResetForm()
    {
        return view('auth.reset-password');
    }

    /**
     * Handle password reset request.
     */
    public function resetPassword(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'email.exists' => 'The email address does not exist in our records.',
            'password.min' => 'The password must be at least 8 characters.',
            'password.confirmed' => 'The password confirmation does not match.',
        ]);

        // Find the user by email
        $user = User::where('email', $validated['email'])->first();

        if (!$user) {
            return back()->withErrors([
                'email' => 'The email address does not exist in our records.',
            ])->onlyInput('email');
        }

        // Update the password
        $user->password = Hash::make($validated['password']);
        $user->save();

        return redirect()->route('password.reset')
            ->with('success', 'Password has been reset successfully! You can now login with your new password.');
    }
}