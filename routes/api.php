<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\UserController; // Assuming you'll create this controller

// Public routes for registration and login
// This route handles new user registration.
Route::post('/register', [RegisteredUserController::class, 'store'])
                ->middleware('guest')
                ->name('register');

// This route handles user login.
Route::post('/login', [AuthenticatedSessionController::class, 'store'])
                ->middleware('guest')
                ->name('login');

// Authenticated routes
Route::middleware('auth:sanctum')->group(function () {
    // This route allows an authenticated user to log out.
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
                    ->name('logout');

    // This route provides the authenticated user's details.
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // Teacher-specific routes (protected by 'role:teacher' middleware)
    Route::middleware('role:teacher')->group(function () {
        // Get all students (for the teacher to mark attendance)
        // Assumes you have a UserController with a 'students' method.
        Route::get('/students', [UserController::class, 'students']);

        // Mark attendance for a student for a specific date
        Route::post('/attendance/mark', [AttendanceController::class, 'markAttendance']);

        // Get attendance for a specific date (for teacher to view)
        Route::get('/attendance/{date}', [AttendanceController::class, 'getAttendanceByDate']);
    });

    // Student-specific routes (protected by 'role:student' middleware)
    Route::middleware('role:student')->group(function () {
        // Get student's own attendance history
        Route::get('/my-attendance', [AttendanceController::class, 'myAttendance']);
    });
});