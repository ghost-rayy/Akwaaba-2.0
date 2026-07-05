<?php

namespace App\Livewire\Company;

use App\Models\Enrollment;
use App\Support\DispatchesToast;
use Livewire\Component;

class Shortlist extends Component
{
    use DispatchesToast;
    public $selectedPersonnelId = null;
    public $rejectionReason = '';
    public $confirmingRejection = false;
    public $viewingLetterId = null;
    public $viewingLetterUrl = '';
    public $viewingLetterBase64 = '';

    public function shortlist($enrollmentId)
    {
        $enrollment = Enrollment::where('company_id', auth()->user()->company_id)
            ->where('id', $enrollmentId)
            ->where('status', 'pending_review')
            ->firstOrFail();

        $enrollment->update(['status' => 'shortlisted']);

        $this->toastSuccess('Personnel shortlisted successfully.');
    }

    public function confirmReject($enrollmentId)
    {
        $this->selectedPersonnelId = $enrollmentId;
        $this->rejectionReason = '';
        $this->confirmingRejection = true;
    }

    public function reject()
    {
        $this->validate(['rejectionReason' => 'required|string|max:1000']);

        $enrollment = Enrollment::where('company_id', auth()->user()->company_id)
            ->where('id', $this->selectedPersonnelId)
            ->where('status', 'pending_review')
            ->firstOrFail();

        $enrollment->update([
            'status' => 'rejected',
            'rejection_reason' => $this->rejectionReason,
        ]);

        $this->confirmingRejection = false;
        $this->selectedPersonnelId = null;
        $this->rejectionReason = '';

        $this->toastSuccess('Personnel rejected.');
    }

    public function viewLetter($enrollmentId)
    {
        $enrollment = Enrollment::with('user')->findOrFail($enrollmentId);
        $letter = $enrollment->user->documents()->where('type', 'posting_letter')->first();
        if ($letter) {
            $path = storage_path('app/public/' . $letter->file_path);
            if (file_exists($path)) {
                $this->viewingLetterBase64 = base64_encode(file_get_contents($path));
                $this->viewingLetterId = $enrollmentId;
            }
        }
    }

    public function closeViewer()
    {
        $this->viewingLetterId = null;
        $this->viewingLetterUrl = '';
        $this->viewingLetterBase64 = '';
    }

    public function render()
    {
        $company = auth()->user()->company;

        return view('livewire.company.shortlist', [
            'pendingPersonnel' => Enrollment::where('company_id', $company->id)
                ->where('status', 'pending_review')
                ->with(['user', 'user.personalInfo', 'department'])
                ->latest()
                ->get(),
            'processedCount' => Enrollment::where('company_id', $company->id)
                ->whereIn('status', ['shortlisted', 'rejected'])
                ->count(),
        ])->layout('layouts.company');
    }
}
