<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Schedule;
use App\Models\Leave;
use Illuminate\Http\Request;
use Validator;
use Auth;
use Carbon\Carbon;

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
            ->map(function ($attendance) {
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

    public function getSchedule()
    {
        $schedule = Schedule::with(['office', 'user'])
            ->where('user_id', auth()->user()->id)
            ->first();
        $today = Carbon::today()->format('Y-m-d');
        $approvedLeave = Leave::where('user_id', Auth::user()->id)
            ->where('status', 'approved')
            ->whereDate('start_date', '<=', $today)
            ->whereDate('end_date', '>=', $today)
            ->exists();
        if ($approvedLeave) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak dapat melakukan presensi karena sedang cuti',
                'data' => null
            ]);
        }
        if ($schedule->is_banned) {
            return response()->json([
                'success' => false,
                'message' => 'You are banned',
                'data' => null
            ]);
        } else {
            return response()->json([
                'success' => true,
                'data' => $schedule,
                'message' => 'Success get schedule'
            ]);
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric'
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation Store Attendance Error',
                'data' => $validator->errors()
            ], 422);
        }
        $today = Carbon::today()->format('Y-m-d');
        $approvedLeave = Leave::where('user_id', Auth::user()->id)
            ->where('status', 'approved')
            ->whereDate('start_date', '<=', $today)
            ->whereDate('end_date', '>=', $today)
            ->exists();
        if ($approvedLeave) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak dapat melakukan presensi karena sedang cuti',
                'data' => null
            ]);
        }
        $schedule = Schedule::where('user_id', auth()->user()->id)
            ->first();
        if ($schedule) {
            // Check if the user has attendance today
            $attendance = Attendance::where('user_id', auth()->user()->id)
                ->whereDate('created_at', date('Y-m-d'))
                ->first();
            if (!$attendance) {
                $attendance = Attendance::create([
                    'user_id' => Auth::user()->id,
                    'schedule_latitude' => $schedule->office->latitude,
                    'schedule_longitude' => $schedule->office->longitude,
                    'schedule_start_time' => $schedule->shift->start_time,
                    'schedule_end_time' => $schedule->shift->end_time,
                    'start_latitude' => $request->latitude,
                    'start_longitude' => $request->longitude,
                    'start_time' => Carbon::now()->toTimeString(),
                    'end_time' => Carbon::now()->toTimeString(),
                ]);
            } else {
                $attendance->update([
                    'end_latitude' => $request->latitude,
                    'end_longitude' => $request->longitude,
                    'end_time' => Carbon::now()->toTimeString(),
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Success store attendance',
                'data' => $attendance
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'No Schedule Found',
            ]);
        }
    }

    public function getAttendanceByMonthAndYear($month, $year)
    {
        $validator = Validator::make([
            'month' => $month,
            'year' => $year
        ], [
            'month' => 'required|numeric|between:1,12',
            // max date year is current year
            'year' => 'required|numeric|max:' . date('Y')
        ]);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation Get Attendance By Month And Year Error',
                'data' => $validator->errors()
            ], 422);
        }
        $userId = auth()->user()->id;
        $attendanceList = Attendance::select('start_time', 'end_time', 'created_at')
            ->where('user_id', $userId)
            ->whereMonth('created_at', $month)
            ->whereYear('created_at', $year)
            ->get()
            ->map(function ($attendance) {
                // $attendance->date = $attendance->created_at->format('Y-m-d');
                return [
                    'start_time' => $attendance->start_time,
                    'end_time' => $attendance->end_time,
                    'date' => $attendance->created_at->toDateString()
                ];
            });
        return response()->json([
            'success' => true,
            'data' => $attendanceList,
            'message' => 'Success get Attendance by month and year'
        ]);
    }

    public function banned()
    {
        // Get schedule data
        $schedule = Schedule::where('user_id', Auth::user()->id)->first();
        if ($schedule) {
            $schedule->update([
                'is_banned' => true
            ]);
        }
        return response()->json([
            'success' => true,
            'message' => 'Success banned user',
            'data' => $schedule
        ]);
    }
}