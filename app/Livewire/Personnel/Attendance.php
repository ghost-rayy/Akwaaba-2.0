<?php

namespace App\Livewire\Personnel;

use App\Models\Attendance as AttendanceModel;
use Livewire\Component;

class Attendance extends Component
{
    public $todayRecord;
    public $checkedIn = false;
    public $checkedOut = false;
    public $canCheckIn = false;
    public $statusMessage = '';
    public $enrollmentStatus = '';

    public function mount()
    {
        $this->checkEligibility();
        $this->loadToday();
    }

    public function checkEligibility()
    {
        $user = auth()->user();
        $enrollment = $user->enrollment;

        if (!$enrollment) {
            $this->canCheckIn = false;
            $this->enrollmentStatus = 'none';
            $this->statusMessage = 'You are not enrolled in any company.';
            return;
        }

        $this->enrollmentStatus = $enrollment->status;

        if ($enrollment->status === 'validated') {
            $this->canCheckIn = true;
            $this->statusMessage = '';
        } elseif ($enrollment->status === 'rejected') {
            $this->canCheckIn = false;
            $this->statusMessage = 'Your enrollment has been rejected.';
        } else {
            $this->canCheckIn = false;
            $this->statusMessage = 'Attendance is only available after your posting letter has been validated by the company admin.';
        }
    }

    public function loadToday()
    {
        $user = auth()->user();
        $this->todayRecord = AttendanceModel::where('user_id', $user->id)
            ->where('date', now()->format('Y-m-d'))
            ->first();

        $this->checkedIn = !is_null($this->todayRecord?->check_in);
        $this->checkedOut = !is_null($this->todayRecord?->check_out);
    }

    public function checkIn()
    {
        $this->checkEligibility();

        if (!$this->canCheckIn) {
            session()->flash('error', $this->statusMessage);
            return;
        }

        $user = auth()->user();

        AttendanceModel::updateOrCreate(
            [
                'user_id' => $user->id,
                'company_id' => $user->company_id,
                'date' => now()->format('Y-m-d'),
            ],
            [
                'check_in' => now()->format('H:i:s'),
                'status' => now()->hour >= 9 ? 'late' : 'present',
            ]
        );

        $this->loadToday();
        session()->flash('message', 'Checked in successfully.');
    }

    public function checkOut()
    {
        $this->checkEligibility();

        if (!$this->canCheckIn) {
            session()->flash('error', $this->statusMessage);
            return;
        }

        $user = auth()->user();

        $attendance = AttendanceModel::where('user_id', $user->id)
            ->where('date', now()->format('Y-m-d'))
            ->firstOrFail();

        $attendance->update(['check_out' => now()->format('H:i:s')]);

        $this->loadToday();
        session()->flash('message', 'Checked out successfully.');
    }

    public function render()
    {
        $user = auth()->user();

        $history = AttendanceModel::where('user_id', $user->id)
            ->latest('date')
            ->take(30)
            ->get();

        $stats = [
            'present' => AttendanceModel::where('user_id', $user->id)->where('status', 'present')->count(),
            'absent' => AttendanceModel::where('user_id', $user->id)->where('status', 'absent')->count(),
            'late' => AttendanceModel::where('user_id', $user->id)->where('status', 'late')->count(),
        ];

        return view('livewire.personnel.attendance', [
            'history' => $history,
            'stats' => $stats,
        ])->layout('layouts.personnel');
    }
}
