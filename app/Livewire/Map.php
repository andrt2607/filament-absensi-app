<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Attendance;

class Map extends Component
{
    public function render()
    {
        // Get all attendances with user relationship
        $attendances = Attendance::with('user')->get();
        return view('livewire.map', [
            'attendances' => $attendances
        ]);
    }
}
