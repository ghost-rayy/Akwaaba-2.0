<?php

namespace App\Livewire\Company;

use App\Models\Department;
use App\Models\User;
use Livewire\Component;

class Departments extends Component
{
    public $editing = false;
    public $departmentId = null;
    public $name = '';
    public $head_id = '';
    public $supervisor_id = '';
    public $showDeleteModal = false;

    protected function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'head_id' => 'nullable|exists:users,id',
            'supervisor_id' => 'nullable|exists:users,id',
        ];
    }

    public function create()
    {
        $this->resetForm();
        $this->editing = true;
        $this->departmentId = null;
    }

    public function edit($id)
    {
        $department = Department::findOrFail($id);
        $this->departmentId = $department->id;
        $this->name = $department->name;
        $this->head_id = $department->head_id ?? '';
        $this->supervisor_id = $department->supervisor_id ?? '';
        $this->editing = true;
    }

    public function save()
    {
        $this->validate();
        $company = auth()->user()->company;

        if ($this->departmentId) {
            $department = Department::findOrFail($this->departmentId);
            $department->update([
                'name' => $this->name,
                'head_id' => $this->head_id ?: null,
                'supervisor_id' => $this->supervisor_id ?: null,
            ]);
        } else {
            Department::create([
                'company_id' => $company->id,
                'name' => $this->name,
                'head_id' => $this->head_id ?: null,
                'supervisor_id' => $this->supervisor_id ?: null,
            ]);
        }

        $this->resetForm();
    }

    public function confirmDelete($id)
    {
        $this->departmentId = $id;
        $this->showDeleteModal = true;
    }

    public function delete()
    {
        Department::findOrFail($this->departmentId)->delete();
        $this->showDeleteModal = false;
        $this->departmentId = null;
    }

    public function cancel()
    {
        $this->resetForm();
    }

    private function resetForm()
    {
        $this->editing = false;
        $this->departmentId = null;
        $this->name = '';
        $this->head_id = '';
        $this->supervisor_id = '';
        $this->showDeleteModal = false;
    }

    public function render()
    {
        $company = auth()->user()->company;
        $companyUsers = User::where('company_id', $company->id)
            ->whereIn('role', ['company_admin', 'hr_staff'])
            ->orderBy('name')
            ->get();

        return view('livewire.company.departments', [
            'departments' => Department::where('company_id', $company->id)
                ->with('head', 'supervisor')
                ->orderBy('name')
                ->get(),
            'companyUsers' => $companyUsers,
        ])->layout('layouts.company');
    }
}
