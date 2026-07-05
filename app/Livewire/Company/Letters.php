<?php

namespace App\Livewire\Company;

use App\Models\LetterTemplate;
use App\Models\TemplateFieldMapping;
use App\Support\DispatchesToast;
use Livewire\Component;
use Livewire\WithFileUploads;

class Letters extends Component
{
    use DispatchesToast, WithFileUploads;

    public $mode = 'list';
    public $selectedTemplateId = null;
    public $editingField = null;

    protected $queryString = [
        'mode' => ['except' => 'list'],
        'selectedTemplateId' => ['except' => ''],
    ];

    public $availableFields = [
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

    public function startMapping($templateId)
    {
        $this->selectedTemplateId = $templateId;
        $this->editingField = null;
        $this->mode = 'mapping';
    }

    public function saveFieldMappings($fields)
    {
        $template = LetterTemplate::findOrFail($this->selectedTemplateId);

        $existingIds = [];
        foreach ($fields as $field) {
            $data = [
                'letter_template_id' => $template->id,
                'field_key' => $field['field_key'],
                'field_type' => 'text',
                'label' => $this->availableFields[$field['field_key']] ?? $field['field_key'],
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
            if (!empty($field['id']) && is_numeric($field['id'])) {
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
            ->findOrFail($id);

        $template->fieldMappings()->delete();
        $template->delete();

        $this->toastSuccess('Template deleted. Upload a new one in Settings.');
    }

    public function render()
    {
        $company = auth()->user()->company;
        $currentTemplate = $this->selectedTemplateId
            ? LetterTemplate::with('fieldMappings')->find($this->selectedTemplateId)
            : null;

        $templateBase64 = '';
        if ($currentTemplate) {
            $path = storage_path('app/public/' . $currentTemplate->template_file_path);
            if (file_exists($path)) {
                $templateBase64 = base64_encode(file_get_contents($path));
            }
        }

        return view('livewire.company.letters', [
            'template' => LetterTemplate::withCount('fieldMappings')
                ->where('company_id', $company->id)
                ->where('type', 'posting_letter')
                ->first(),
            'currentTemplate' => $currentTemplate,
            'templateBase64' => $templateBase64,
        ])->layout('layouts.company');
    }
}
