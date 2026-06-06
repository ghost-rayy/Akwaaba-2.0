<?php

namespace App\Exports;

use App\Models\Enrollment;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class PersonnelExport implements FromQuery, WithHeadings, WithMapping
{
    protected $companyId;

    public function __construct($companyId)
    {
        $this->companyId = $companyId;
    }

    public function query()
    {
        return Enrollment::where('company_id', $this->companyId)
            ->with(['user', 'user.personalInfo', 'department']);
    }

    public function headings(): array
    {
        return ['Name', 'NSS Number', 'Email', 'Phone', 'Department', 'Status', 'Enrolled Date'];
    }

    public function map($enrollment): array
    {
        return [
            $enrollment->user->name,
            $enrollment->nss_number,
            $enrollment->user->email,
            $enrollment->user->phone,
            $enrollment->department?->name ?? 'N/A',
            str_replace('_', ' ', ucfirst($enrollment->status)),
            $enrollment->created_at->format('d M Y'),
        ];
    }
}
