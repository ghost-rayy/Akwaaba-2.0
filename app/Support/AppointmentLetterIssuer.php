<?php

namespace App\Support;

use App\Models\AppointmentLetter;
use App\Models\Company;
use App\Models\Enrollment;
use App\Models\LetterTemplate;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class AppointmentLetterIssuer
{
    public static function activeAppointmentTemplate(Company $company): LetterTemplate
    {
        $template = LetterTemplate::where('company_id', $company->id)
            ->where('type', 'appointment_letter')
            ->where('is_active', true)
            ->first();

        if (! $template || ! filled($template->body)) {
            throw new \RuntimeException('No appointment letter draft found. Draft and save the letter in the Letters tab first.');
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

    public static function renderBody(string $body, Enrollment $enrollment, Company $company): string
    {
        $data = self::buildFieldData($enrollment, $company);

        $signatureHtml = '';
        if ($company->digital_signature_path && Storage::disk('public')->exists($company->digital_signature_path)) {
            $signatureHtml = self::imageTag(Storage::disk('public')->path($company->digital_signature_path), 'Signature');
        }

        $stampHtml = '';
        if ($company->stamp_path && Storage::disk('public')->exists($company->stamp_path)) {
            $stampHtml = self::imageTag(Storage::disk('public')->path($company->stamp_path), 'Stamp');
        }

        $replacements = [];
        foreach ($data as $key => $value) {
            if ($key === 'signature' || $key === 'stamp') {
                continue;
            }
            $replacements['{{'.$key.'}}'] = e((string) $value);
        }
        $replacements['{{signature}}'] = $signatureHtml;
        $replacements['{{stamp}}'] = $stampHtml;

        $rendered = strtr($body, $replacements);

        // Preserve plain-text paragraphs when the draft has no HTML tags
        if ($rendered === strip_tags($rendered)) {
            $rendered = nl2br($rendered);
        }

        return $rendered;
    }

    public static function generatePdf(
        Enrollment $enrollment,
        Company $company,
        LetterTemplate $template,
    ): string {
        $content = self::renderBody(
            $template->body,
            $enrollment->fresh(['user.personalInfo', 'user.educationInfo', 'department']),
            $company,
        );

        $pdf = Pdf::loadView('pdf.appointment-letter', [
            'content' => $content,
            'company' => $company,
        ])->setPaper('a4');

        $fileName = 'appointment_letter_'.$enrollment->nss_number.'_'.now()->format('YmdHis').'.pdf';
        $filePath = 'appointment_letters/'.$company->id.'/'.$fileName;

        Storage::disk('public')->makeDirectory('appointment_letters/'.$company->id);
        Storage::disk('public')->put($filePath, $pdf->output());

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

    protected static function imageTag(string $absolutePath, string $alt): string
    {
        if (! file_exists($absolutePath)) {
            return '';
        }

        $mime = mime_content_type($absolutePath) ?: 'image/png';
        $data = base64_encode(file_get_contents($absolutePath));

        return '<img src="data:'.$mime.';base64,'.$data.'" alt="'.e($alt).'" style="max-height:80px;max-width:200px;" />';
    }
}
