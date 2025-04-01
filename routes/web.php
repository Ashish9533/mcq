<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MCQController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\ActivityLogController;

// Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('login', [LoginController::class, 'login']);
    Route::get('register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('register', [RegisterController::class, 'register']);
});


Route::get('/activity-log', [ActivityLogController::class, 'index'])->name('activity-log.index');
    // User Management Routes
    Route::prefix('users')->group(function () {
        Route::get('/', [ActivityLogController::class, 'users'])->name('users.index');
        Route::get('/{user}/activities', [ActivityLogController::class, 'userActivities'])->name('users.activities');

        Route::get('/create', [ActivityLogController::class, 'create'])->name('users.create');
        Route::post('/', [ActivityLogController::class, 'store'])->name('users.store');
        Route::get('/{user}/edit', [ActivityLogController::class, 'edit'])->name('users.edit');
        Route::put('/{user}', [ActivityLogController::class, 'update'])->name('users.update');
        Route::delete('/{user}', [ActivityLogController::class, 'destroy'])->name('users.destroy');
        Route::post('/{user}/toggle-status', [ActivityLogController::class, 'toggleStatus'])->name('users.toggle-status');
        Route::get('/{user}/profile', [ActivityLogController::class, 'profile'])->name('users.profile');
        Route::put('/{user}/profile', [ActivityLogController::class, 'updateProfile'])->name('users.update-profile');
    });


Route::get('exam-mcq-user-result/{user?}',[ActivityLogController::class,'getExamResult']);

Route::get('test',function(){
    return view('test');
});









Route::post('logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

// Password Reset Routes
Route::get('password/reset', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('password/email', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('password/reset/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('password/reset', [ResetPasswordController::class, 'reset'])->name('password.update');

// Home Route
Route::get('/', function () {
    return view('home');
})->name('home');

// MCQ Routes
Route::middleware(['web', 'auth'])->group(function () {
    Route::get('/mcq', [MCQController::class, 'index'])->name('mcq.index');
    Route::get('/mcq/question/{id}', [MCQController::class, 'getQuestion'])->name('mcq.question');
    Route::post('/mcq/submit', [MCQController::class, 'submitExam'])->name('mcq.submit');
    Route::post('mcq/before-time-submit', [MCQController::class, 'beforeTimeSubmit'])->name('mcq.before-time-submit');

    Route::post('/log-activity', [ActivityLogController::class, 'logActivity'])->name('log.activity');
    Route::get('/exam-ended', [McqController::class, 'showEndedExam'])
    ->name('exam.ended');
 
});

Route::get('/thanks',[MCQController::class,'thanksCandidate'])->name('thank');

// Add this route for secure logout
// Route::post('/logout', [LogoutController::class, 'logout'])->name('logout');
