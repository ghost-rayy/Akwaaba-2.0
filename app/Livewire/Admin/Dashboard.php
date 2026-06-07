<?php

namespace App\Livewire\Admin;

use App\Models\Attendance;
use App\Models\Company;
use App\Models\Department;
use App\Models\Enrollment;
use App\Models\User;
use Livewire\Component;

class Dashboard extends Component
{
    public function render()
    {
        $totalCompanies = Company::count();
        $totalPersonnel = Enrollment::count();
        $totalUsers = User::count();
        $totalDepartments = Department::count();

        // Status distribution
        $statusCounts = [
            'pending_forms' => Enrollment::where('status', 'pending_forms')->count(),
            'pending_review' => Enrollment::where('status', 'pending_review')->count(),
            'shortlisted' => Enrollment::where('status', 'shortlisted')->count(),
            'endorsed' => Enrollment::where('status', 'endorsed')->count(),
            'validated' => Enrollment::where('status', 'validated')->count(),
            'active' => Enrollment::where('status', 'active')->count(),
            'rejected' => Enrollment::where('status', 'rejected')->count(),
        ];
        $activePersonnel = $statusCounts['validated'] + $statusCounts['active'];
        $pendingReview = $statusCounts['pending_review'];
        $shortlisted = $statusCounts['shortlisted'];
        $endorsed = $statusCounts['endorsed'];

        // Today's attendance breakdown
        $todayPresent = Attendance::whereDate('date', today())->where('status', 'present')->count();
        $todayLate = Attendance::whereDate('date', today())->where('status', 'late')->count();
        $todayAbsent = Attendance::whereDate('date', today())->where('status', 'absent')->count();
        $todayTotal = $todayPresent + $todayLate + $todayAbsent;

        // Weekly attendance trend (last 7 days)
        $weeklyAttendance = collect();
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $weeklyAttendance->push([
                'date' => $date->format('D'),
                'present' => Attendance::whereDate('date', $date)->where('status', 'present')->count(),
                'late' => Attendance::whereDate('date', $date)->where('status', 'late')->count(),
                'absent' => Attendance::whereDate('date', $date)->where('status', 'absent')->count(),
            ]);
        }

        // NSS year distribution
        $yearDistribution = Enrollment::selectRaw('nss_year, count(*) as total')
            ->whereNotNull('nss_year')
            ->groupBy('nss_year')
            ->orderBy('nss_year', 'desc')
            ->get();

        // Company comparison table
        $companyStats = Company::withCount([
            'enrollments',
            'enrollments as active_count' => fn($q) => $q->whereIn('status', ['validated', 'active']),
            'enrollments as pending_count' => fn($q) => $q->where('status', 'pending_review'),
            'departments',
        ])->latest()->take(10)->get();

        // Recent enrollments
        $recentEnrollments = Enrollment::with(['user', 'company'])
            ->latest()
            ->take(6)
            ->get();

        // Pipeline summary (total users per role)
        $roleCounts = [
            'company_admin' => User::where('role', 'company_admin')->count(),
            'hr_staff' => User::where('role', 'hr_staff')->count(),
            'nss_personnel' => User::where('role', 'nss_personnel')->count(),
        ];

        return view('livewire.admin.dashboard', compact(
            'totalCompanies', 'totalPersonnel', 'totalUsers', 'totalDepartments',
            'statusCounts', 'activePersonnel', 'pendingReview', 'shortlisted', 'endorsed',
            'todayPresent', 'todayLate', 'todayAbsent', 'todayTotal',
            'weeklyAttendance', 'yearDistribution', 'companyStats', 'recentEnrollments',
            'roleCounts',
        ))->layout('layouts.admin');
    }
}
