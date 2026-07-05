<?php

namespace App\Livewire\Personnel;

use App\Models\EducationInfo;
use App\Models\PersonalInfo;
use App\Models\University;
use App\Support\DispatchesToast;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;

class Dashboard extends Component
{
    use DispatchesToast;
    // Password change
    public $current_password = '';
    public $new_password = '';
    public $new_password_confirmation = '';

    // Step 1: Personal Info
    public $full_name;
    public $nss_number;
    public $phone;
    public $email;
    public $place_of_residence;
    public $region_of_residence;
    public $nss_year;

    // Step 2: Education Info
    public $university = '';
    public $universityIsOther = false;
    public $city_of_school;
    public $region_of_school;
    public $form_of_education;
    public $programme_of_study;

    // Step 3: Documents
    public $posting_letter_path = null;
    public $posting_letter_name = null;
    public $passport_photo_path = null;
    public $passport_photo_name = null;

    public $regions = [
        'Ahafo', 'Ashanti', 'Bono', 'Bono East', 'Central', 'Eastern',
        'Greater Accra', 'Northern', 'North East', 'Oti', 'Savannah',
        'Upper East', 'Upper West', 'Volta', 'Western', 'Western North',
    ];

    public $educationForms = ['HND', 'Degree', 'Diploma', 'Masters', 'PhD', 'Other'];

    public function mount()
    {
        $user = auth()->user();
        $this->nss_number = $user->nss_number ?? '';
        $this->phone = $user->phone ?? '';
        $this->email = $user->email ?? '';
        $this->full_name = $user->name ?? '';
        $this->nss_year = $user->enrollment?->nss_year ?? '';

        if ($info = $user->personalInfo) {
            $this->full_name = $info->full_name;
            $this->nss_number = $info->nss_number;
            $this->phone = $info->phone;
            $this->email = $info->email;
            $this->place_of_residence = $info->place_of_residence;
            $this->region_of_residence = $info->region_of_residence;
        }

        if ($edu = $user->educationInfo) {
            $this->university = $edu->university;
            $this->universityIsOther = ! University::where('name', $edu->university)->exists();
            $this->city_of_school = $edu->city_of_school;
            $this->region_of_school = $edu->region_of_school;
            $this->form_of_education = $edu->form_of_education;
            $this->programme_of_study = $edu->programme_of_study;
        }
    }

    public function changePassword()
    {
        $this->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:8|confirmed',
        ]);

        if (!Hash::check($this->current_password, auth()->user()->password)) {
            $this->addError('current_password', 'Current password is incorrect.');
            return;
        }

        auth()->user()->update([
            'password' => Hash::make($this->new_password),
            'must_change_password' => false,
        ]);

        $this->toastSuccess('Password changed successfully! Please complete your profile.');
        $this->reset(['current_password', 'new_password', 'new_password_confirmation']);
    }

    public function saveStep1()
    {
        $this->validate([
            'full_name' => 'required|string|max:255',
            'phone' => 'required|string',
            'email' => 'required|email',
            'place_of_residence' => 'required|string',
            'region_of_residence' => 'required|string',
        ]);

        $user = auth()->user();

        PersonalInfo::updateOrCreate(
            ['user_id' => $user->id],
            [
                'full_name' => $this->full_name,
                'nss_number' => $this->nss_number,
                'phone' => $this->phone,
                'email' => $this->email,
                'place_of_residence' => $this->place_of_residence,
                'region_of_residence' => $this->region_of_residence,
            ]
        );

        $user->update(['name' => $this->full_name, 'form_step' => 1]);
        $this->toastSuccess('Personal information saved!');
    }

    public function saveStep2()
    {
        $this->validate([
            'university' => 'required|string',
            'city_of_school' => 'required|string',
            'region_of_school' => 'required|string',
            'form_of_education' => 'required|string',
            'programme_of_study' => 'required|string',
        ]);

        $user = auth()->user();

        EducationInfo::updateOrCreate(
            ['user_id' => $user->id],
            [
                'university' => $this->university,
                'city_of_school' => $this->city_of_school,
                'region_of_school' => $this->region_of_school,
                'form_of_education' => $this->form_of_education,
                'programme_of_study' => $this->programme_of_study,
            ]
        );

        $user->update(['form_step' => 2]);
        $this->toastSuccess('Education information saved!');
    }

    public function saveStep3()
    {
        $this->validate([
            'posting_letter_path' => 'required|string',
            'posting_letter_name' => 'required|string',
            'passport_photo_path' => 'nullable|string',
            'passport_photo_name' => 'nullable|string',
        ], [
            'posting_letter_path.required' => 'Please upload your posting letter and wait until the field turns green.',
        ]);

        $user = auth()->user();
        $company = $user->company;

        $user->documents()->create([
            'company_id' => $company->id,
            'type' => 'posting_letter',
            'file_path' => $this->posting_letter_path,
            'original_name' => $this->posting_letter_name,
        ]);

        if ($this->passport_photo_path) {
            $user->documents()->create([
                'company_id' => $company->id,
                'type' => 'passport',
                'file_path' => $this->passport_photo_path,
                'original_name' => $this->passport_photo_name,
            ]);
        }

        $user->update(['form_step' => 3]);

        $user->enrollment?->update(['status' => 'pending_review']);

        $this->toastSuccess('Documents uploaded successfully! You now have full access to the portal.');
    }

    public function render()
    {
        $user = auth()->user();
        $enrollment = $user->enrollment;
        $endorsedLetter = null;

        if ($enrollment) {
            $endorsedLetter = \App\Models\EndorsedLetter::where('enrollment_id', $enrollment->id)
                ->latest()
                ->first();
        }

        $evaluations = \App\Models\Evaluation::where('user_id', $user->id)
            ->latest()
            ->take(5)
            ->get();

        $recentAttendance = \App\Models\Attendance::where('user_id', $user->id)
            ->latest('date')
            ->take(7)
            ->get();

        return view('livewire.personnel.dashboard', [
            'step' => $user->must_change_password ? 0 : $user->form_step + 1,
            'enrollmentStatus' => $enrollment?->status,
            'rejectionReason' => $enrollment?->rejection_reason,
            'companyName' => $user->company?->name,
            'departmentName' => $enrollment?->department?->name,
            'documentCount' => $user->documents()->count(),
            'endorsedLetter' => $endorsedLetter,
            'evaluations' => $evaluations,
            'recentAttendance' => $recentAttendance,
            'universities' => University::orderBy('name')->pluck('name')->all(),
        ])->layout('layouts.personnel');
    }
}
