<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\RegistrationController;

Route::get('/', fn () => auth()->check() ? redirect()->route('dashboard') : redirect()->route('login'));

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::middleware('role:Administrator')->group(function () {
        Route::resource('students', StudentController::class);
        Route::resource('courses', CourseController::class);
        Route::get('/registrations', [RegistrationController::class, 'index'])->name('registrations.index');
        Route::delete('/registrations/{registration}', [RegistrationController::class, 'destroy'])->name('registrations.destroy');
    });

    Route::middleware('role:Administrator|Lecturer|Student')->group(function () {
        Route::get('/my-courses', [RegistrationController::class, 'myCourses'])->name('registrations.mine');
    });

    Route::middleware('role:Student')->group(function () {
        Route::post('/courses/{course}/register', [RegistrationController::class, 'store'])->name('registrations.store');
    });

    Route::middleware('role:Lecturer')->group(function () {
        Route::get('/lecturer/courses', [CourseController::class, 'assigned'])->name('courses.assigned');
    });
});

require __DIR__.'/auth.php';
