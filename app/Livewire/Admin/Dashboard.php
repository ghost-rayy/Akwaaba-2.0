<?php

namespace App\Livewire\Admin;

use App\Models\Attendance;
use App\Models\Company;
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
        $totalDepartments = \App\Models\Department::count();

        $activePersonnel = Enrollment::whereIn('status', ['validated', 'active'])->count();
        $pendingReview = Enrollment::where('status', 'pending_review')->count();
        $shortlisted = Enrollment::where('status', 'shortlisted')->count();
        $endorsed = Enrollment::where('status', 'endorsed')->count();

        $todayPresent = Attendance::whereDate('date', today())->where('status', 'present')->count();
        $todayAttendance = Attendance::whereDate('date', today())->count();

        $recentCompanies = Company::latest()->take(5)->get();

        $companyStats = Company::withCount([
            'enrollments',
            'enrollments as active_enrollments_count' => fn($q) => $q->whereIn('status', ['validated', 'active']),
            'departments',
        ])->latest()->take(6)->get();

        return view('livewire.admin.dashboard', compact(
            'totalCompanies', 'totalPersonnel', 'totalUsers', 'totalDepartments',
            'activePersonnel', 'pendingReview', 'shortlisted', 'endorsed',
            'todayPresent', 'todayAttendance',
            'recentCompanies', 'companyStats',
        ))->layout('layouts.admin');
    }
}
