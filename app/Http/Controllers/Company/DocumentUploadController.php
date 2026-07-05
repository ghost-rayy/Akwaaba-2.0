<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\LetterTemplate;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DocumentUploadController extends Controller
{
    public function logo(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'file' => 'required|image|mimes:png,jpg,jpeg|max:2048',
        ]);

        $company = auth('company')->user()->company;
        $path = $validated['file']->store('logos/'.$company->id, 'public');
        $company->update(['logo_path' => $path]);

        return response()->json([
            'path' => $path,
            'logo_url' => asset('storage/'.$path),
            'message' => 'Company logo uploaded.',
        ]);
    }

    public function stamp(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'file' => 'required|image|mimes:png,jpg,jpeg|max:2048',
        ]);

        $company = auth('company')->user()->company;
        $path = $validated['file']->store('stamps/'.$company->id, 'public');
        $company->update(['stamp_path' => $path]);

        return response()->json([
            'path' => $path,
            'message' => 'Stamp uploaded.',
        ]);
    }

    public function signature(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'file' => 'required|image|mimes:png,jpg,jpeg|max:2048',
        ]);

        $company = auth('company')->user()->company;
        $path = $validated['file']->store('signatures/'.$company->id, 'public');
        $company->update(['digital_signature_path' => $path]);

        return response()->json([
            'path' => $path,
            'message' => 'Signature uploaded.',
        ]);
    }

    public function postingLetter(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'file' => 'required|file|mimes:pdf|max:10240',
        ]);

        $company = auth('company')->user()->company;
        $path = $validated['file']->store('postingletters/'.$company->id, 'public');
        $company->update(['posting_letter_path' => $path]);

        LetterTemplate::updateOrCreate(
            ['company_id' => $company->id, 'type' => 'posting_letter'],
            [
                'name' => $company->name.' Posting Letter',
                'template_file_path' => $path,
                'is_active' => true,
            ]
        );

        return response()->json([
            'path' => $path,
            'message' => 'Posting letter template uploaded.',
        ]);
    }
}
