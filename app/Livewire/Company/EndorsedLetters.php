<?php

namespace App\Livewire\Company;

use App\Models\EndorsedLetter;
use App\Support\DispatchesToast;
use Livewire\Component;
use Livewire\WithPagination;

class EndorsedLetters extends Component
{
    use DispatchesToast, WithPagination;
    public $previewUrl = null;
    public $previewName = null;
    public $rejectingId = null;
    public $rejectReason = '';

    public function preview($letterId)
    {
        $letter = EndorsedLetter::with('enrollment.user')
            ->whereHas('enrollment', fn($q) => $q->where('company_id', auth()->user()->company_id))
            ->findOrFail($letterId);

        $this->previewUrl = '/storage/'.$letter->generated_file_path.'?v='.$letter->updated_at->timestamp;
        $this->previewName = $letter->enrollment->nss_number . ' — ' . $letter->enrollment->user->name;
    }

    public function closePreview()
    {
        $this->previewUrl = null;
        $this->previewName = null;
    }

    public function confirmValidate($letterId)
    {
        $letter = EndorsedLetter::whereHas('enrollment', fn($q) => $q->where('company_id', auth()->user()->company_id))
            ->findOrFail($letterId);

        $letter->update([
            'validated_by' => auth()->id(),
            'validated_at' => now(),
        ]);

        $letter->enrollment->update([
            'status' => 'validated',
        ]);

        $this->toastSuccess('Personnel validated successfully.');
    }

    public function confirmReject($letterId)
    {
        $this->rejectingId = $letterId;
        $this->rejectReason = '';
    }

    public function reject()
    {
        $this->validate(['rejectReason' => 'required|string|max:500']);

        $letter = EndorsedLetter::whereHas('enrollment', fn($q) => $q->where('company_id', auth()->user()->company_id))
            ->findOrFail($this->rejectingId);

        $letter->enrollment->update([
            'status' => 'rejected',
            'rejection_reason' => $this->rejectReason,
        ]);

        $this->rejectingId = null;
        $this->rejectReason = '';

        $this->toastSuccess('Personnel rejected.');
    }

    public function reEndorse($letterId)
    {
        $user = auth()->user();
        $company = $user->company;

        try {
            $letter = EndorsedLetter::whereHas('enrollment', function ($q) use ($company) {
                $q->where('company_id', $company->id);
            })->with('enrollment.user')->findOrFail($letterId);

            LetterEndorsement::reEndorse($letter, $user, $company);
        } catch (\Throwable $e) {
            $this->toastError($e->getMessage());

            return;
        }

        $this->toastSuccess($letter->enrollment->user->name."'s letter re-endorsed. Personnel can download the updated PDF.");
    }

    public function render()
    {
        $company = auth()->user()->company;

        return view('livewire.company.endorsed-letters', [
            'letters' => EndorsedLetter::whereHas('enrollment', fn($q) => $q->where('company_id', $company->id))
                ->with(['enrollment.user.passportPhoto', 'enrollment.department', 'endorsedBy', 'validatedBy'])
                ->latest('updated_at')->paginate(10),
        ])->layout('layouts.company');
    }
}
