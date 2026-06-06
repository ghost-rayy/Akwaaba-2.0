<?php

namespace App\Livewire\Admin;

use App\Models\Company;
use Livewire\Component;
use Livewire\WithPagination;

class Companies extends Component
{
    use WithPagination;

    public $search = '';
    public $editingId = null;
    public $name = '';
    public $email = '';
    public $phone = '';
    public $location = '';
    public $contact_person = '';
    public $registration_number = '';
    public $is_active = true;

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function create()
    {
        $this->reset(['editingId', 'name', 'email', 'phone', 'location', 'contact_person', 'registration_number']);
        $this->is_active = true;
    }

    public function edit($id)
    {
        $company = Company::findOrFail($id);
        $this->editingId = $company->id;
        $this->name = $company->name;
        $this->email = $company->email;
        $this->phone = $company->phone;
        $this->location = $company->location;
        $this->contact_person = $company->contact_person;
        $this->registration_number = $company->registration_number;
        $this->is_active = $company->is_active;
    }

    public function save()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'location' => 'nullable|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'registration_number' => 'nullable|string|max:255|unique:companies,registration_number,' . ($this->editingId ?? 'NULL'),
        ]);

        Company::updateOrCreate(
            ['id' => $this->editingId],
            [
                'name' => $this->name,
                'email' => $this->email,
                'phone' => $this->phone,
                'location' => $this->location,
                'contact_person' => $this->contact_person,
                'registration_number' => $this->registration_number,
                'is_active' => $this->is_active,
            ]
        );

        session()->flash('message', $this->editingId ? 'Company updated.' : 'Company created.');
        $this->reset(['editingId', 'name', 'email', 'phone', 'location', 'contact_person', 'registration_number']);
    }

    public function render()
    {
        $query = Company::query();

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('email', 'like', '%' . $this->search . '%')
                    ->orWhere('registration_number', 'like', '%' . $this->search . '%');
            });
        }

        return view('livewire.admin.companies', [
            'companies' => $query->withCount('enrollments', 'departments')->latest()->paginate(10),
        ])->layout('layouts.admin');
    }
}
