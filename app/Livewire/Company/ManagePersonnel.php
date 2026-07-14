<?php

namespace App\Livewire\Company;

use App\Models\Department;
use App\Models\Enrollment;
use App\Support\DispatchesToast;
use Livewire\Component;
use Livewire\WithPagination;

class ManagePersonnel extends Component
{
    use DispatchesToast, WithPagination;

    public $search = '';
    public $filterDepartment = '';
    public $filterStatus = 'validated';
    public $assigningPersonnelId = null;
    public $assignDepartmentId = '';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function startAssign($enrollmentId)
    {
        $this->assigningPersonnelId = $enrollmentId;
        $enrollment = Enrollment::find($enrollmentId);
        $this->assignDepartmentId = (string) ($enrollment->department_id ?? '');
    }

    public function saveDepartment()
    {
        $this->validate(['assignDepartmentId' => 'required|exists:departments,id']);

        Enrollment::where('company_id', auth()->user()->company_id)
            ->where('id', $this->assigningPersonnelId)
            ->update(['department_id' => $this->assignDepartmentId]);

        $this->assigningPersonnelId = null;
        $this->assignDepartmentId = '';

        $this->toastSuccess('Department assigned successfully.');
    }

    public function render()
    {
        $company = auth()->user()->company;

        $query = Enrollment::where('company_id', $company->id)
            ->with(['user', 'user.personalInfo', 'user.passportPhoto', 'department']);

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('nss_number', 'like', '%' . $this->search . '%')
                    ->orWhereHas('user', function ($uq) {
                        $uq->where('name', 'like', '%' . $this->search . '%')
                            ->orWhere('email', 'like', '%' . $this->search . '%')
                            ->orWhere('phone', 'like', '%' . $this->search . '%');
                    });
            });
        }

        if ($this->filterDepartment) {
            $query->where('department_id', $this->filterDepartment);
        }

        if ($this->filterStatus) {
            $query->where('status', $this->filterStatus);
        }

        return view('livewire.company.manage-personnel', [
            'enrollments' => $query->latest()->paginate(10),
            'departments' => Department::where('company_id', $company->id)->get(),
            'statuses' => ['pending_forms', 'pending_review', 'shortlisted', 'rejected', 'endorsed', 'validated', 'active', 'completed'],
        ])->layout('layouts.company');
    }
}
