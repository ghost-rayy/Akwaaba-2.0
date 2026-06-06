<?php

namespace App\Livewire\Company;

use App\Exports\AttendanceExport;
use App\Exports\EvaluationExport;
use App\Exports\PersonnelExport;
use App\Models\Attendance;
use App\Models\Enrollment;
use App\Models\Evaluation;
use Maatwebsite\Excel\Facades\Excel;
use Livewire\Component;

class Report extends Component
{
    public $dateRange = 'month';
    public $startDate;
    public $endDate;

    public function mount()
    {
        $this->startDate = now()->startOfMonth()->format('Y-m-d');
        $this->endDate = now()->format('Y-m-d');
    }

    public function downloadPersonnel()
    {
        return Excel::download(
            new PersonnelExport(auth()->user()->company_id),
            'personnel_' . now()->format('Ymd') . '.xlsx'
        );
    }

    public function downloadAttendance()
    {
        return Excel::download(
            new AttendanceExport(auth()->user()->company_id, $this->startDate, $this->endDate),
            'attendance_' . $this->startDate . '_' . $this->endDate . '.xlsx'
        );
    }

    public function downloadEvaluations()
    {
        return Excel::download(
            new EvaluationExport(auth()->user()->company_id),
            'evaluations_' . now()->format('Ymd') . '.xlsx'
        );
    }

    public function render()
    {
        $companyId = auth()->user()->company_id;

        $totalPersonnel = Enrollment::where('company_id', $companyId)->count();
        $personnelByStatus = Enrollment::where('company_id', $companyId)
            ->selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status');

        $recentAttendance = Attendance::where('company_id', $companyId)
            ->whereBetween('date', [$this->startDate, $this->endDate])
            ->selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status');

        $totalAttendance = array_sum($recentAttendance->toArray());

        $avgScores = Evaluation::where('company_id', $companyId)
            ->selectRaw('avg(punctuality_score) as punctuality, avg(performance_score) as performance, avg(attitude_score) as attitude, avg(teamwork_score) as teamwork, avg(overall_score) as overall')
            ->first();

        $totalEvaluations = Evaluation::where('company_id', $companyId)->count();
        $endorsedCount = Enrollment::where('company_id', $companyId)->where('status', 'endorsed')->count();

        return view('livewire.company.report', [
            'totalPersonnel' => $totalPersonnel,
            'personnelByStatus' => $personnelByStatus,
            'totalAttendance' => $totalAttendance,
            'recentAttendance' => $recentAttendance,
            'avgScores' => $avgScores,
            'totalEvaluations' => $totalEvaluations,
            'endorsedCount' => $endorsedCount,
        ])->layout('layouts.company');
    }
}
