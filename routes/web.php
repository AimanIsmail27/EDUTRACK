<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AuthController; // Ensure your AuthController is imported

/*
|--------------------------------------------------------------------------
| Public Routes (Authentication)
|--------------------------------------------------------------------------
*/

// Root URL: Redirects to login
Route::get('/', function () {
    if (Auth::check()) {
        // If authenticated, use AuthController to redirect to the correct dashboard
        return app(AuthController::class)->redirectToDashboard(Auth::user());
    }
    return redirect()->route('login');
});

// Login Form (GET request to display the form)
Route::get('/login', function () {
    return view('auth.login');
})->name('login');

// Login Submission (POST request to process credentials)
Route::post('/login', [AuthController::class, 'authenticate']);

// Logout Route
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');


/*
|--------------------------------------------------------------------------
| Protected Routes (Dashboards)
|--------------------------------------------------------------------------
| These routes require the user to be authenticated and have the correct role.
*/
Route::middleware('auth')->group(function () {
    
    // Administrator Dashboard
    Route::get('/dashboard/administrator', function () {
        return view('dashboard.administrator');
    })->middleware('role:administrator')->name('dashboard.admin');

    // Lecturer Dashboard
    Route::get('/dashboard/lecturer', function () {
        return view('dashboard.lecturer');
    })->middleware('role:lecturer')->name('dashboard.lecturer');

    // Student Dashboard
    Route::get('/dashboard/student', function () {
        return view('dashboard.student');
    })->middleware('role:student')->name('dashboard.student');
});