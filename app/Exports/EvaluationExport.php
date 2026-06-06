<?php

namespace App\Exports;

use App\Models\Evaluation;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class EvaluationExport implements FromQuery, WithHeadings, WithMapping
{
    protected $companyId;

    public function __construct($companyId)
    {
        $this->companyId = $companyId;
    }

    public function query()
    {
        return Evaluation::where('company_id', $this->companyId)
            ->with(['user', 'evaluator']);
    }

    public function headings(): array
    {
        return [
            'Name', 'NSS Number', 'Period Start', 'Period End',
            'Punctuality', 'Performance', 'Attitude', 'Teamwork',
            'Overall', 'Comments', 'Recommendation', 'Evaluated By', 'Date',
        ];
    }

    public function map($evaluation): array
    {
        return [
            $evaluation->user->name,
            $evaluation->user->nss_number ?? 'N/A',
            $evaluation->period_start->format('d M Y'),
            $evaluation->period_end->format('d M Y'),
            $evaluation->punctuality_score,
            $evaluation->performance_score,
            $evaluation->attitude_score,
            $evaluation->teamwork_score,
            $evaluation->overall_score,
            $evaluation->comments ?? '',
            $evaluation->recommendation ?? '',
            $evaluation->evaluator->name,
            $evaluation->created_at->format('d M Y'),
        ];
    }
}
