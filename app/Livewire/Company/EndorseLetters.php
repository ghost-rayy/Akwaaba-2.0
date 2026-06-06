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

        $signaturePath = $this->signature?->store('signatures/' . $company->id, 'public');
        $stampPath = $this->stamp?->store('stamps/' . $company->id, 'public');

        foreach ($this->selectedPersonnel as $enrollmentId) {
            $enrollment = Enrollment::with('user.personalInfo', 'user.educationInfo')
                ->where('company_id', $company->id)
                ->where('status', 'shortlisted')
                ->findOrFail($enrollmentId);

            $data = $this->buildFieldData($enrollment, $company, $template);

            $sigBase64 = $signaturePath ? $this->imageToBase64(Storage::disk('public')->path($signaturePath)) : null;
            $stampBase64 = $stampPath ? $this->imageToBase64(Storage::disk('public')->path($stampPath)) : null;

            $pdf = Pdf::loadView('pdf.endorsed-letter', [
                'data' => $data,
                'template' => $template,
                'signatureBase64' => $sigBase64,
                'stampBase64' => $stampBase64,
            ]);

            $fileName = 'endorsed_letter_' . $enrollment->nss_number . '_' . now()->format('YmdHis') . '.pdf';
            $filePath = 'endorsed_letters/' . $company->id . '/' . $fileName;
            Storage::disk('public')->put($filePath, $pdf->output());

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
        }

        $this->reset(['selectedPersonnel', 'signature', 'stamp', 'selectedTemplateId']);
        session()->flash('message', 'Letters endorsed successfully for ' . count($this->selectedPersonnel) . ' personnel.');
    }

    protected function buildFieldData($enrollment, $company, $template)
    {
        $user = $enrollment->user;
        $personalInfo = $user->personalInfo;
        $educationInfo = $user->educationInfo;

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
            'company_location' => $company->city ?? $company->address ?? '',
            'start_date' => $enrollment->start_date?->format('d/m/Y') ?? now()->format('d/m/Y'),
            'end_date' => $enrollment->end_date?->format('d/m/Y') ?? now()->addYear()->format('d/m/Y'),
            'posting_date' => now()->format('d/m/Y'),
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
