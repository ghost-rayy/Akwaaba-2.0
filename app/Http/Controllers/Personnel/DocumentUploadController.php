<?php

namespace App\Http\Controllers\Personnel;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DocumentUploadController extends Controller
{
    public function postingLetter(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'file' => 'required|file|mimes:pdf|max:5120',
        ]);

        $user = auth('personnel')->user();
        $file = $validated['file'];
        $path = $file->store('documents/'.$user->id.'/pending', 'public');

        return response()->json([
            'path' => $path,
            'name' => $file->getClientOriginalName(),
        ]);
    }

    public function passportPhoto(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'file' => 'required|file|mimes:jpg,jpeg,png|max:2048',
        ]);

        $user = auth('personnel')->user();
        $file = $validated['file'];
        $path = $file->store('documents/'.$user->id.'/pending', 'public');

        return response()->json([
            'path' => $path,
            'name' => $file->getClientOriginalName(),
        ]);
    }
}
