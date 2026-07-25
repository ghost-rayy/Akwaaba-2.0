<?php

namespace App\Support;

use App\Models\AppointmentLetter;
use App\Models\Company;
use App\Models\Enrollment;
use App\Models\LetterTemplate;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use setasign\Fpdi\Fpdi;

class AppointmentLetterIssuer
{
    public static function activeAppointmentTemplate(Company $company): LetterTemplate
    {
        $template = LetterTemplate::where('company_id', $company->id)
            ->where('type', 'appointment_letter')
            ->where('is_active', true)
            ->with('fieldMappings')
            ->first();

        if (! $template) {
            throw new \RuntimeException('No active appointment letter template. Upload and configure field mappings in the Letters tab first.');
        }

        if ($template->fieldMappings->isEmpty()) {
            throw new \RuntimeException('Appointment letter template has no field mappings. Configure fields in the Letters tab first.');
        }

        if (! $template->template_file_path) {
            throw new \RuntimeException('Appointment letter template PDF is missing. Re-upload the template in the Letters tab.');
        }

        return $template;
    }

    public static function buildFieldData(Enrollment $enrollment, Company $company): array
    {
        $enrollment->loadMissing(['department']);

        $data = LetterEndorsement::buildFieldData($enrollment, $company);
        $data['department'] = $enrollment->department?->name ?? '';
        $data['todays_date'] = now()->format('d/m/Y');

        return $data;
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

        $sourcePdfPath = storage_path('app/public/'.$template->template_file_path);

        if (! file_exists($sourcePdfPath)) {
            throw new \RuntimeException('Appointment letter template PDF file not found.');
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

        $fileName = 'appointment_letter_'.$enrollment->nss_number.'_'.now()->format('YmdHis').'.pdf';
        $filePath = 'appointment_letters/'.$company->id.'/'.$fileName;

        Storage::disk('public')->makeDirectory('appointment_letters/'.$company->id);
        $pdf->Output('F', Storage::disk('public')->path($filePath));

        return $filePath;
    }

    public static function issue(Enrollment $enrollment, User $issuer, Company $company): AppointmentLetter
    {
        if (! in_array($enrollment->status, ['validated', 'active'], true)) {
            throw new \RuntimeException('Appointment letters can only be issued to validated personnel.');
        }

        if ((int) $enrollment->company_id !== (int) $company->id) {
            throw new \RuntimeException('Enrollment does not belong to this company.');
        }

        $template = self::activeAppointmentTemplate($company);

        $filePath = self::generatePdf(
            $enrollment->fresh(['user.personalInfo', 'user.educationInfo', 'department']),
            $company,
            $template,
            $company->digital_signature_path,
            $company->stamp_path,
        );

        $existing = AppointmentLetter::where('enrollment_id', $enrollment->id)->latest('id')->first();
        $previousPath = $existing?->generated_file_path;

        if ($existing) {
            $existing->update([
                'letter_template_id' => $template->id,
                'issued_by' => $issuer->id,
                'generated_file_path' => $filePath,
            ]);
            $letter = $existing->fresh();
        } else {
            $letter = AppointmentLetter::create([
                'enrollment_id' => $enrollment->id,
                'letter_template_id' => $template->id,
                'issued_by' => $issuer->id,
                'generated_file_path' => $filePath,
            ]);
        }

        if ($previousPath && $previousPath !== $filePath && Storage::disk('public')->exists($previousPath)) {
            Storage::disk('public')->delete($previousPath);
        }

        return $letter;
    }
}
