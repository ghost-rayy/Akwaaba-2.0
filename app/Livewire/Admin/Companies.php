<?php

namespace App\Livewire\Admin;

use App\Mail\CompanyAdminOnboardedMail;
use App\Models\Company;
use App\Models\User;
use App\Support\DispatchesToast;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithPagination;

class Companies extends Component
{
    use DispatchesToast, WithPagination;

    public $search = '';
    public $showModal = false;
    public $editingId = null;
    public $name = '';
    public $admin_email = '';
    public $is_active = true;

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function create()
    {
        $this->reset(['editingId', 'name', 'admin_email']);
        $this->is_active = true;
        $this->showModal = true;
    }

    public function edit($id)
    {
        $company = Company::findOrFail($id);
        $this->editingId = $company->id;
        $this->name = $company->name;
        $this->admin_email = '';
        $this->is_active = $company->is_active;
        $this->showModal = true;
    }

    public function save()
    {
        if ($this->editingId) {
            $this->validate([
                'name' => 'required|string|max:255',
                'is_active' => 'boolean',
            ]);

            Company::where('id', $this->editingId)->update([
                'name' => $this->name,
                'is_active' => $this->is_active,
            ]);

            $this->toastSuccess('Company updated.');
            $this->reset(['editingId', 'name', 'admin_email', 'showModal']);
            return;
        }

        $this->validate([
            'name' => 'required|string|max:255',
            'admin_email' => 'required|email|unique:users,email',
        ]);

        $company = Company::create([
            'name' => $this->name,
            'email' => $this->admin_email,
            'is_active' => true,
        ]);

        $password = Str::random(10);

        $admin = User::create([
            'name' => $this->name . ' Admin',
            'email' => $this->admin_email,
            'password' => Hash::make($password),
            'role' => 'company_admin',
            'company_id' => $company->id,
            'must_change_password' => true,
        ]);

        Mail::to($admin->email)->send(new CompanyAdminOnboardedMail($admin, $password));

        $this->toastSuccess("Company created. Login details sent to {$this->admin_email}.");
        $this->reset(['editingId', 'name', 'admin_email', 'showModal']);
    }

    public function render()
    {
        $query = Company::query();

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('email', 'like', '%' . $this->search . '%');
            });
        }

        return view('livewire.admin.companies', [
            'companies' => $query->withCount('enrollments', 'departments')->latest()->paginate(10),
        ])->layout('layouts.admin');
    }
}
