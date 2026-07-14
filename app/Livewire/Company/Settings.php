<?php

namespace App\Livewire\Company;

use App\Mail\HrStaffOnboardedMail;
use App\Models\User;
use App\Support\DispatchesToast;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Component;

class Settings extends Component
{
    use DispatchesToast;

    public $name;

    public $email;

    public $phone;

    public $location;

    public $postal_address;

    public $registration_number;

    public $contact_person;

    public $posting_date;

    public $current_password;

    public $new_password;

    public $new_password_confirmation;

    public $hr_name = '';

    public $hr_email = '';

    public $hr_phone = '';

    public function mount()
    {
        $company = auth()->user()->company;
        $this->name = $company->name;
        $this->email = $company->email;
        $this->phone = $company->phone;
        $this->location = $company->location;
        $this->postal_address = $company->postal_address;
        $this->registration_number = $company->registration_number;
        $this->contact_person = $company->contact_person;
        $this->posting_date = $company->posting_date ?? now()->format('Y-m-d');
    }

    public function updateProfile()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'location' => 'nullable|string|max:255',
            'postal_address' => 'nullable|string|max:255',
            'registration_number' => 'nullable|string|max:100',
            'contact_person' => 'nullable|string|max:255',
        ]);

        $this->posting_date = now()->format('Y-m-d');

        auth()->user()->company->update([
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'location' => $this->location,
            'postal_address' => $this->postal_address,
            'registration_number' => $this->registration_number,
            'contact_person' => $this->contact_person,
            'posting_date' => $this->posting_date,
        ]);

        $this->toastSuccess('Company profile updated.');
    }

    public function changePassword()
    {
        $this->validate([
            'current_password' => 'required|current_password',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        auth()->user()->update(['password' => Hash::make($this->new_password)]);

        $this->current_password = null;
        $this->new_password = null;
        $this->new_password_confirmation = null;
        $this->toastSuccess('Password changed.');
    }

    public function updatedHrPhone(?string $value): void
    {
        $this->hr_phone = substr(preg_replace('/\D/', '', (string) $value), 0, 10);
    }

    public function createHrStaff()
    {
        $companyId = auth()->user()->company_id;

        $this->validate([
            'hr_name' => 'required|string|max:255',
            'hr_email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email'),
            ],
            'hr_phone' => [
                'required',
                'digits:10',
                Rule::unique('users', 'phone'),
            ],
        ], [
            'hr_name.required' => 'Name is required.',
            'hr_email.required' => 'Email is required.',
            'hr_email.unique' => 'This email is already in use.',
            'hr_phone.required' => 'Phone number is required.',
            'hr_phone.digits' => 'Phone number must be exactly 10 digits.',
            'hr_phone.unique' => 'This phone number is already in use.',
        ]);

        $password = Str::random(10);

        $hr = User::create([
            'name' => $this->hr_name,
            'email' => $this->hr_email,
            'phone' => $this->hr_phone,
            'password' => Hash::make($password),
            'role' => 'hr_staff',
            'company_id' => $companyId,
            'must_change_password' => true,
        ]);

        Mail::to($hr->email)->send(new HrStaffOnboardedMail($hr, $password));

        $this->reset(['hr_name', 'hr_email', 'hr_phone']);

        $this->toastSuccess("HR staff created. Login credentials sent to {$hr->email}.");
    }

    public function resetHrPassword(int $userId)
    {
        $hr = $this->hrStaffQuery()->findOrFail($userId);
        $password = Str::random(10);

        $hr->update([
            'password' => Hash::make($password),
            'must_change_password' => true,
        ]);

        Mail::to($hr->email)->send(new HrStaffOnboardedMail($hr, $password, isPasswordReset: true));

        $this->toastSuccess("New temporary password sent to {$hr->email}.");
    }

    public function removeHrStaff(int $userId)
    {
        $hr = $this->hrStaffQuery()->findOrFail($userId);

        if ($hr->headedDepartments()->exists() || $hr->supervisedDepartments()->exists()) {
            $this->toastError('This HR staff is assigned to a department. Reassign department heads/supervisors first.');

            return;
        }

        $email = $hr->email;
        $hr->delete();

        $this->toastSuccess("HR staff {$email} removed.");
    }

    protected function hrStaffQuery()
    {
        return User::query()
            ->where('company_id', auth()->user()->company_id)
            ->where('role', 'hr_staff');
    }

    public function render()
    {
        return view('livewire.company.settings', [
            'hrStaff' => $this->hrStaffQuery()->orderBy('name')->get(),
        ])->layout('layouts.company');
    }
}
