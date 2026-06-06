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
    public $successMessage = '';

    protected function rules()
    {
        return [
            'nss_number' => 'required|string|unique:users,nss_number|unique:enrollments,nss_number',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|string',
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
