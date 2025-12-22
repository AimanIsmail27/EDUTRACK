<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AuthController; // Ensure your AuthController is imported
use App\Http\Controllers\CourseController; // Ensure your CourseController is imported
use App\Http\Controllers\LecturerController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\AssignmentController;
use App\Http\Controllers\StudentAssignmentController;
use App\Http\Controllers\StudentCourseController;
use App\Http\Controllers\LecturerSubmissionController;


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

    Route::middleware('role:lecturer')->prefix('lecturer')->name('lecturer.')->group(function () {
        Route::resource('assignments', AssignmentController::class)->except(['show']);
        Route::get('assignments/calendar', [AssignmentController::class, 'calendar'])
            ->name('assignments.calendar');
        Route::get('assignments/calendar/events', [AssignmentController::class, 'calendarEvents'])
            ->name('assignments.calendar.events');
        Route::get('assignments/{assignment}/brief/download', [AssignmentController::class, 'downloadBrief'])
            ->name('assignments.brief.download');
        Route::get('assignments/{assignment}/submissions', [LecturerSubmissionController::class, 'index'])
            ->name('assignments.submissions');
        Route::post('assignments/{assignment}/submissions/{submission}/grade', [LecturerSubmissionController::class, 'grade'])
            ->name('assignments.submissions.grade');
        Route::get('assignments/{assignment}/submissions/{submission}/download', [LecturerSubmissionController::class, 'download'])
            ->name('assignments.submissions.download');
    });

    // Student Dashboard
    Route::get('/dashboard/student', function () {
        return view('dashboard.student');
    })->middleware('role:student')->name('dashboard.student');

    Route::middleware('role:student')->prefix('student')->name('student.')->group(function () {
        Route::get('courses', [StudentCourseController::class, 'index'])->name('courses.index');
        Route::get('assignments', [StudentAssignmentController::class, 'index'])->name('assignments.index');
        Route::get('assignments/calendar', [StudentAssignmentController::class, 'calendar'])->name('assignments.calendar');
        Route::get('assignments/calendar/events', [StudentAssignmentController::class, 'calendarEvents'])->name('assignments.calendar.events');
        Route::get('assignments/{assignment}', [StudentAssignmentController::class, 'show'])->name('assignments.show');
        Route::get('assignments/{assignment}/brief/download', [StudentAssignmentController::class, 'downloadBrief'])->name('assignments.brief.download');
        Route::get('assignments/{assignment}/submission/download', [StudentAssignmentController::class, 'downloadSubmission'])->name('assignments.submission.download');
        Route::post('assignments/{assignment}/submit', [StudentAssignmentController::class, 'submit'])->name('assignments.submit');
        Route::delete('assignments/{assignment}/submission', [StudentAssignmentController::class, 'destroySubmission'])->name('assignments.submission.destroy');
    });


    // ==============================
    // Administrator - Register Lecturer
    // ==============================

    // View all lecturers + registration form
    Route::get('/administrator/register-lecturer', [LecturerController::class, 'index'])
        ->middleware('role:administrator')
        ->name('register.lecturer');

    // Store new lecturer
    Route::post('/administrator/register-lecturer', [LecturerController::class, 'store'])
        ->middleware('role:administrator')
        ->name('register.lecturer.store');

    // Show lecturer (optional, used for edit/view)
    Route::get('/administrator/register-lecturer/{lecturer}', [LecturerController::class, 'show'])
        ->middleware('role:administrator')
        ->name('register.lecturer.show');

    // Edit lecturer
    Route::get('/administrator/register-lecturer/{lecturer}/edit', [LecturerController::class, 'edit'])
        ->middleware('role:administrator')
        ->name('register.lecturer.edit');

    // Update lecturer
    Route::put('/administrator/register-lecturer/{lecturer}', [LecturerController::class, 'update'])
        ->middleware('role:administrator')
        ->name('register.lecturer.update');

    // Delete lecturer
    Route::delete('/administrator/register-lecturer/{lecturer}', [LecturerController::class, 'destroy'])
        ->middleware('role:administrator')
        ->name('register.lecturer.destroy');

    // ==============================
    // Administrator - Register Student
    // ==============================

    // View all students + registration form
    Route::get('/administrator/register-student', [StudentController::class, 'index'])
        ->middleware('role:administrator')
        ->name('register.student');

    // Store new student
    Route::post('/administrator/register-student', [StudentController::class, 'store'])
        ->middleware('role:administrator')
        ->name('register.student.store');

    // Show student
    Route::get('/administrator/register-student/{student}', [StudentController::class, 'show'])
        ->middleware('role:administrator')
        ->name('register.student.show');

    // Edit student
    Route::get('/administrator/register-student/{student}/edit', [StudentController::class, 'edit'])
        ->middleware('role:administrator')
        ->name('register.student.edit');

    // Update student
    Route::put('/administrator/register-student/{student}', [StudentController::class, 'update'])
        ->middleware('role:administrator')
        ->name('register.student.update');

    // Delete student
    Route::delete('/administrator/register-student/{student}', [StudentController::class, 'destroy'])
        ->middleware('role:administrator')
        ->name('register.student.destroy');

    Route::post('/administrator/course/{courseCode}/add-participant', [CourseController::class, 'addParticipant'])
        ->name('admin.course.addParticipant');

    Route::get('/administrator/search-students', [CourseController::class, 'searchStudents'])
    ->name('admin.students.search');

    Route::delete('/administrator/course/{code}/participant/{matric}', [CourseController::class, 'removeParticipant'])
    ->name('admin.course.removeParticipant');
});