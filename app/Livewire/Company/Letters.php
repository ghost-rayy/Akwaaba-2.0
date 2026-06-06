<?php

namespace App\Livewire\Company;

use App\Models\LetterTemplate;
use App\Models\TemplateFieldMapping;
use Livewire\Component;
use Livewire\WithFileUploads;

class Letters extends Component
{
    use WithFileUploads;

    public $mode = 'list';
    public $selectedTemplateId = null;
    public $name = '';
    public $template_file;
    public $editingField = null;

    public $availableFields = [
        'full_name' => 'Full Name',
        'nss_number' => 'NSS Number',
        'date_of_birth' => 'Date of Birth',
        'place_of_residence' => 'Place of Residence',
        'region_of_residence' => 'Region of Residence',
        'university' => 'University',
        'programme_of_study' => 'Programme of Study',
        'form_of_education' => 'Form of Education',
        'company_name' => 'Company Name',
        'company_location' => 'Company Location',
        'start_date' => 'Start Date',
        'end_date' => 'End Date',
        'posting_date' => 'Posting Date',
    ];

    protected function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'template_file' => 'required|file|mimes:pdf|max:10240',
        ];
    }

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function showCreate()
    {
        $this->reset(['name', 'template_file']);
        $this->mode = 'create';
    }

    public function saveTemplate()
    {
        $this->validate();

        $path = $this->template_file->store('templates', 'public');

        LetterTemplate::create([
            'company_id' => auth()->user()->company_id,
            'name' => $this->name,
            'type' => 'posting_letter',
            'template_file_path' => $path,
            'is_active' => false,
        ]);

        session()->flash('message', 'Template uploaded successfully. Now configure field mappings.');
        $this->mode = 'list';
    }

    public function startMapping($templateId)
    {
        $this->selectedTemplateId = $templateId;
        $this->editingField = null;
        $this->mode = 'mapping';

        $template = LetterTemplate::with('fieldMappings')->findOrFail($templateId);
        $this->dispatch('load-fields', fields: $template->fieldMappings->map(function ($fm) {
            return [
                'id' => $fm->id,
                'x' => (float) $fm->x,
                'y' => (float) $fm->y,
                'w' => (float) ($fm->width ?? 150),
                'h' => (float) ($fm->height ?? 30),
                'field_key' => $fm->field_key,
                'label' => $fm->label,
                'font_size' => $fm->font_size ?? 12,
                'text_alignment' => $fm->text_alignment ?? 'left',
            ];
        })->toArray());
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
                'page_number' => 1,
                'x' => round($field['x'], 2),
                'y' => round($field['y'], 2),
                'width' => round($field['w'], 2),
                'height' => round($field['h'], 2),
                'font_size' => $field['font_size'] ?? 12,
                'text_alignment' => $field['text_alignment'] ?? 'left',
                'is_required' => true,
            ];

            if (!empty($field['id']) && is_numeric($field['id'])) {
                $mapping = TemplateFieldMapping::where('id', $field['id'])
                    ->where('letter_template_id', $template->id)
                    ->first();
                if ($mapping) {
                    $mapping->update($data);
                    $existingIds[] = $mapping->id;
                }
            } else {
                $mapping = TemplateFieldMapping::create($data);
                $existingIds[] = $mapping->id;
            }
        }

        TemplateFieldMapping::where('letter_template_id', $template->id)
            ->whereNotIn('id', $existingIds)
            ->delete();

        $template->update(['is_active' => true]);

        session()->flash('message', 'Field mappings saved successfully.');
        $this->mode = 'list';
    }

    public function deleteTemplate($id)
    {
        $template = LetterTemplate::where('company_id', auth()->user()->company_id)
            ->findOrFail($id);

        $template->fieldMappings()->delete();
        $template->delete();

        session()->flash('message', 'Template deleted.');
    }

    public function render()
    {
        $company = auth()->user()->company;

        return view('livewire.company.letters', [
            'templates' => LetterTemplate::where('company_id', $company->id)
                ->withCount('fieldMappings')
                ->latest()
                ->get(),
            'currentTemplate' => $this->selectedTemplateId
                ? LetterTemplate::with('fieldMappings')->find($this->selectedTemplateId)
                : null,
        ])->layout('layouts.company');
    }
}
