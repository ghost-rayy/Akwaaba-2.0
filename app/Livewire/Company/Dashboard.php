<?php

namespace App\Livewire\Company;

use Livewire\Component;

class Dashboard extends Component
{
    public function render()
    {
        $user = auth()->user();
        $company = $user->company;

        return view('livewire.company.dashboard', [
            'totalPersonnel' => $company?->enrollments()->count() ?? 0,
            'activePersonnel' => $company?->enrollments()->whereIn('status', ['active', 'validated', 'endorsed'])->count() ?? 0,
            'pendingReview' => $company?->enrollments()->where('status', 'pending_review')->count() ?? 0,
            'totalDepartments' => $company?->departments()->count() ?? 0,
        ])->layout('layouts.company');
    }
}
