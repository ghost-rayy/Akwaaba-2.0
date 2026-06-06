<?php

namespace App\Livewire\Company;

use App\Models\LetterTemplate;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Hash;

class Settings extends Component
{
    use WithFileUploads;

    public $name;
    public $email;
    public $phone;
    public $location;
    public $postal_address;
    public $registration_number;
    public $contact_person;
    public $posting_date;
    public $signature;
    public $stamp;
    public $posting_letter;
    public $current_password;
    public $new_password;
    public $new_password_confirmation;

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

        session()->flash('profile_message', 'Company profile updated.');
    }

    public function uploadSignature()
    {
        $this->validate([
            'signature' => 'required|image|mimes:png,jpg,jpeg|max:2048',
        ]);

        $path = $this->signature->store('signatures/' . auth()->user()->company_id, 'public');
        auth()->user()->company->update(['digital_signature_path' => $path]);

        $this->signature = null;
        session()->flash('signature_message', 'Signature uploaded.');
    }

    public function uploadStamp()
    {
        $this->validate([
            'stamp' => 'required|image|mimes:png,jpg,jpeg|max:2048',
        ]);

        $path = $this->stamp->store('stamps/' . auth()->user()->company_id, 'public');
        auth()->user()->company->update(['stamp_path' => $path]);

        $this->stamp = null;
        session()->flash('stamp_message', 'Stamp uploaded.');
    }

    public function uploadPostingLetter()
    {
        $this->validate([
            'posting_letter' => 'required|file|mimes:pdf|max:10240',
        ]);

        $company = auth()->user()->company;
        $path = $this->posting_letter->store('postingletters/' . $company->id, 'public');
        $company->update(['posting_letter_path' => $path]);

        LetterTemplate::updateOrCreate(
            ['company_id' => $company->id, 'type' => 'posting_letter'],
            [
                'name' => $company->name . ' Posting Letter',
                'template_file_path' => $path,
                'is_active' => true,
            ]
        );

        $this->posting_letter = null;
        session()->flash('posting_letter_message', 'Posting letter template uploaded.');
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
        session()->flash('password_message', 'Password changed.');
    }

    public function render()
    {
        return view('livewire.company.settings')->layout('layouts.company');
    }
}
