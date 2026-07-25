<?php

namespace App\Livewire\Company;

use App\Models\AppointmentLetter;
use App\Models\Enrollment;
use App\Models\LetterTemplate;
use App\Models\TemplateFieldMapping;
use App\Support\AppointmentLetterIssuer;
use App\Support\DispatchesToast;
use Livewire\Component;
use Livewire\WithFileUploads;

class Letters extends Component
{
    use DispatchesToast, WithFileUploads;

    public $letterType = 'posting_letter';
    public $mode = 'list';
    public $selectedTemplateId = null;
    public $editingField = null;
    public $appointmentUpload;
    public $selectedEnrollmentIds = [];

    protected $queryString = [
        'letterType' => ['except' => 'posting_letter'],
        'mode' => ['except' => 'list'],
        'selectedTemplateId' => ['except' => ''],
    ];

    public $postingFields = [
        'company_name' => 'Company Name',
        'company_location' => 'Company Location',
        'company_email' => 'Company Email',
        'company_phone' => 'Company Phone',
        'company_postal_address' => 'Postal Address',
        'company_registration_number' => 'Registration Number',
        'company_contact_person' => 'Contact Person',
        'todays_date' => "Today's Date",
        'signature' => 'Signature',
        'stamp' => 'Stamp',
    ];

    public $appointmentFields = [
        'full_name' => 'Full Name',
        'nss_number' => 'NSS Number',
        'department' => 'Department',
        'start_date' => 'Start Date',
        'end_date' => 'End Date',
        'company_name' => 'Company Name',
        'company_location' => 'Company Location',
        'company_email' => 'Company Email',
        'company_phone' => 'Company Phone',
        'company_postal_address' => 'Postal Address',
        'company_registration_number' => 'Registration Number',
        'company_contact_person' => 'Contact Person',
        'todays_date' => "Today's Date",
        'signature' => 'Signature',
        'stamp' => 'Stamp',
    ];

    public function getAvailableFieldsProperty(): array
    {
        return $this->letterType === 'appointment_letter'
            ? $this->appointmentFields
            : $this->postingFields;
    }

    public function switchLetterType(string $type): void
    {
        if (! in_array($type, ['posting_letter', 'appointment_letter'], true)) {
            return;
        }

        $this->letterType = $type;
        $this->mode = 'list';
        $this->selectedTemplateId = null;
        $this->selectedEnrollmentIds = [];
        $this->reset('appointmentUpload');
    }

    public function startMapping($templateId)
    {
        $template = LetterTemplate::where('company_id', auth()->user()->company_id)
            ->where('type', $this->letterType)
            ->findOrFail($templateId);

        $this->selectedTemplateId = $template->id;
        $this->editingField = null;
        $this->mode = 'mapping';
    }

    public function startGenerate(): void
    {
        if ($this->letterType !== 'appointment_letter') {
            return;
        }

        $this->selectedEnrollmentIds = [];
        $this->mode = 'generate';
    }

    public function uploadAppointmentTemplate()
    {
        $this->validate([
            'appointmentUpload' => 'required|file|mimes:pdf|max:10240',
        ]);

        $company = auth()->user()->company;
        $path = $this->appointmentUpload->store('appointmentletters/'.$company->id, 'public');

        LetterTemplate::updateOrCreate(
            ['company_id' => $company->id, 'type' => 'appointment_letter'],
            [
                'name' => $company->name.' Appointment Letter',
                'template_file_path' => $path,
                'is_active' => true,
            ]
        );

        $this->reset('appointmentUpload');
        $this->toastSuccess('Appointment letter template uploaded.');
    }

    public function saveFieldMappings($fields)
    {
        $template = LetterTemplate::where('company_id', auth()->user()->company_id)
            ->where('type', $this->letterType)
            ->findOrFail($this->selectedTemplateId);

        $fieldLabels = $this->availableFields;
        $existingIds = [];
        foreach ($fields as $field) {
            $data = [
                'letter_template_id' => $template->id,
                'field_key' => $field['field_key'],
                'field_type' => 'text',
                'label' => $fieldLabels[$field['field_key']] ?? $field['field_key'],
                'page_number' => $field['page_number'] ?? 1,
                'x' => round($field['x'], 2),
                'y' => round($field['y'], 2),
                'width' => round($field['w'], 2),
                'height' => round($field['h'], 2),
                'font_size' => $field['font_size'] ?? 12,
                'text_alignment' => $field['text_alignment'] ?? 'left',
                'is_required' => true,
            ];

            $mapping = null;
            if (! empty($field['id']) && is_numeric($field['id'])) {
                $mapping = TemplateFieldMapping::where('id', $field['id'])
                    ->where('letter_template_id', $template->id)
                    ->first();
            }

            if ($mapping) {
                $mapping->update($data);
                $existingIds[] = $mapping->id;
            } else {
                $newMapping = TemplateFieldMapping::create($data);
                $existingIds[] = $newMapping->id;
            }
        }

        TemplateFieldMapping::where('letter_template_id', $template->id)
            ->whereNotIn('id', $existingIds)
            ->delete();

        $template->update(['is_active' => true]);

        $this->toastSuccess('Field mappings saved successfully.');
        $this->mode = 'list';
    }

    public function deleteTemplate($id)
    {
        $template = LetterTemplate::where('company_id', auth()->user()->company_id)
            ->where('type', $this->letterType)
            ->findOrFail($id);

        $template->fieldMappings()->delete();
        $template->delete();

        $message = $this->letterType === 'appointment_letter'
            ? 'Appointment template deleted. Upload a new one here.'
            : 'Template deleted. Upload a new one in Settings.';

        $this->toastSuccess($message);
    }

    public function toggleEnrollment($enrollmentId): void
    {
        $enrollmentId = (int) $enrollmentId;
        if (in_array($enrollmentId, $this->selectedEnrollmentIds, true)) {
            $this->selectedEnrollmentIds = array_values(array_filter(
                $this->selectedEnrollmentIds,
                fn ($id) => (int) $id !== $enrollmentId
            ));
        } else {
            $this->selectedEnrollmentIds[] = $enrollmentId;
        }
    }

    public function selectAllEligible(): void
    {
        $company = auth()->user()->company;
        $this->selectedEnrollmentIds = Enrollment::where('company_id', $company->id)
            ->whereIn('status', ['validated', 'active'])
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->all();
    }

    public function clearSelection(): void
    {
        $this->selectedEnrollmentIds = [];
    }

    public function issueSelected()
    {
        if ($this->letterType !== 'appointment_letter') {
            return;
        }

        if (empty($this->selectedEnrollmentIds)) {
            $this->toastError('Select at least one validated personnel.');

            return;
        }

        $company = auth()->user()->company;
        $issuer = auth()->user();
        $issued = 0;
        $errors = [];

        $enrollments = Enrollment::where('company_id', $company->id)
            ->whereIn('id', $this->selectedEnrollmentIds)
            ->whereIn('status', ['validated', 'active'])
            ->with(['user.personalInfo', 'user.educationInfo', 'department'])
            ->get();

        foreach ($enrollments as $enrollment) {
            try {
                AppointmentLetterIssuer::issue($enrollment, $issuer, $company);
                $issued++;
            } catch (\Throwable $e) {
                $name = $enrollment->user?->name ?? $enrollment->nss_number;
                $errors[] = $name.': '.$e->getMessage();
            }
        }

        if ($issued > 0) {
            $this->toastSuccess($issued.' appointment letter(s) issued successfully.');
        }

        if (! empty($errors)) {
            $this->toastError(implode(' ', array_slice($errors, 0, 3)));
        }

        $this->selectedEnrollmentIds = [];
    }

    public function issueOne($enrollmentId)
    {
        if ($this->letterType !== 'appointment_letter') {
            return;
        }

        $company = auth()->user()->company;
        $enrollment = Enrollment::where('company_id', $company->id)
            ->whereIn('status', ['validated', 'active'])
            ->with(['user.personalInfo', 'user.educationInfo', 'department'])
            ->findOrFail($enrollmentId);

        try {
            AppointmentLetterIssuer::issue($enrollment, auth()->user(), $company);
            $this->toastSuccess('Appointment letter issued to '.$enrollment->user->name.'.');
        } catch (\Throwable $e) {
            $this->toastError($e->getMessage());
        }
    }

    public function render()
    {
        $company = auth()->user()->company;
        $currentTemplate = $this->selectedTemplateId
            ? LetterTemplate::where('company_id', $company->id)
                ->where('type', $this->letterType)
                ->with('fieldMappings')
                ->find($this->selectedTemplateId)
            : null;

        $templateBase64 = '';
        if ($currentTemplate?->template_file_path) {
            $path = storage_path('app/public/'.$currentTemplate->template_file_path);
            if (file_exists($path)) {
                $templateBase64 = base64_encode(file_get_contents($path));
            }
        }

        $template = LetterTemplate::withCount('fieldMappings')
            ->where('company_id', $company->id)
            ->where('type', $this->letterType)
            ->first();

        $eligiblePersonnel = collect();
        $issuedLetters = collect();

        if ($this->letterType === 'appointment_letter' && in_array($this->mode, ['list', 'generate'], true)) {
            $issuedByEnrollment = AppointmentLetter::whereHas('enrollment', function ($q) use ($company) {
                $q->where('company_id', $company->id);
            })->get()->keyBy('enrollment_id');

            $eligiblePersonnel = Enrollment::where('company_id', $company->id)
                ->whereIn('status', ['validated', 'active'])
                ->with(['user', 'department'])
                ->latest('validated_at')
                ->get()
                ->map(function ($enrollment) use ($issuedByEnrollment) {
                    $enrollment->has_appointment_letter = $issuedByEnrollment->has($enrollment->id);

                    return $enrollment;
                });

            $issuedLetters = AppointmentLetter::whereHas('enrollment', function ($q) use ($company) {
                $q->where('company_id', $company->id);
            })->with(['enrollment.user', 'enrollment.department', 'issuedBy'])
                ->latest()
                ->get();
        }

        return view('livewire.company.letters', [
            'template' => $template,
            'currentTemplate' => $currentTemplate,
            'templateBase64' => $templateBase64,
            'availableFields' => $this->availableFields,
            'eligiblePersonnel' => $eligiblePersonnel,
            'issuedLetters' => $issuedLetters,
        ])->layout('layouts.company');
    }
}
