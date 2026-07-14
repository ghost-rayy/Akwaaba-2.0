<?php

namespace App\Support;

use App\Models\Company;
use App\Models\EndorsedLetter;
use App\Models\Enrollment;
use App\Models\LetterTemplate;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use setasign\Fpdi\Fpdi;

class LetterEndorsement
{
    public static function activePostingTemplate(Company $company): LetterTemplate
    {
        $template = LetterTemplate::where('company_id', $company->id)
            ->where('type', 'posting_letter')
            ->where('is_active', true)
            ->with('fieldMappings')
            ->first();

        if (! $template) {
            throw new \RuntimeException('No active posting letter template. Upload and configure field mappings in Settings first.');
        }

        if ($template->fieldMappings->isEmpty()) {
            throw new \RuntimeException('Posting letter template has no field mappings. Configure fields in the Letters tab first.');
        }

        return $template;
    }

    public static function buildFieldData(Enrollment $enrollment, Company $company): array
    {
        $enrollment->loadMissing(['user.personalInfo', 'user.educationInfo']);

        $user = $enrollment->user;
        $personalInfo = $user->personalInfo;
        $educationInfo = $user->educationInfo;

        $startDateObj = $enrollment->start_date ? \Carbon\Carbon::parse($enrollment->start_date) : null;
        $endDateObj = $enrollment->end_date ? \Carbon\Carbon::parse($enrollment->end_date) : null;
        $postingDateObj = $enrollment->endorsement_date ? \Carbon\Carbon::parse($enrollment->endorsement_date) : null;

        return [
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
    }

    public static function generatePdf(
        Enrollment $enrollment,
        Company $company,
        LetterTemplate $template,
        ?string $signaturePath,
        ?string $stampPath,
    ): string {
        $data = self::buildFieldData($enrollment, $company);
        $template->loadMissing('fieldMappings');

        $postingLetter = $enrollment->user->documents()->where('type', 'posting_letter')->first();
        $sourcePdfPath = null;

        if ($postingLetter) {
            $sourcePdfPath = storage_path('app/public/'.$postingLetter->file_path);
        }

        if (! $sourcePdfPath || ! file_exists($sourcePdfPath)) {
            $sourcePdfPath = storage_path('app/public/'.$template->template_file_path);
        }

        if (! file_exists($sourcePdfPath)) {
            throw new \RuntimeException('Template PDF or uploaded document file not found.');
        }

        $pdf = new Fpdi('P', 'pt');
        $pageCount = $pdf->setSourceFile($sourcePdfPath);

        for ($pageNum = 1; $pageNum <= $pageCount; $pageNum++) {
            $templateId = $pdf->importPage($pageNum);
            $size = $pdf->getTemplateSize($templateId);

            $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
            $pdf->useTemplate($templateId);

            $pageFields = $template->fieldMappings->where('page_number', $pageNum);
            foreach ($pageFields as $mapping) {
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
                    if ($mapping->text_alignment === 'center') {
                        $align = 'C';
                    }
                    if ($mapping->text_alignment === 'right') {
                        $align = 'R';
                    }

                    $pdf->Cell($w, $h, $text, 0, 0, $align);
                }
            }
        }

        $fileName = 'endorsed_letter_'.$enrollment->nss_number.'_'.now()->format('YmdHis').'.pdf';
        $filePath = 'endorsed_letters/'.$company->id.'/'.$fileName;

        Storage::disk('public')->makeDirectory('endorsed_letters/'.$company->id);
        $pdf->Output('F', Storage::disk('public')->path($filePath));

        return $filePath;
    }

    public static function endorse(Enrollment $enrollment, User $admin, Company $company): EndorsedLetter
    {
        $template = self::activePostingTemplate($company);

        $postingDate = now();
        $startDate = $enrollment->start_date ?? now();
        $endDate = $enrollment->end_date ?? now()->addYear();

        $enrollment->update([
            'status' => 'endorsed',
            'endorsement_date' => $postingDate,
            'start_date' => $startDate,
            'end_date' => $endDate,
        ]);

        try {
            $filePath = self::generatePdf(
                $enrollment->fresh(['user.personalInfo', 'user.educationInfo']),
                $company,
                $template,
                $company->digital_signature_path,
                $company->stamp_path,
            );
        } catch (\Throwable $e) {
            $enrollment->update([
                'status' => 'shortlisted',
                'endorsement_date' => null,
            ]);

            throw $e;
        }

        return EndorsedLetter::create([
            'enrollment_id' => $enrollment->id,
            'letter_template_id' => $template->id,
            'endorsed_by' => $admin->id,
            'generated_file_path' => $filePath,
            'status' => 'endorsed',
        ]);
    }

    public static function reEndorse(EndorsedLetter $letter, User $admin, Company $company): EndorsedLetter
    {
        $template = self::activePostingTemplate($company);
        $enrollment = $letter->enrollment()->with(['user.personalInfo', 'user.educationInfo'])->firstOrFail();

        $previousPath = $letter->generated_file_path;

        $enrollment->update([
            'status' => 'endorsed',
            'endorsement_date' => now(),
        ]);

        $filePath = self::generatePdf(
            $enrollment->fresh(['user.personalInfo', 'user.educationInfo']),
            $company,
            $template,
            $company->digital_signature_path,
            $company->stamp_path,
        );

        $letter->update([
            'letter_template_id' => $template->id,
            'endorsed_by' => $admin->id,
            'generated_file_path' => $filePath,
            'status' => 'endorsed',
            'validated_file_path' => null,
            'validated_by' => null,
            'validated_at' => null,
        ]);

        if ($previousPath && $previousPath !== $filePath && Storage::disk('public')->exists($previousPath)) {
            Storage::disk('public')->delete($previousPath);
        }

        return $letter->fresh();
    }
}
