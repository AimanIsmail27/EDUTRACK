<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AuthController; // Ensure your AuthController is imported
use App\Http\Controllers\CourseController; // Ensure your CourseController is imported
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

    // Administrator - View All Courses
    Route::get('/administrator/manage-course/view-all', [CourseController::class, 'index'])
        ->middleware('role:administrator')
        ->name('admin.viewAllCourse');
        
    // Administrator - Add New Course
    Route::get('/add', [CourseController::class, 'create'])->name('admin.courses.create');

    // Administrator - Store New Course
    Route::post('/store', [CourseController::class, 'store'])->name('admin.courses.store');

    Route::get('/edit/{code}', [CourseController::class, 'edit'])->name('admin.courses.edit');

     // 5. Update Course (Update) - Handles the PUT request from the edit form
    Route::put('/update/{code}', [CourseController::class, 'update'])->name('admin.courses.update');

    // 6. Delete Course (Destroy) - Handles the DELETE request
    Route::delete('/destroy/{code}', [CourseController::class, 'destroy'])->name('admin.courses.destroy');
    
    Route::get('/view/{code}', [CourseController::class, 'show'])
             ->name('admin.courses.show');

    // Lecturer Dashboard
    Route::get('/dashboard/lecturer', function () {
        return view('dashboard.lecturer');
    })->middleware('role:lecturer')->name('dashboard.lecturer');

    // Student Dashboard
    Route::get('/dashboard/student', function () {
        return view('dashboard.student');
    })->middleware('role:student')->name('dashboard.student');
});