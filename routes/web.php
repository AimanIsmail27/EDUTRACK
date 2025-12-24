<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\LecturerController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\AssignmentController;
use App\Http\Controllers\StudentAssignmentController;
use App\Http\Controllers\StudentCourseController;
use App\Http\Controllers\LecturerSubmissionController;
use App\Http\Controllers\MaterialController;

/*
|--------------------------------------------------------------------------
| Public Routes (Authentication)
|--------------------------------------------------------------------------
*/

// Root URL: Redirects based on authentication status
Route::get('/', function () {
    if (Auth::check()) {
        return app(AuthController::class)->redirectToDashboard(Auth::user());
    }
    return redirect()->route('login');
});

// Login Form
Route::get('/login', function () {
    return view('auth.login');
})->name('login');

// Login Submission
Route::post('/login', [AuthController::class, 'authenticate']);

// Logout Route
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');


/*
|--------------------------------------------------------------------------
| Protected Routes (Authenticated Users Only)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    
    /*
    |--------------------------------------------------------------------------
    | Administrator Routes
    |--------------------------------------------------------------------------
    */
    Route::middleware('role:administrator')->group(function () {
        
        // Admin Dashboard
        Route::get('/dashboard/administrator', function () {
            return view('dashboard.administrator');
        })->name('dashboard.admin');

        // Course Management
        Route::get('/administrator/manage-course/view-all', [CourseController::class, 'index'])->name('admin.viewAllCourse');
        Route::get('/add', [CourseController::class, 'create'])->name('admin.courses.create');
        Route::post('/store', [CourseController::class, 'store'])->name('admin.courses.store');
        Route::get('/edit/{code}', [CourseController::class, 'edit'])->name('admin.courses.edit');
        Route::put('/update/{code}', [CourseController::class, 'update'])->name('admin.courses.update');
        Route::delete('/destroy/{code}', [CourseController::class, 'destroy'])->name('admin.courses.destroy');
        Route::get('/view/{code}', [CourseController::class, 'show'])->name('admin.courses.show');

        // Participant Management
        Route::post('/administrator/course/{courseCode}/add-participant', [CourseController::class, 'addParticipant'])->name('admin.course.addParticipant');
        Route::get('/administrator/search-students', [CourseController::class, 'searchStudents'])->name('admin.students.search');
        Route::delete('/administrator/course/{code}/participant/{matric}', [CourseController::class, 'removeParticipant'])->name('admin.course.removeParticipant');

        // Register Lecturer Management
        Route::get('/administrator/register-lecturer', [LecturerController::class, 'index'])->name('register.lecturer');
        Route::post('/administrator/register-lecturer', [LecturerController::class, 'store'])->name('register.lecturer.store');
        Route::get('/administrator/register-lecturer/{lecturer}', [LecturerController::class, 'show'])->name('register.lecturer.show');
        Route::get('/administrator/register-lecturer/{lecturer}/edit', [LecturerController::class, 'edit'])->name('register.lecturer.edit');
        Route::put('/administrator/register-lecturer/{lecturer}', [LecturerController::class, 'update'])->name('register.lecturer.update');
        Route::delete('/administrator/register-lecturer/{lecturer}', [LecturerController::class, 'destroy'])->name('register.lecturer.destroy');

        // Register Student Management
        Route::get('/administrator/register-student', [StudentController::class, 'index'])->name('register.student');
        Route::post('/administrator/register-student', [StudentController::class, 'store'])->name('register.student.store');
        Route::get('/administrator/register-student/{student}', [StudentController::class, 'show'])->name('register.student.show');
        Route::get('/administrator/register-student/{student}/edit', [StudentController::class, 'edit'])->name('register.student.edit');
        Route::put('/administrator/register-student/{student}', [StudentController::class, 'update'])->name('register.student.update');
        Route::delete('/administrator/register-student/{student}', [StudentController::class, 'destroy'])->name('register.student.destroy');
    });

    /*
    |--------------------------------------------------------------------------
    | Lecturer Routes
    |--------------------------------------------------------------------------
    */
    Route::middleware('role:lecturer')->group(function () {
        
        // Lecturer Dashboard
        Route::get('/dashboard/lecturer', function () {
            return view('dashboard.lecturer');
        })->name('dashboard.lecturer');

        // View Taught Courses
        Route::get('/lecturer/my-courses', [CourseController::class, 'lecturerCourses'])
            ->name('lecturer.myCourses');

        // NEW: View Specific Course Details
        Route::get('/lecturer/course/{code}', [CourseController::class, 'show'])
            ->name('lecturer.courses.show');
        Route::post('/materials/upload', [MaterialController::class, 'store'])->name('materials.store');
        Route::get('/materials/download/{id}', [MaterialController::class, 'download'])->name('materials.download');
        Route::delete('/materials/delete/{id}', [MaterialController::class, 'destroy'])->name('materials.destroy');
    
    });

    /*
    |--------------------------------------------------------------------------
    | Student Routes
    |--------------------------------------------------------------------------
    */
    Route::middleware('role:student')->group(function () {

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
        })->name('dashboard.student');

        // View Enrolled Courses
        Route::get('/student/my-courses', [CourseController::class, 'studentCourses'])
            ->name('student.courses');

        // View Student Assessments
        Route::get('/student/assessments', [CourseController::class, 'studentAssessments'])
            ->name('student.assessment');
        
        Route::get('/student/materials/download/{id}', [MaterialController::class, 'download'])
            ->name('student.materials.download');
        
        Route::get('/student/course/{code}', [CourseController::class, 'studentCourseShow'])->name('student.courses.show');
    });
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