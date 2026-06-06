<?php

namespace App\Livewire\Personnel;

use App\Models\EndorsedLetter;
use Livewire\Component;
use Livewire\WithFileUploads;

class Documents extends Component
{
    use WithFileUploads;

    public $validatedFile;

    public function uploadValidatedLetter($letterId)
    {
        $this->validate([
            'validatedFile' => 'required|file|mimes:pdf|max:10240',
        ]);

        $letter = EndorsedLetter::whereHas('enrollment', function ($q) {
            $q->where('user_id', auth()->id());
        })->findOrFail($letterId);

        $path = $this->validatedFile->store('validated_letters/' . auth()->id(), 'public');

        $letter->update([
            'validated_file_path' => $path,
        ]);

        $this->reset(['validatedFile']);

        session()->flash('success', 'Validated endorsed posting letter uploaded successfully.');
    }

    public function render()
    {
        $user = auth()->user();

        $endorsedLetters = EndorsedLetter::whereHas('enrollment', function ($q) use ($user) {
            $q->where('user_id', $user->id);
        })->with(['letterTemplate', 'enrollment.department'])->latest()->get();

        return view('livewire.personnel.documents', [
            'endorsedLetters' => $endorsedLetters,
        ])->layout('layouts.personnel');
    }
}
