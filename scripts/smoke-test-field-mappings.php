<?php

use App\Livewire\Company\EndorseLetters;
use App\Models\Enrollment;
use App\Models\LetterTemplate;
use Illuminate\Support\Facades\Storage;

require __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== Field Mapping Smoke Test ===\n\n";

$template = LetterTemplate::with('fieldMappings')
    ->where('type', 'posting_letter')
    ->where('is_active', true)
    ->first();

if (! $template) {
    echo "FAIL: No active posting letter template found.\n";
    exit(1);
}

$enrollment = Enrollment::where('status', 'shortlisted')
    ->with(['user.personalInfo', 'user.educationInfo', 'user.documents', 'company'])
    ->first();

if (! $enrollment) {
    $enrollment = Enrollment::with(['user.personalInfo', 'user.educationInfo', 'user.documents', 'company'])
        ->whereHas('user.documents', fn ($q) => $q->where('type', 'posting_letter'))
        ->first();
}

if (! $enrollment) {
    echo "FAIL: No enrollment with personnel data found.\n";
    exit(1);
}

$company = $enrollment->company;
$component = new EndorseLetters;
$buildFieldData = new ReflectionMethod(EndorseLetters::class, 'buildFieldData');
$buildFieldData->setAccessible(true);
$data = $buildFieldData->invoke($component, $enrollment, $company, $template);

$generatePdf = new ReflectionMethod(EndorseLetters::class, 'generateEndorsedPdf');
$generatePdf->setAccessible(true);

echo "Template: {$template->name} (ID {$template->id})\n";
echo "Company: {$company->name}\n";
echo "Personnel: {$enrollment->user->name} ({$enrollment->nss_number}) — status: {$enrollment->status}\n";
echo 'Field mappings: '.$template->fieldMappings->count()."\n\n";

$passed = 0;
$failed = 0;
$warnings = 0;

foreach ($template->fieldMappings as $mapping) {
    $key = $mapping->field_key;
    $label = $mapping->label ?: $key;

    if ($key === 'signature') {
        $path = $company->digital_signature_path;
        $exists = $path && file_exists(Storage::disk('public')->path($path));
        $status = $exists ? 'PASS (image file present)' : 'WARN (no signature uploaded — image will be skipped)';
        $exists ? $passed++ : $warnings++;
    } elseif ($key === 'stamp') {
        $path = $company->stamp_path;
        $exists = $path && file_exists(Storage::disk('public')->path($path));
        $status = $exists ? 'PASS (image file present)' : 'WARN (no stamp uploaded — image will be skipped)';
        $exists ? $passed++ : $warnings++;
    } else {
        $value = $data[$key] ?? null;
        if ($value === null) {
            $status = 'FAIL (field key not in buildFieldData — will render blank)';
            $failed++;
        } elseif ($value === '') {
            $status = 'WARN (empty value — will render blank on PDF)';
            $warnings++;
        } else {
            $status = 'PASS';
            $passed++;
        }
        $display = is_string($value) ? $value : json_encode($value);
        echo sprintf("  [%s] %s (%s) page %d\n", $status, $label, $key, $mapping->page_number);
        if ($key !== 'signature' && $key !== 'stamp') {
            echo "         Value: {$display}\n";
        }

        continue;
    }

    echo sprintf("  [%s] %s (%s) page %d\n", $status, $label, $key, $mapping->page_number);
}

// Check for mapped keys missing from buildFieldData
$availableInBuilder = array_keys($data);
$mappedKeys = $template->fieldMappings->pluck('field_key')->unique()->values()->all();
$unmappedInBuilder = array_diff($mappedKeys, $availableInBuilder, ['signature', 'stamp']);

if ($unmappedInBuilder) {
    echo "\nFAIL: These mapped keys have no data source in buildFieldData:\n";
    foreach ($unmappedInBuilder as $key) {
        echo "  - {$key}\n";
    }
    $failed += count($unmappedInBuilder);
}

// Attempt PDF generation
echo "\n--- PDF Generation ---\n";
$sourcePdf = storage_path('app/public/'.$template->template_file_path);
$postingLetter = $enrollment->user->documents()->where('type', 'posting_letter')->first();
if ($postingLetter) {
    $personnelPdf = storage_path('app/public/'.$postingLetter->file_path);
    echo 'Personnel posting letter: '.($postingLetter->file_path).(file_exists($personnelPdf) ? ' (exists)' : ' (MISSING)')."\n";
}
echo 'Template PDF: '.$template->template_file_path.(file_exists($sourcePdf) ? ' (exists)' : ' (MISSING)')."\n";

if (! file_exists($sourcePdf) && ! ($postingLetter && file_exists(storage_path('app/public/'.$postingLetter->file_path)))) {
    echo "FAIL: No source PDF available for overlay test.\n";
    exit(1);
}

try {
    $outputPath = $generatePdf->invoke(
        $component,
        $enrollment,
        $company,
        $template,
        $company->digital_signature_path,
        $company->stamp_path,
    );

    $fullOutput = Storage::disk('public')->path($outputPath);
    $size = file_exists($fullOutput) ? filesize($fullOutput) : 0;
    echo "PASS: Generated endorsed PDF at {$outputPath} ({$size} bytes)\n";

    // Clean up smoke test output
    if (file_exists($fullOutput)) {
        unlink($fullOutput);
        echo "      (test PDF removed)\n";
    }
} catch (Throwable $e) {
    echo 'FAIL: PDF generation error — '.$e->getMessage()."\n";
    $failed++;
}

echo "\n=== Summary ===\n";
echo "Passed:  {$passed}\n";
echo "Warnings: {$warnings}\n";
echo "Failed:  {$failed}\n";

exit($failed > 0 ? 1 : 0);
