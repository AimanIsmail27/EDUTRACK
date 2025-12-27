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
use App\Http\Controllers\DashboardController;

/*
|--------------------------------------------------------------------------
| Public Routes (Authentication)
|--------------------------------------------------------------------------
*/

// Root URL
Route::get('/', function () {
    if (Auth::check()) {
        return app(AuthController::class)->redirectToDashboard(Auth::user());
    }
    return redirect()->route('login');
});

// Login & Logout
Route::get('/login', fn() => view('auth.login'))->name('login');
Route::post('/login', [AuthController::class, 'authenticate']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Password Reset
Route::get('/reset-password', [AuthController::class, 'showResetForm'])->name('password.reset');
Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.reset.submit');

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

        // Dashboard
        Route::get('/dashboard/administrator', fn() => view('dashboard.administrator'))->name('dashboard.admin');

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

        // Lecturer Management
        Route::get('/administrator/register-lecturer', [LecturerController::class, 'index'])->name('register.lecturer');
        Route::post('/administrator/register-lecturer', [LecturerController::class, 'store'])->name('register.lecturer.store');
        Route::post('/administrator/register-lecturer/upload', [LecturerController::class, 'uploadCsv'])->name('register.lecturer.upload');
        Route::get('/administrator/register-lecturer/{lecturer}', [LecturerController::class, 'show'])->name('register.lecturer.show');
        Route::get('/administrator/register-lecturer/{lecturer}/edit', [LecturerController::class, 'edit'])->name('register.lecturer.edit');
        Route::put('/administrator/register-lecturer/{lecturer}', [LecturerController::class, 'update'])->name('register.lecturer.update');
        Route::delete('/administrator/register-lecturer/{lecturer}', [LecturerController::class, 'destroy'])->name('register.lecturer.destroy');

        // Student Management
        Route::get('/administrator/register-student', [StudentController::class, 'index'])->name('register.student');
        Route::post('/administrator/register-student', [StudentController::class, 'store'])->name('register.student.store');
        Route::post('/administrator/register-student/upload', [StudentController::class, 'uploadCsv'])->name('register.student.upload');
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

        // Dashboard
        Route::get('/dashboard/lecturer', [DashboardController::class, 'lecturerDashboard'])
        ->name('dashboard.lecturer');

        // Courses
        Route::get('/lecturer/my-courses', [CourseController::class, 'lecturerCourses'])->name('lecturer.myCourses');
        Route::get('/lecturer/course/{code}', [CourseController::class, 'show'])->name('lecturer.courses.show');

        // Materials
        Route::post('/materials/upload', [MaterialController::class, 'store'])->name('materials.store');
        Route::get('/materials/download/{id}', [MaterialController::class, 'download'])->name('materials.download');
        Route::delete('/materials/delete/{id}', [MaterialController::class, 'destroy'])->name('materials.destroy');

        // Assignments (fixed)
        Route::prefix('lecturer')->name('lecturer.')->group(function () {
        Route::resource('assignments', AssignmentController::class)->except(['show']);
        Route::get('assignments/calendar', [AssignmentController::class, 'calendar'])->name('assignments.calendar');
        Route::get('assignments/calendar/events', [AssignmentController::class, 'calendarEvents'])->name('assignments.calendar.events');
        Route::get('assignments/{assignment}/brief/download', [AssignmentController::class, 'downloadBrief'])->name('assignments.brief.download');
        Route::get('assignments/{assignment}/submissions', [LecturerSubmissionController::class, 'index'])->name('assignments.submissions');
        Route::post('assignments/{assignment}/submissions/{submission}/grade', [LecturerSubmissionController::class, 'grade'])->name('assignments.submissions.grade');
        Route::get('assignments/{assignment}/submissions/{submission}/download', [LecturerSubmissionController::class, 'download'])->name('assignments.submissions.download');
    });
    });

    /*
    |--------------------------------------------------------------------------
    | Student Routes
    |--------------------------------------------------------------------------
    */
    Route::middleware('role:student')->group(function () {

        // Dashboard
        Route::get('/dashboard/student', [DashboardController::class, 'studentDashboard'])
        ->name('dashboard.student');

        // Courses
        Route::get('/student/my-courses', [CourseController::class, 'studentCourses'])->name('student.courses');
        Route::get('/student/course/{code}', [CourseController::class, 'studentCourseShow'])->name('student.courses.show');
        Route::get('/student/assessments', [CourseController::class, 'studentAssessments'])->name('student.assessment');
        Route::get('/student/materials/download/{id}', [MaterialController::class, 'download'])->name('student.materials.download');

        // Student Assignments
        Route::get('/student/courses', [StudentCourseController::class, 'index'])->name('student.courses.index');
        Route::get('/student/assignments', [StudentAssignmentController::class, 'index'])->name('student.assignments.index');
        Route::get('/student/assignments/calendar', [StudentAssignmentController::class, 'calendar'])->name('student.assignments.calendar');
        Route::get('/student/assignments/calendar/events', [StudentAssignmentController::class, 'calendarEvents'])->name('student.assignments.calendar.events');
        Route::get('/student/assignments/{assignment}', [StudentAssignmentController::class, 'show'])->name('student.assignments.show');
        Route::get('/student/assignments/{assignment}/brief/download', [StudentAssignmentController::class, 'downloadBrief'])->name('student.assignments.brief.download');
        Route::get('/student/assignments/{assignment}/submission/download', [StudentAssignmentController::class, 'downloadSubmission'])->name('student.assignments.submission.download');
        Route::post('/student/assignments/{assignment}/submit', [StudentAssignmentController::class, 'submit'])->name('student.assignments.submit');
        Route::delete('/student/assignments/{assignment}/submission', [StudentAssignmentController::class, 'destroySubmission'])->name('student.assignments.submission.destroy');
    });

});
