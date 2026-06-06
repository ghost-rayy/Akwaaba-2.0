<?php

namespace App\Exports;

use App\Models\Attendance;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class AttendanceExport implements FromQuery, WithHeadings, WithMapping
{
    protected $companyId;
    protected $startDate;
    protected $endDate;

    public function __construct($companyId, $startDate, $endDate)
    {
        $this->companyId = $companyId;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function query()
    {
        return Attendance::where('company_id', $this->companyId)
            ->whereBetween('date', [$this->startDate, $this->endDate])
            ->with('user');
    }

    public function headings(): array
    {
        return ['Name', 'NSS Number', 'Date', 'Status', 'Check In', 'Check Out', 'Remarks'];
    }

    public function map($attendance): array
    {
        return [
            $attendance->user->name,
            $attendance->user->nss_number ?? 'N/A',
            $attendance->date->format('d M Y'),
            ucfirst($attendance->status),
            $attendance->check_in ? substr($attendance->check_in, 0, 5) : '-',
            $attendance->check_out ? substr($attendance->check_out, 0, 5) : '-',
            $attendance->remarks ?? '',
        ];
    }
}
