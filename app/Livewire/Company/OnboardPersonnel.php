<?php

namespace App\Livewire\Company;

use App\Mail\OnboardedMail;
use App\Models\Enrollment;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Livewire\Component;

class OnboardPersonnel extends Component
{
    public $nss_number = '';
    public $email = '';
    public $phone = '';
    public $nss_year = '';
    public $successMessage = '';

    public $years = [];

    public function mount()
    {
        $currentYear = (int) date('Y');
        $this->years = range($currentYear, $currentYear - 10);
        $this->nss_year = (string) $currentYear;
    }

    protected function rules()
    {
        return [
            'nss_number' => 'required|string|unique:users,nss_number|unique:enrollments,nss_number',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|digits:10|unique:users,phone',
            'nss_year' => 'required|string|digits:4',
        ];
    }

    protected function messages()
    {
        return [
            'nss_number.required' => 'NSS number is required.',
            'nss_number.unique' => 'This NSS number is already taken.',
            'email.required' => 'Email address is required.',
            'email.email' => 'Enter a valid email address.',
            'email.unique' => 'This email is already in use.',
            'phone.required' => 'Phone number is required.',
            'phone.digits' => 'Phone number must be exactly 10 digits.',
            'phone.unique' => 'This phone number is already in use.',
        ];
    }

    public function onboard()
    {
        $this->validate();

        $user = auth()->user();
        $company = $user->company;

        $password = Str::random(10);

        $personnel = User::create([
            'name' => $this->nss_number,
            'email' => $this->email,
            'password' => Hash::make($password),
            'role' => 'nss_personnel',
            'company_id' => $company->id,
            'phone' => $this->phone,
            'nss_number' => $this->nss_number,
            'must_change_password' => true,
            'form_step' => 0,
        ]);

        Enrollment::create([
            'user_id' => $personnel->id,
            'company_id' => $company->id,
            'enrolled_by' => $user->id,
            'nss_number' => $this->nss_number,
            'nss_year' => $this->nss_year,
            'status' => 'pending_forms',
        ]);

        Mail::to($personnel->email)->send(new OnboardedMail($personnel, $password));

        $this->successMessage = "Personnel onboarded successfully! Email sent to {$this->email}.";

        $this->reset(['nss_number', 'email', 'phone']);
    }

    public function render()
    {
        $company = auth()->user()->company;

        return view('livewire.company.onboard-personnel', [
            'recentOnboardings' => $company
                ? Enrollment::where('company_id', $company->id)
                    ->with('user')
                    ->latest()
                    ->take(10)
                    ->get()
                : collect(),
        ])->layout('layouts.company');
    }
}
