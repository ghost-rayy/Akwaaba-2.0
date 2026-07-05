<?php

namespace App\Livewire\Personnel;

use App\Models\Attendance as AttendanceModel;
use App\Support\DispatchesToast;
use Livewire\Component;

class Attendance extends Component
{
    use DispatchesToast;

    public $todayRecord;
    public $checkedIn = false;
    public $checkedOut = false;
    public $markedAbsent = false;
    public $canCheckIn = false;
    public $statusMessage = '';
    public $enrollmentStatus = '';
    public $showAbsentForm = false;
    public $absenceReason = '';

    public function mount()
    {
        $this->checkEligibility();
        $this->loadToday();
    }

    public function checkEligibility()
    {
        $user = auth()->user();
        $enrollment = $user->enrollment;

        if (! $enrollment) {
            $this->canCheckIn = false;
            $this->enrollmentStatus = 'none';
            $this->statusMessage = 'You are not enrolled in any company.';

            return;
        }

        $this->enrollmentStatus = $enrollment->status;

        if (in_array($enrollment->status, ['validated', 'active'], true)) {
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
            ->whereDate('date', now()->format('Y-m-d'))
            ->first();

        $this->markedAbsent = $this->todayRecord?->isAbsent() ?? false;
        $this->checkedIn = ! is_null($this->todayRecord?->check_in);
        $this->checkedOut = ! is_null($this->todayRecord?->check_out);
    }

    public function checkIn()
    {
        $this->checkEligibility();

        if (! $this->canCheckIn) {
            $this->toastError($this->statusMessage);

            return;
        }

        if ($this->todayRecord?->isAbsent()) {
            $this->toastError('You have already marked yourself absent for today.');

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
                'check_in_validated_at' => null,
                'check_in_validated_by' => null,
            ]
        );

        $this->showAbsentForm = false;
        $this->loadToday();
        $this->toastSuccess('Check-in submitted. Awaiting company validation.');
    }

    public function checkOut()
    {
        $this->checkEligibility();

        if (! $this->canCheckIn) {
            $this->toastError($this->statusMessage);

            return;
        }

        $user = auth()->user();

        $attendance = AttendanceModel::where('user_id', $user->id)
            ->whereDate('date', now()->format('Y-m-d'))
            ->firstOrFail();

        $attendance->update([
            'check_out' => now()->format('H:i:s'),
            'check_out_validated_at' => null,
            'check_out_validated_by' => null,
        ]);

        $this->loadToday();
        $this->toastSuccess('Check-out submitted. Awaiting company validation.');
    }

    public function markAbsent()
    {
        $this->checkEligibility();

        if (! $this->canCheckIn) {
            $this->toastError($this->statusMessage);

            return;
        }

        $this->validate([
            'absenceReason' => 'required|string|min:5|max:1000',
        ], [
            'absenceReason.required' => 'Please provide a reason for your absence.',
            'absenceReason.min' => 'The absence reason must be at least 5 characters.',
        ]);

        if ($this->todayRecord?->check_in) {
            $this->toastError('You have already checked in today and cannot mark absent.');

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
                'status' => 'absent',
                'remarks' => $this->absenceReason,
                'check_in' => null,
                'check_out' => null,
                'check_in_validated_at' => null,
                'check_in_validated_by' => null,
                'check_out_validated_at' => null,
                'check_out_validated_by' => null,
                'absence_validated_at' => null,
                'absence_validated_by' => null,
            ]
        );

        $this->showAbsentForm = false;
        $this->absenceReason = '';
        $this->loadToday();
        $this->toastSuccess('Absence submitted. Awaiting company validation.');
    }

    public function render()
    {
        $user = auth()->user();

        $history = AttendanceModel::where('user_id', $user->id)
            ->latest('date')
            ->take(30)
            ->get();

        $stats = [
            'present' => AttendanceModel::where('user_id', $user->id)->where('status', 'present')->whereNotNull('check_in_validated_at')->count(),
            'absent' => AttendanceModel::where('user_id', $user->id)->where('status', 'absent')->whereNotNull('absence_validated_at')->count(),
            'late' => AttendanceModel::where('user_id', $user->id)->where('status', 'late')->whereNotNull('check_in_validated_at')->count(),
        ];

        return view('livewire.personnel.attendance', [
            'history' => $history,
            'stats' => $stats,
        ])->layout('layouts.personnel');
    }
}
