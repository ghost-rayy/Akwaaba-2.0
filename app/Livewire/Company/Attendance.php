<?php

namespace App\Livewire\Company;

use App\Models\Attendance as AttendanceModel;
use App\Models\Enrollment;
use Livewire\Component;

class Attendance extends Component
{
    public $selectedDate;
    public $records = [];
    public $saving = false;

    public function mount()
    {
        $this->selectedDate = now()->format('Y-m-d');
        $this->loadRecords();
    }

    public function loadRecords()
    {
        $company = auth()->user()->company;

        $personnel = Enrollment::where('company_id', $company->id)
            ->whereIn('status', ['endorsed', 'active'])
            ->with('user')
            ->get();

        $existing = AttendanceModel::where('company_id', $company->id)
            ->where('date', $this->selectedDate)
            ->get()
            ->keyBy('user_id');

        $this->records = $personnel->map(function ($e) use ($existing) {
            $att = $existing->get($e->user_id);
            return [
                'enrollment_id' => $e->id,
                'user_id' => $e->user_id,
                'name' => $e->user->name,
                'nss_number' => $e->nss_number,
                'status' => $att?->status ?? 'present',
                'check_in' => $att?->check_in ? substr($att->check_in, 0, 5) : '',
                'check_out' => $att?->check_out ? substr($att->check_out, 0, 5) : '',
                'remarks' => $att?->remarks ?? '',
            ];
        })->toArray();
    }

    public function updatedSelectedDate()
    {
        $this->loadRecords();
    }

    public function saveAll()
    {
        $company = auth()->user()->company;

        foreach ($this->records as $record) {
            AttendanceModel::updateOrCreate(
                [
                    'user_id' => $record['user_id'],
                    'company_id' => $company->id,
                    'date' => $this->selectedDate,
                ],
                [
                    'status' => $record['status'],
                    'check_in' => $record['check_in'] ?: null,
                    'check_out' => $record['check_out'] ?: null,
                    'remarks' => $record['remarks'] ?: null,
                ]
            );
        }

        session()->flash('message', 'Attendance saved for ' . date('d M Y', strtotime($this->selectedDate)) . '.');
    }

    public function render()
    {
        return view('livewire.company.attendance')->layout('layouts.company');
    }
}
