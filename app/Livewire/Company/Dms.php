<?php

namespace App\Livewire\Company;

use App\Models\Department;
use App\Models\Document;
use App\Models\Enrollment;
use Livewire\Component;

class Dms extends Component
{
    public $selectedYear = null;
    public $selectedDepartment = null;
    public $search = '';
    public $filterType = '';

    public function selectYear($year)
    {
        $this->selectedYear = $year;
        $this->selectedDepartment = null;
        $this->search = '';
        $this->filterType = '';
    }

    public function selectDepartment($departmentId)
    {
        $this->selectedDepartment = $departmentId === '0' ? 0 : (int) $departmentId;
        $this->search = '';
        $this->filterType = '';
    }

    public function backToYears()
    {
        $this->selectedYear = null;
        $this->selectedDepartment = null;
        $this->search = '';
        $this->filterType = '';
    }

    public function backToDepartments()
    {
        $this->selectedDepartment = null;
        $this->search = '';
        $this->filterType = '';
    }

    public function clearFilters()
    {
        $this->search = '';
        $this->filterType = '';
    }

    public function render()
    {
        $companyId = auth()->user()->company_id;

        if ($this->selectedYear && $this->selectedDepartment !== null) {
            $documents = Document::where('documents.company_id', $companyId)
                ->join('enrollments', 'documents.user_id', '=', 'enrollments.user_id')
                ->where('enrollments.company_id', $companyId)
                ->where('enrollments.nss_year', $this->selectedYear)
                ->where(function ($q) {
                    if ($this->selectedDepartment === 0) {
                        $q->whereNull('enrollments.department_id');
                    } else {
                        $q->where('enrollments.department_id', $this->selectedDepartment);
                    }
                })
                ->when($this->search, function ($q) {
                    $q->where(function ($q) {
                        $q->where('documents.original_name', 'like', '%' . $this->search . '%')
                          ->orWhere('documents.type', 'like', '%' . $this->search . '%')
                          ->orWhereHas('user', function ($q) {
                              $q->where('name', 'like', '%' . $this->search . '%');
                          });
                    });
                })
                ->when($this->filterType, function ($q) {
                    $q->where('documents.type', $this->filterType);
                })
                ->select('documents.*')
                ->with('user')
                ->latest('documents.created_at')
                ->get();

            $documentTypes = Document::where('company_id', $companyId)
                ->whereHas('user.enrollment', fn($q) => $q->where('company_id', $companyId))
                ->distinct()
                ->pluck('type');

            return view('livewire.company.dms', [
                'level' => 'documents',
                'documents' => $documents,
                'documentTypes' => $documentTypes,
                'departmentName' => $this->selectedDepartment === 0 ? 'Unassigned' : (Department::find($this->selectedDepartment)?->name ?? 'Unknown'),
            ])->layout('layouts.company');
        }

        if ($this->selectedYear) {
            $departmentData = Document::selectRaw('enrollments.department_id, COUNT(*) as total')
                ->join('enrollments', 'documents.user_id', '=', 'enrollments.user_id')
                ->where('documents.company_id', $companyId)
                ->where('enrollments.company_id', $companyId)
                ->where('enrollments.nss_year', $this->selectedYear)
                ->whereNotNull('enrollments.department_id')
                ->groupBy('enrollments.department_id')
                ->get();

            $departmentIds = $departmentData->pluck('department_id');
            $departments = Department::whereIn('id', $departmentIds)->orderBy('name')->get();
            $departmentCounts = $departmentData->pluck('total', 'department_id');

            $unassignedCount = Document::where('documents.company_id', $companyId)
                ->join('enrollments', 'documents.user_id', '=', 'enrollments.user_id')
                ->where('enrollments.company_id', $companyId)
                ->where('enrollments.nss_year', $this->selectedYear)
                ->whereNull('enrollments.department_id')
                ->count();

            return view('livewire.company.dms', [
                'level' => 'departments',
                'departments' => $departments,
                'departmentCounts' => $departmentCounts,
                'unassignedCount' => $unassignedCount,
            ])->layout('layouts.company');
        }

        $years = Document::selectRaw('enrollments.nss_year, COUNT(*) as total')
            ->join('enrollments', 'documents.user_id', '=', 'enrollments.user_id')
            ->where('documents.company_id', $companyId)
            ->where('enrollments.company_id', $companyId)
            ->whereNotNull('enrollments.nss_year')
            ->groupBy('enrollments.nss_year')
            ->orderBy('enrollments.nss_year', 'desc')
            ->get();

        return view('livewire.company.dms', [
            'level' => 'years',
            'years' => $years,
        ])->layout('layouts.company');
    }
}
