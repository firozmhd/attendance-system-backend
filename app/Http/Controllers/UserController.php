<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * Get all users with the role 'student'.
     * This method is intended for teachers to retrieve a list of their students.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function students()
    {
        // Retrieve all users where the 'role' column is 'student'.
        $students = User::where('role', 'student')->get();
        // Return the list of students as a JSON response.
        return response()->json($students);
    }
}