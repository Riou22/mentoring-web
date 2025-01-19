<?php

namespace App\Http\Controllers\Mentor;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AttendanceController extends Controller
{
    public function createAttendance(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'module_id' => 'required|exists:modules,module_id',
            'attendance_open' => 'required|date',
            'deadline' => 'required|date|after:attendance_open',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $attendance = Attendance::create([
                'module_id' => $request->module_id,
                'attendance_open' => $request->attendance_open,
                'deadline' => $request->deadline,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Attendance created successfully',
                'data' => $attendance,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while creating attendance',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function updateAttendance(Request $request, $attendance_id)
    {
        $validator = Validator::make($request->all(), [
            'attendance_open' => 'required|date',
            'deadline' => 'required|date|after:attendance_open',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $attendance = Attendance::find($attendance_id);

            if (!$attendance) {
                return response()->json([
                    'success' => false,
                    'message' => 'Attendance not found',
                ], 404);
            }

            $attendance->attendance_open = $request->attendance_open;
            $attendance->deadline = $request->deadline;
            $attendance->save();

            return response()->json([
                'success' => true,
                'message' => 'Attendance updated successfully',
                'data' => $attendance,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while updating attendance',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
