<?php

namespace App\Livewire;

use Carbon\Carbon;
use Livewire\Component;
use App\Models\Schedule;
use App\Models\Attendance;
use App\Models\Leave;
use Illuminate\Support\Facades\Auth;

class Presensi extends Component
{
    public $latitude;
    public $longitude;
    public $isInsideRadius = false;
    public function render()
    {
        // Get schedule data
        $schedule = Schedule::where('user_id', Auth::user()->id)->first();
        $attendance = Attendance::where('user_id', Auth::user()->id)
            ->whereDate('created_at', date('Y-m-d'))->first();
        // ->whereDate('created_at', Carbon::today())->first();
        // dd($attendance);
        return view('livewire.presensi', [
            'schedule' => $schedule,
            'isInsideRadius' => $this->isInsideRadius,
            'attendance' => $attendance,
        ]);
    }

    public function store()
    {
        $this->validate([
            'latitude' => 'required',
            'longitude' => 'required',
        ]);

        $schedule = Schedule::where('user_id', Auth::user()->id)->first();
        //get today 
        $today = Carbon::today()->format('Y-m-d');
        $approvedLeave = Leave::where('user_id', Auth::user()->id)
        ->where('status', 'approved')
        ->whereDate('start_date', '<=', $today)
        ->whereDate('end_date', '>=', $today)
        ->exists();
        if($approvedLeave){
            session()->flash('error', 'Anda tidak dapat melakukan presensi karena sedang cuti');
            return;
        }
        if ($schedule) {
            $attendance = Attendance::where('user_id', Auth::user()->id)
                ->whereDate('created_at', date('Y-m-d'))->first();
            // ->whereDate('created_at', Carbon::today())->first();
            if (!$attendance) {
                $attendance = Attendance::create([
                    'user_id' => Auth::user()->id,
                    'schedule_latitude' => $schedule->office->latitude,
                    'schedule_longitude' => $schedule->office->longitude,
                    'schedule_start_time' => $schedule->shift->start_time,
                    'schedule_end_time' => $schedule->shift->end_time,
                    'start_latitude' => $this->latitude,
                    'start_longitude' => $this->longitude,
                    'start_time' => Carbon::now()->toTimeString(),
                    'end_time' => Carbon::now()->toTimeString(),
                ]);
            } else {
                $attendance->update([
                    'end_latitude' => $this->latitude,
                    'end_longitude' => $this->longitude,
                    'end_time' => Carbon::now()->toTimeString(),
                ]);
            }
            return redirect('admin/attendances');
            // return redirect()->route('presensi', [
            //     'isInsideRadius' => false,
            //     'schedule' => $schedule,
            // ]);
        }
    }
}
