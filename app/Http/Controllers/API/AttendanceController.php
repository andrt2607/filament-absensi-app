<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function getAttendanceToday()
    {
        // Get the authenticated user by sanctum
        $userId = auth()->user()->id;
        $today = now()->toDateString();
        $currentMonth = now()->month;
        $attendanceToday = Attendance::select('start_time', 'end_time')
            ->where('user_id', $userId)
            ->whereDate('created_at', $today)
            ->first();
            $attendanceThisMonth = Attendance::select('start_time', 'end_time', 'created_at')
            ->where('user_id', $userId)
            ->whereMonth('created_at', $currentMonth)
            ->get()
            ->map(function($attendance){
                // $attendance->date = $attendance->created_at->format('Y-m-d');
                return [
                    'start_time' => $attendance->start_time,
                    'end_time' => $attendance->end_time,
                    'date' => $attendance->created_at->toDateString()
                ];
            });
            ;
        return response()->json([
            'success' => true,
            // 'data' => $attendanceToday,
            'data' => [
                'today' => $attendanceToday,
                'this_month' => $attendanceThisMonth
            ],
            'message' => 'Success get Attendance today'
        ]);
    }
}
