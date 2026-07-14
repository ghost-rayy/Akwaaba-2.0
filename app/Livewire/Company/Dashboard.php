<?php

namespace App\Livewire\Company;

use App\Models\Attendance;
use App\Models\Department;
use App\Models\EndorsedLetter;
use App\Models\Enrollment;
use Livewire\Component;

class Dashboard extends Component
{
    public function render()
    {
        $user = auth()->user();
        $company = $user->company;

        $statusCounts = Enrollment::where('company_id', $company?->id)
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $totalPersonnel = $statusCounts->sum();
        $activePersonnel = (int) ($statusCounts['validated'] ?? 0) + (int) ($statusCounts['active'] ?? 0);
        $pendingReview = (int) ($statusCounts['pending_review'] ?? 0);
        $shortlisted = (int) ($statusCounts['shortlisted'] ?? 0);
        $endorsed = (int) ($statusCounts['endorsed'] ?? 0);
        $totalDepartments = $company?->departments()->count() ?? 0;

        $todayPresent = Attendance::where('company_id', $company?->id)
            ->whereDate('date', today())
            ->where('status', 'present')
            ->count();

        $todayTotal = Attendance::where('company_id', $company?->id)
            ->whereDate('date', today())
            ->count();

        $recentOnboardings = $company
            ? Enrollment::where('company_id', $company->id)
                ->with('user')
                ->latest()
                ->take(5)
                ->get()
            : collect();

        $pendingValidations = $company
            ? EndorsedLetter::whereHas('enrollment', fn($q) => $q->where('company_id', $company->id))
                ->whereNotNull('validated_file_path')
                ->whereNull('validated_by')
                ->with(['enrollment.user', 'enrollment.department'])
                ->latest()
                ->take(5)
                ->get()
            : collect();

        $departmentDistribution = $company
            ? Department::where('company_id', $company->id)
                ->withCount(['enrollments' => fn($q) => $q->whereIn('status', ['validated', 'active'])])
                ->get()
            : collect();

        return view('livewire.company.dashboard', compact(
            'totalPersonnel', 'activePersonnel', 'pendingReview',
            'shortlisted', 'endorsed', 'totalDepartments',
            'todayPresent', 'todayTotal',
            'recentOnboardings', 'pendingValidations', 'departmentDistribution',
        ))->layout('layouts.company');
    }
}
