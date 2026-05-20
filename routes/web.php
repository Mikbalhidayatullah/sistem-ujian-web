<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\QuestionMediaController;
use App\Http\Controllers\Student\AttemptController;
use App\Http\Controllers\Teacher\DashboardController as TeacherDashboardController;
use App\Http\Controllers\Teacher\ExamController as TeacherExamController;
use App\Http\Controllers\Teacher\SubjectController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');
Route::get('/csrf-token', function (Request $request) {
    $request->session()->regenerateToken();

    return response()->json([
        'token' => csrf_token(),
    ]);
})->name('csrf.token');
Route::get('/media/questions/{question}', [QuestionMediaController::class, 'show'])->name('questions.media');
Route::get('/ujian/status', [AttemptController::class, 'status'])->name('exam.access.status');
Route::post('/ujian/masuk', [AttemptController::class, 'start'])->name('exam.access.start');
Route::get('/ujian/sesi/{attempt}', [AttemptController::class, 'show'])->name('exam.attempts.show');
Route::get('/ujian/hasil/{attempt}', [AttemptController::class, 'result'])->name('exam.attempts.result');
Route::post('/ujian/sesi/{attempt}/save', [AttemptController::class, 'saveProgress'])->name('exam.attempts.save');
Route::post('/ujian/sesi/{attempt}/submit', [AttemptController::class, 'submit'])->name('exam.attempts.submit');
Route::post('/ujian/sesi/{attempt}/violations', [AttemptController::class, 'recordViolation'])->name('exam.attempts.violations');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.store');
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [AuthController::class, 'dashboard'])->name('dashboard');
    Route::get('/profile', [AuthController::class, 'profile'])->name('profile.show');
    Route::put('/profile/password', [AuthController::class, 'updatePassword'])->name('profile.password.update');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::prefix('admin')->middleware('role:admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
        Route::get('/accounts', [AdminDashboardController::class, 'accounts'])->name('accounts.index');
        Route::post('/teachers', [AdminDashboardController::class, 'storeTeacher'])->name('teachers.store');
        Route::match(['put', 'patch'], '/teachers/{teacher}', [AdminDashboardController::class, 'updateTeacher'])->name('teachers.update');
    });

    Route::prefix('teacher')->middleware('role:teacher')->name('teacher.')->group(function () {
        Route::get('/dashboard', [TeacherDashboardController::class, 'index'])->name('dashboard');
        Route::get('/monitoring', [TeacherDashboardController::class, 'monitoring'])->name('monitoring');
        Route::delete('/monitoring', [TeacherDashboardController::class, 'destroyAllMonitoringViolations'])->name('monitoring.destroy-all');
        Route::delete('/monitoring/{violation}', [TeacherDashboardController::class, 'destroyMonitoringViolation'])->name('monitoring.destroy');
        Route::get('/subjects', [SubjectController::class, 'index'])->name('subjects.index');
        Route::post('/subjects', [SubjectController::class, 'store'])->name('subjects.store');
        Route::match(['put', 'patch'], '/subjects/{subject}', [SubjectController::class, 'update'])->name('subjects.update');
        Route::delete('/subjects/{subject}', [SubjectController::class, 'destroy'])->name('subjects.destroy');
        Route::get('/exams/create', [TeacherExamController::class, 'create'])->name('exams.create');
        Route::post('/exams', [TeacherExamController::class, 'store'])->name('exams.store');
        Route::get('/exams/{exam}/edit', [TeacherExamController::class, 'edit'])->name('exams.edit');
        Route::match(['put', 'patch'], '/exams/{exam}', [TeacherExamController::class, 'update'])->name('exams.update');
        Route::delete('/exams/{exam}', [TeacherExamController::class, 'destroy'])->name('exams.destroy');
        Route::delete('/exams/{exam}/attempts', [TeacherExamController::class, 'destroyAllAttempts'])->name('exams.attempts.destroy-all');
        Route::delete('/exams/{exam}/attempts/{attempt}', [TeacherExamController::class, 'destroyAttempt'])->name('exams.attempts.destroy');
        Route::delete('/exams/{exam}/violations', [TeacherExamController::class, 'destroyAllViolations'])->name('exams.violations.destroy-all');
        Route::delete('/exams/{exam}/violations/{violation}', [TeacherExamController::class, 'destroyViolation'])->name('exams.violations.destroy');
        Route::get('/exams/{exam}', [TeacherExamController::class, 'show'])->name('exams.show');
        Route::get('/exams/{exam}/export-scores', [TeacherExamController::class, 'exportScores'])->name('exams.export-scores');
        Route::post('/exams/{exam}/access', [TeacherExamController::class, 'toggleAccess'])->name('exams.access');
        Route::post('/exams/{exam}/violations/toggle', [TeacherExamController::class, 'toggleViolations'])->name('exams.violations.toggle');
        Route::post('/exams/{exam}/questions/import-template', [TeacherExamController::class, 'importTemplate'])->name('exams.questions.import-template');
        Route::post('/exams/{exam}/questions', [TeacherExamController::class, 'storeQuestion'])->name('exams.questions.store');
        Route::match(['put', 'patch'], '/exams/{exam}/questions/default-points', [TeacherExamController::class, 'updateDefaultPoints'])
            ->name('exams.questions.default-points.update');
        Route::delete('/exams/{exam}/questions', [TeacherExamController::class, 'destroyAllQuestions'])
            ->name('exams.questions.destroy-all');
        Route::match(['put', 'patch'], '/exams/{exam}/questions/{question}', [TeacherExamController::class, 'updateQuestion'])
            ->whereNumber('question')
            ->name('exams.questions.update');
        Route::delete('/exams/{exam}/questions/{question}', [TeacherExamController::class, 'destroyQuestion'])
            ->whereNumber('question')
            ->name('exams.questions.destroy');
    });
});
