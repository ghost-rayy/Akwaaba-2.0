<?php

namespace App\Livewire\Company;

use App\Models\Enrollment;
use App\Models\Evaluation as EvaluationModel;
use App\Support\DispatchesToast;
use Livewire\Component;

class Evaluation extends Component
{
    use DispatchesToast;
    public $selectedPersonnelId = '';
    public $periodStart;
    public $periodEnd;
    public $punctualityScore = 0;
    public $performanceScore = 0;
    public $attitudeScore = 0;
    public $teamworkScore = 0;
    public $comments = '';
    public $recommendation = '';

    protected function rules()
    {
        return [
            'selectedPersonnelId' => 'required|exists:enrollments,id',
            'periodStart' => 'required|date',
            'periodEnd' => 'required|date|after_or_equal:periodStart',
            'punctualityScore' => 'required|integer|min:1|max:5',
            'performanceScore' => 'required|integer|min:1|max:5',
            'attitudeScore' => 'required|integer|min:1|max:5',
            'teamworkScore' => 'required|integer|min:1|max:5',
            'comments' => 'nullable|string',
            'recommendation' => 'nullable|string',
        ];
    }

    public function mount()
    {
        $this->periodStart = now()->subMonth()->format('Y-m-d');
        $this->periodEnd = now()->format('Y-m-d');
    }

    public function save()
    {
        $this->validate();

        $enrollment = Enrollment::with('user')
            ->where('company_id', auth()->user()->company_id)
            ->whereIn('status', ['validated', 'active'])
            ->findOrFail($this->selectedPersonnelId);

        $overall = round(($this->punctualityScore + $this->performanceScore + $this->attitudeScore + $this->teamworkScore) / 4, 2);

        EvaluationModel::create([
            'user_id' => $enrollment->user_id,
            'company_id' => auth()->user()->company_id,
            'evaluator_id' => auth()->id(),
            'period_start' => $this->periodStart,
            'period_end' => $this->periodEnd,
            'punctuality_score' => $this->punctualityScore,
            'performance_score' => $this->performanceScore,
            'attitude_score' => $this->attitudeScore,
            'teamwork_score' => $this->teamworkScore,
            'overall_score' => $overall,
            'comments' => $this->comments,
            'recommendation' => $this->recommendation,
        ]);

        $this->reset(['selectedPersonnelId', 'punctualityScore', 'performanceScore', 'attitudeScore', 'teamworkScore', 'comments', 'recommendation']);
        $this->mount();

        $this->toastSuccess('Evaluation saved successfully. Overall score: '.$overall.'/5');
    }

    public function render()
    {
        $company = auth()->user()->company;

        return view('livewire.company.evaluation', [
            'personnelList' => Enrollment::where('company_id', $company->id)
                ->whereIn('status', ['validated', 'active'])
                ->with('user')
                ->latest()
                ->get(),
            'recentEvaluations' => EvaluationModel::where('company_id', $company->id)
                ->with(['user', 'evaluator'])
                ->latest()
                ->take(20)
                ->get(),
        ])->layout('layouts.company');
    }
}
