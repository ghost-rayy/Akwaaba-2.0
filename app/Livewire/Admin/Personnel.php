<?php

namespace App\Livewire\Admin;

use App\Models\Enrollment;
use Livewire\Component;
use Livewire\WithPagination;

class Personnel extends Component
{
    use WithPagination;

    public $search = '';
    public $filterCompany = '';
    public $filterStatus = '';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = Enrollment::with(['user', 'company', 'department']);

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('nss_number', 'like', '%' . $this->search . '%')
                    ->orWhereHas('user', function ($uq) {
                        $uq->where('name', 'like', '%' . $this->search . '%')
                            ->orWhere('email', 'like', '%' . $this->search . '%');
                    });
            });
        }

        if ($this->filterCompany) {
            $query->where('company_id', $this->filterCompany);
        }

        if ($this->filterStatus) {
            $query->where('status', $this->filterStatus);
        }

        $statuses = ['pending_forms', 'pending_review', 'shortlisted', 'rejected', 'endorsed', 'validated', 'active', 'completed'];

        return view('livewire.admin.personnel', [
            'enrollments' => $query->latest()->paginate(15),
            'statuses' => $statuses,
            'companies' => \App\Models\Company::orderBy('name')->get(),
        ])->layout('layouts.admin');
    }
}
