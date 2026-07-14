<?php

namespace App\Livewire\Company;

use App\Models\EndorsedLetter;
use App\Models\Enrollment;
use App\Models\LetterTemplate;
use App\Support\DispatchesToast;
use App\Support\LetterEndorsement;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class EndorseLetters extends Component
{
    use DispatchesToast, WithFileUploads, WithPagination;

    public $selectedTemplateId = '';
    public $selectedPersonnel = [];
    public $signature;
    public $stamp;

    public $selectedPersonnelId = null;
    public $rejectionReason = '';
    public $confirmingRejection = false;
    public $viewingLetterId = null;
    public $viewingLetterBase64 = '';
    public $viewingValidatedLetterId = null;
    public $viewingValidatedLetterBase64 = '';

    protected function rules()
    {
        return [
            'selectedTemplateId' => 'required|exists:letter_templates,id',
            'selectedPersonnel' => 'required|array|min:1',
            'selectedPersonnel.*' => 'exists:enrollments,id',
            'signature' => 'nullable|file|mimes:png,jpg,jpeg|max:2048',
            'stamp' => 'nullable|file|mimes:png,jpg,jpeg|max:2048',
        ];
    }

    public function endorse()
    {
        $this->validate();

        $user = auth()->user();
        $company = $user->company;
        $template = LetterTemplate::with('fieldMappings')->findOrFail($this->selectedTemplateId);

        $signaturePath = $this->signature
            ? $this->signature->store('signatures/' . $company->id, 'public')
            : $company->digital_signature_path;
        $stampPath = $this->stamp
            ? $this->stamp->store('stamps/' . $company->id, 'public')
            : $company->stamp_path;

        foreach ($this->selectedPersonnel as $enrollmentId) {
            $enrollment = Enrollment::with('user.personalInfo', 'user.educationInfo')
                ->where('company_id', $company->id)
                ->where('status', 'shortlisted')
                ->findOrFail($enrollmentId);

            try {
                $filePath = $this->generateEndorsedPdf($enrollment, $company, $template, $signaturePath, $stampPath);

                EndorsedLetter::create([
                    'enrollment_id' => $enrollment->id,
                    'letter_template_id' => $template->id,
                    'endorsed_by' => $user->id,
                    'generated_file_path' => $filePath,
                    'status' => 'endorsed',
                ]);

                $enrollment->update([
                    'status' => 'endorsed',
                    'endorsement_date' => now(),
                ]);
            } catch (\Exception $e) {
                $this->toastError('Failed to generate PDF overlay for '.$enrollment->user->name.': '.$e->getMessage());
                return;
            }
        }

        $this->reset(['selectedPersonnel', 'signature', 'stamp', 'selectedTemplateId']);
        $this->toastSuccess('Letters endorsed successfully for '.count($this->selectedPersonnel).' personnel.');
    }

    protected function buildFieldData($enrollment, $company, $template)
    {
        $user = $enrollment->user;
        $personalInfo = $user->personalInfo;
        $educationInfo = $user->educationInfo;

        $startDateObj = $enrollment->start_date ? \Carbon\Carbon::parse($enrollment->start_date) : null;
        $endDateObj = $enrollment->end_date ? \Carbon\Carbon::parse($enrollment->end_date) : null;
        $postingDateObj = $enrollment->endorsement_date ? \Carbon\Carbon::parse($enrollment->endorsement_date) : null;

        $data = [
            'full_name' => $personalInfo?->full_name ?? $user->name,
            'nss_number' => $enrollment->nss_number,
            'date_of_birth' => $personalInfo?->date_of_birth ?? '',
            'place_of_residence' => $personalInfo?->place_of_residence ?? '',
            'region_of_residence' => $personalInfo?->region_of_residence ?? '',
            'university' => $educationInfo?->university ?? '',
            'programme_of_study' => $educationInfo?->programme_of_study ?? '',
            'form_of_education' => $educationInfo?->form_of_education ?? '',
            'company_name' => $company->name,
            'company_location' => $company->location ?? '',
            'company_email' => $company->email ?? '',
            'company_phone' => $company->phone ?? '',
            'company_postal_address' => $company->postal_address ?? '',
            'company_registration_number' => $company->registration_number ?? '',
            'company_contact_person' => $company->contact_person ?? '',
            'todays_date' => $company->posting_date ? \Carbon\Carbon::parse($company->posting_date)->format('d/m/Y') : now()->format('d/m/Y'),
            'start_date' => $startDateObj ? $startDateObj->format('d/m/Y') : now()->format('d/m/Y'),
            'end_date' => $endDateObj ? $endDateObj->format('d/m/Y') : now()->addYear()->format('d/m/Y'),
            'posting_date' => $postingDateObj ? $postingDateObj->format('d/m/Y') : now()->format('d/m/Y'),
        ];

        return $data;
    }

    protected function imageToBase64($path)
    {
        if (!$path || !file_exists($path)) return null;
        $type = pathinfo($path, PATHINFO_EXTENSION);
        $data = file_get_contents($path);
        return 'data:image/' . $type . ';base64,' . base64_encode($data);
    }

    public function viewLetter($enrollmentId)
    {
        $enrollment = Enrollment::with('user')->findOrFail($enrollmentId);
        $letter = $enrollment->user->documents()->where('type', 'posting_letter')->first();
        if ($letter) {
            $path = storage_path('app/public/' . $letter->file_path);
            if (file_exists($path)) {
                $this->viewingLetterBase64 = base64_encode(file_get_contents($path));
                $this->viewingLetterId = $enrollmentId;
            }
        }
    }

    public function closeViewer()
    {
        $this->viewingLetterId = null;
        $this->viewingLetterBase64 = '';
    }

    public function previewValidatedLetter($letterId)
    {
        $letter = EndorsedLetter::whereHas('enrollment', function ($q) {
            $q->where('company_id', auth()->user()->company_id);
        })->findOrFail($letterId);

        if ($letter->validated_file_path) {
            $path = storage_path('app/public/' . $letter->validated_file_path);
            if (file_exists($path)) {
                $this->viewingValidatedLetterBase64 = base64_encode(file_get_contents($path));
                $this->viewingValidatedLetterId = $letterId;
            }
        }
    }

    public function closeValidatedViewer()
    {
        $this->viewingValidatedLetterId = null;
        $this->viewingValidatedLetterBase64 = '';
    }

    public function validateLetter($letterId)
    {
        $letter = EndorsedLetter::whereHas('enrollment', function ($q) {
            $q->where('company_id', auth()->user()->company_id);
        })->findOrFail($letterId);

        $letter->update([
            'validated_by' => auth()->id(),
            'validated_at' => now(),
            'status' => 'validated',
        ]);

        $letter->enrollment->update([
            'status' => 'validated',
        ]);

        $this->closeValidatedViewer();

        $this->toastSuccess('Personnel letter validated successfully.');
    }

    public function confirmReject($enrollmentId)
    {
        $this->selectedPersonnelId = $enrollmentId;
        $this->rejectionReason = '';
        $this->confirmingRejection = true;
    }

    public function reject()
    {
        $this->validate(['rejectionReason' => 'required|string|max:1000']);

        $enrollment = Enrollment::where('company_id', auth()->user()->company_id)
            ->where('id', $this->selectedPersonnelId)
            ->where('status', 'shortlisted')
            ->firstOrFail();

        $enrollment->update([
            'status' => 'rejected',
            'rejection_reason' => $this->rejectionReason,
        ]);

        $this->confirmingRejection = false;
        $this->selectedPersonnelId = null;
        $this->rejectionReason = '';

        $this->toastSuccess('Personnel rejected.');
    }

    public function endorsePersonnel($enrollmentId)
    {
        $user = auth()->user();
        $company = $user->company;

        try {
            $enrollment = Enrollment::with('user.personalInfo', 'user.educationInfo')
                ->where('company_id', $company->id)
                ->where('status', 'shortlisted')
                ->findOrFail($enrollmentId);

            LetterEndorsement::endorse($enrollment, $user, $company);
        } catch (\Throwable $e) {
            $this->toastError($e->getMessage());

            return;
        }

        $this->toastSuccess($enrollment->user->name."'s letter endorsed successfully.");
    }

    public function bulkEndorsePersonnel($ids = [])
    {
        $ids = collect($ids)->filter()->values()->toArray();

        if (empty($ids)) {
            $this->toastError('No personnel selected.');
            return;
        }

        $user = auth()->user();
        $company = $user->company;
        $successCount = 0;

        foreach ($ids as $id) {
            try {
                $enrollment = Enrollment::with('user.personalInfo', 'user.educationInfo')
                    ->where('company_id', $company->id)
                    ->where('status', 'shortlisted')
                    ->findOrFail($id);
                LetterEndorsement::endorse($enrollment, $user, $company);
                $successCount++;
            } catch (\Throwable $e) {
                $this->toastError('Failed for enrollment #'.$id.': '.$e->getMessage());
            }
        }

        if ($successCount > 0) {
            $this->toastSuccess("{$successCount} personnel endorsed successfully.");
        }
    }

    public function reEndorse($letterId)
    {
        $user = auth()->user();
        $company = $user->company;

        try {
            $letter = EndorsedLetter::whereHas('enrollment', function ($q) use ($company) {
                $q->where('company_id', $company->id);
            })->with('enrollment.user')->findOrFail($letterId);

            LetterEndorsement::reEndorse($letter, $user, $company);
        } catch (\Throwable $e) {
            $this->toastError($e->getMessage());

            return;
        }

        $this->toastSuccess($letter->enrollment->user->name."'s letter re-endorsed. Personnel can download the updated PDF.");
    }

    protected function generateEndorsedPdf($enrollment, $company, $template, $signaturePath, $stampPath)
    {
        return LetterEndorsement::generatePdf($enrollment, $company, $template, $signaturePath, $stampPath);
    }

    public function render()
    {
        $company = auth()->user()->company;

        return view('livewire.company.endorse-letters', [
            'shortlistedPersonnel' => Enrollment::where('company_id', $company->id)
                ->where('status', 'shortlisted')
                ->with(['user.passportPhoto', 'user.personalInfo', 'department'])
                ->latest()
                ->paginate(10, pageName: 'shortlistedPage'),
            'endorsedLetters' => EndorsedLetter::whereHas('enrollment', function ($q) use ($company) {
                $q->where('company_id', $company->id);
            })->with(['enrollment.user.passportPhoto', 'enrollment.department'])->latest('updated_at')->paginate(10, pageName: 'endorsedPage'),
        ])->layout('layouts.company');
    }
}
