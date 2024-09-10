<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'schedule_latitude',
        'schedule_longitude',
        'schedule_start_time',
        'schedule_end_time',
        'start_latitude',
        'start_longitude',
        'end_latitude',
        'end_longitude',
        'start_time',
        'end_time',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isLate(){
        $scheduleStartTime = Carbon::parse($this->schedule_start_time);
        $startTime = Carbon::parse($this->start_time);
        return $startTime -> greaterThan($scheduleStartTime);
    }

    public function workDuration(){
        $startTime = Carbon::parse($this->start_time);
        $endTime = Carbon::parse($this->end_time);
        // return $startTime -> diffInHours($endTime);
        $duration = $startTime->diff($endTime);
        $hours = $duration->h;
        $minutes = $duration->i;
        return "$hours jam $minutes menit";
    }
}
