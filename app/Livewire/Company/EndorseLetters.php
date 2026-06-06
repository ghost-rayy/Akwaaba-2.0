<?php

namespace App\Livewire\Company;

use App\Models\EndorsedLetter;
use App\Models\Enrollment;
use App\Models\LetterTemplate;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class EndorseLetters extends Component
{
    use WithFileUploads;

    public $selectedTemplateId = '';
    public $selectedPersonnel = [];
    public $signature;
    public $stamp;

    public $selectedPersonnelId = null;
    public $rejectionReason = '';
    public $confirmingRejection = false;
    public $confirmingEndorsement = false;
    public $viewingLetterId = null;
    public $viewingLetterBase64 = '';
    public $viewingValidatedLetterId = null;
    public $viewingValidatedLetterBase64 = '';
    public $startDate;
    public $endDate;
    public $postingDate;

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
                session()->flash('error', 'Failed to generate PDF overlay for ' . $enrollment->user->name . ': ' . $e->getMessage());
                return;
            }
        }

        $this->reset(['selectedPersonnel', 'signature', 'stamp', 'selectedTemplateId']);
        session()->flash('message', 'Letters endorsed successfully for ' . count($this->selectedPersonnel) . ' personnel.');
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

        session()->flash('message', 'Personnel letter validated successfully.');
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

        session()->flash('message', 'Personnel rejected.');
    }

    public function confirmEndorse($enrollmentId)
    {
        $this->selectedPersonnelId = $enrollmentId;
        $enrollment = Enrollment::findOrFail($enrollmentId);
        $this->startDate = $enrollment->start_date?->format('Y-m-d') ?? now()->format('Y-m-d');
        $this->endDate = $enrollment->end_date?->format('Y-m-d') ?? now()->addYear()->format('Y-m-d');
        $this->postingDate = now()->format('Y-m-d');
        $this->selectedTemplateId = '';
        $this->confirmingEndorsement = true;
    }

    public function endorseSingle()
    {
        $this->validate([
            'selectedTemplateId' => 'required|exists:letter_templates,id',
            'signature' => 'nullable|file|mimes:png,jpg,jpeg|max:2048',
            'stamp' => 'nullable|file|mimes:png,jpg,jpeg|max:2048',
            'startDate' => 'required|date',
            'endDate' => 'required|date|after_or_equal:startDate',
            'postingDate' => 'required|date',
        ]);

        $user = auth()->user();
        $company = $user->company;
        $template = LetterTemplate::with('fieldMappings')->findOrFail($this->selectedTemplateId);

        $signaturePath = $this->signature
            ? $this->signature->store('signatures/' . $company->id, 'public')
            : $company->digital_signature_path;
        $stampPath = $this->stamp
            ? $this->stamp->store('stamps/' . $company->id, 'public')
            : $company->stamp_path;

        $enrollment = Enrollment::with('user.personalInfo', 'user.educationInfo')
            ->where('company_id', $company->id)
            ->where('status', 'shortlisted')
            ->findOrFail($this->selectedPersonnelId);

        // Update database with user chosen dates first
        $enrollment->update([
            'status' => 'endorsed',
            'endorsement_date' => $this->postingDate ? \Carbon\Carbon::parse($this->postingDate) : now(),
            'start_date' => $this->startDate ? \Carbon\Carbon::parse($this->startDate) : null,
            'end_date' => $this->endDate ? \Carbon\Carbon::parse($this->endDate) : null,
        ]);

        try {
            $filePath = $this->generateEndorsedPdf($enrollment, $company, $template, $signaturePath, $stampPath);

            EndorsedLetter::create([
                'enrollment_id' => $enrollment->id,
                'letter_template_id' => $template->id,
                'endorsed_by' => $user->id,
                'generated_file_path' => $filePath,
                'status' => 'endorsed',
            ]);
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to generate PDF overlay: ' . $e->getMessage());
            return;
        }

        $this->confirmingEndorsement = false;
        $this->selectedPersonnelId = null;
        $this->reset(['signature', 'stamp', 'selectedTemplateId']);

        session()->flash('message', 'Letter endorsed successfully.');
    }

    protected function generateEndorsedPdf($enrollment, $company, $template, $signaturePath, $stampPath)
    {
        $data = $this->buildFieldData($enrollment, $company, $template);

        // Get source PDF: personnel's uploaded letter or the company's template letter fallback
        $postingLetter = $enrollment->user->documents()->where('type', 'posting_letter')->first();
        $sourcePdfPath = null;
        if ($postingLetter) {
            $sourcePdfPath = storage_path('app/public/' . $postingLetter->file_path);
        }
        if (!$sourcePdfPath || !file_exists($sourcePdfPath)) {
            $sourcePdfPath = storage_path('app/public/' . $template->template_file_path);
        }

        if (!file_exists($sourcePdfPath)) {
            throw new \Exception("Template PDF or uploaded document file not found.");
        }

        $pdf = new \setasign\Fpdi\Fpdi('P', 'pt');
        $pageCount = $pdf->setSourceFile($sourcePdfPath);

        for ($pageNum = 1; $pageNum <= $pageCount; $pageNum++) {
            $templateId = $pdf->importPage($pageNum);
            $size = $pdf->getTemplateSize($templateId);

            $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
            $pdf->useTemplate($templateId);

            $pageFields = $template->fieldMappings->where('page_number', $pageNum);
            foreach ($pageFields as $mapping) {
                // Scale coordinates from canvas viewport scale 1.5 to PDF points
                $x = $mapping->x / 1.5;
                $y = $mapping->y / 1.5;
                $w = $mapping->width / 1.5;
                $h = $mapping->height / 1.5;

                $fieldKey = $mapping->field_key;

                if ($fieldKey === 'signature' && $signaturePath) {
                    $sigFullPath = Storage::disk('public')->path($signaturePath);
                    if (file_exists($sigFullPath)) {
                        $pdf->Image($sigFullPath, $x, $y, $w, $h);
                    }
                } elseif ($fieldKey === 'stamp' && $stampPath) {
                    $stampFullPath = Storage::disk('public')->path($stampPath);
                    if (file_exists($stampFullPath)) {
                        $pdf->Image($stampFullPath, $x, $y, $w, $h);
                    }
                } else {
                    $text = $data[$fieldKey] ?? '';
                    $pdf->SetFont('Arial', '', $mapping->font_size ?? 12);
                    $pdf->SetXY($x, $y);

                    $align = 'L';
                    if ($mapping->text_alignment === 'center') $align = 'C';
                    if ($mapping->text_alignment === 'right') $align = 'R';

                    $pdf->Cell($w, $h, $text, 0, 0, $align);
                }
            }
        }

        $fileName = 'endorsed_letter_' . $enrollment->nss_number . '_' . now()->format('YmdHis') . '.pdf';
        $filePath = 'endorsed_letters/' . $company->id . '/' . $fileName;

        // Ensure directory exists
        Storage::disk('public')->makeDirectory('endorsed_letters/' . $company->id);

        $destPath = Storage::disk('public')->path($filePath);
        $pdf->Output('F', $destPath);

        return $filePath;
    }

    public function render()
    {
        $company = auth()->user()->company;

        return view('livewire.company.endorse-letters', [
            'templates' => LetterTemplate::where('company_id', $company->id)
                ->where('is_active', true)
                ->with('fieldMappings')
                ->get(),
            'shortlistedPersonnel' => Enrollment::where('company_id', $company->id)
                ->where('status', 'shortlisted')
                ->with(['user', 'user.personalInfo', 'department'])
                ->latest()
                ->get(),
            'endorsedLetters' => EndorsedLetter::whereHas('enrollment', function ($q) use ($company) {
                $q->where('company_id', $company->id);
            })->with(['enrollment.user', 'enrollment.department'])->latest()->get(),
        ])->layout('layouts.company');
    }
}
