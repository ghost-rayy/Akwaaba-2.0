<?php

namespace App\Livewire\Company;

use App\Models\Attendance as AttendanceModel;
use App\Support\DispatchesToast;
use Livewire\Component;
use Livewire\WithPagination;

class Attendance extends Component
{
    use DispatchesToast, WithPagination;

    public $selectedDate;
    public $search = '';
    public $sortField = 'check_in';
    public $sortDirection = 'desc';

    protected $queryString = [
        'search' => ['except' => ''],
        'sortField' => ['except' => 'check_in'],
        'sortDirection' => ['except' => 'desc'],
    ];

    public function mount()
    {
        $this->selectedDate = now()->format('Y-m-d');
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedSelectedDate()
    {
        $this->resetPage();
    }

    public function sortBy(string $field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }

        $this->resetPage();
    }

    public function validateCheckIn(int $attendanceId)
    {
        $attendance = $this->findCompanyAttendance($attendanceId);

        if (! $attendance->check_in) {
            $this->toastError('No check-in to validate.');

            return;
        }

        $attendance->update([
            'check_in_validated_at' => now(),
            'check_in_validated_by' => auth()->id(),
        ]);

        $this->toastSuccess($attendance->user->name."'s check-in validated.");
    }

    public function validateCheckOut(int $attendanceId)
    {
        $attendance = $this->findCompanyAttendance($attendanceId);

        if (! $attendance->check_out) {
            $this->toastError('No check-out to validate.');

            return;
        }

        $attendance->update([
            'check_out_validated_at' => now(),
            'check_out_validated_by' => auth()->id(),
        ]);

        $this->toastSuccess($attendance->user->name."'s check-out validated.");
    }

    public function validateAbsence(int $attendanceId)
    {
        $attendance = $this->findCompanyAttendance($attendanceId);

        if (! $attendance->isAbsent()) {
            $this->toastError('This record is not an absence submission.');

            return;
        }

        $attendance->update([
            'absence_validated_at' => now(),
            'absence_validated_by' => auth()->id(),
        ]);

        $this->toastSuccess($attendance->user->name."'s absence validated.");
    }

    protected function findCompanyAttendance(int $attendanceId): AttendanceModel
    {
        return AttendanceModel::where('company_id', auth()->user()->company_id)
            ->with('user')
            ->findOrFail($attendanceId);
    }

    protected function baseQuery()
    {
        return AttendanceModel::query()
            ->where('company_id', auth()->user()->company_id)
            ->whereDate('date', $this->selectedDate)
            ->where(function ($query) {
                $query->whereNotNull('check_in')
                    ->orWhereNotNull('check_out')
                    ->orWhere('status', 'absent');
            })
            ->with(['user.enrollment']);
    }

    protected function applySearch($query)
    {
        if ($this->search === '') {
            return $query;
        }

        $term = trim($this->search);

        return $query->where(function ($q) use ($term) {
            $q->whereHas('user', fn ($userQuery) => $userQuery->where('name', 'like', "%{$term}%"))
                ->orWhereHas('user.enrollment', fn ($enrollmentQuery) => $enrollmentQuery->where('nss_number', 'like', "%{$term}%"));
        });
    }

    protected function applySort($query)
    {
        $direction = $this->sortDirection;

        return match ($this->sortField) {
            'name' => $query->orderBy(
                \App\Models\User::select('name')->whereColumn('users.id', 'attendance.user_id'),
                $direction
            ),
            'nss_number' => $query->orderBy(
                \App\Models\Enrollment::select('nss_number')
                    ->whereColumn('enrollments.user_id', 'attendance.user_id')
                    ->limit(1),
                $direction
            ),
            'status' => $query->orderBy('status', $direction),
            'check_out' => $query->orderBy('check_out', $direction),
            'date' => $query->orderBy('date', $direction)->orderBy('check_in', $direction),
            default => $query->orderBy('check_in', $direction),
        };
    }

    public function render()
    {
        $dailyQuery = $this->applySort($this->applySearch($this->baseQuery()));
        $records = $dailyQuery->paginate(15);

        $historyQuery = AttendanceModel::query()
            ->where('company_id', auth()->user()->company_id)
            ->where(function ($query) {
                $query->whereNotNull('check_in')
                    ->orWhereNotNull('check_out')
                    ->orWhere('status', 'absent');
            })
            ->with(['user.enrollment']);

        $historyQuery = $this->applySearch($historyQuery);
        $history = $this->applySort($historyQuery)->limit(50)->get();

        $pendingCount = AttendanceModel::where('company_id', auth()->user()->company_id)
            ->where(function ($query) {
                $query->where(function ($q) {
                    $q->whereNotNull('check_in')->whereNull('check_in_validated_at');
                })->orWhere(function ($q) {
                    $q->whereNotNull('check_out')->whereNull('check_out_validated_at');
                })->orWhere(function ($q) {
                    $q->where('status', 'absent')->whereNull('absence_validated_at');
                });
            })
            ->count();

        return view('livewire.company.attendance', [
            'records' => $records,
            'history' => $history,
            'pendingCount' => $pendingCount,
        ])->layout('layouts.company');
    }
}
