<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\LecturerController;
use App\Http\Controllers\StudentController;
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
});