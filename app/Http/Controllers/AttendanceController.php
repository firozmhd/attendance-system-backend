<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon; // For date manipulation

class AttendanceController extends Controller
{
    /**
     * Mark attendance for a student. (Teacher function)
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function markAttendance(Request $request)
    {
        try {
            // Validate incoming request data.
            $request->validate([
                'user_id' => 'required|exists:users,id', // 'user_id' must exist in the 'users' table
                'date' => 'required|date', // 'date' must be a valid date format
                'status' => 'required|in:present,absent,leave', // 'status' must be one of these values
            ]);

            // Find the student by user_id.
            $student = User::find($request->user_id);

            // Check if the found user is actually a student.
            if (!$student || $student->role !== 'student') {
                return response()->json(['message' => 'User is not a student.'], 403);
            }

            // Check if attendance for this student on this date already exists.
            // If it exists, update it; otherwise, create a new record.
            $attendance = Attendance::updateOrCreate(
                [
                    'user_id' => $request->user_id,
                    'date' => $request->date,
                ],
                [
                    'status' => $request->status,
                ]
            );

            // Return a success response with the attendance record.
            return response()->json(['message' => 'Attendance marked successfully.', 'attendance' => $attendance]);

        } catch (ValidationException $e) {
            // Handle validation errors.
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            // Handle other exceptions.
            return response()->json(['message' => 'Error marking attendance: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Get attendance records for a specific date. (Teacher function)
     *
     * @param  string  $date
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAttendanceByDate(string $date)
    {
        try {
            // Ensure the date is in a valid format (YYYY-MM-DD).
            Carbon::parse($date); // This will throw an exception if the date is invalid

            // Get all students.
            $students = User::where('role', 'student')->get();

            $attendanceData = [];
            // For each student, find their attendance for the given date.
            foreach ($students as $student) {
                $attendance = Attendance::where('user_id', $student->id)
                                        ->where('date', $date)
                                        ->first();
                // If attendance exists, add it to the data; otherwise, default to 'absent'.
                $attendanceData[] = [
                    'student_id' => $student->id,
                    'student_name' => $student->name,
                    'date' => $date,
                    'status' => $attendance ? $attendance->status : 'absent', // Default to absent if no record
                ];
            }

            // Return the attendance data as a JSON response.
            return response()->json($attendanceData);

        } catch (\Exception $e) {
            // Handle invalid date format or other errors.
            return response()->json(['message' => 'Error retrieving attendance: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Get the authenticated student's attendance history. (Student function)
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function myAttendance(Request $request)
    {
        // Get the authenticated user.
        $user = $request->user();

        // Check if the user is a student.
        if ($user->role !== 'student') {
            return response()->json(['message' => 'Unauthorized. Only students can view their attendance.'], 403);
        }

        // Retrieve all attendance records for the authenticated student, ordered by date.
        $attendance = $user->attendances()->orderBy('date', 'desc')->get();

        // Return the attendance records as a JSON response.
        return response()->json($attendance);
    }
}