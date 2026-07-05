<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $table = 'attendance';

    protected $fillable = [
        'user_id',
        'company_id',
        'date',
        'check_in',
        'check_out',
        'status',
        'remarks',
        'check_in_validated_at',
        'check_in_validated_by',
        'check_out_validated_at',
        'check_out_validated_by',
        'absence_validated_at',
        'absence_validated_by',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'check_in_validated_at' => 'datetime',
            'check_out_validated_at' => 'datetime',
            'absence_validated_at' => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function checkInValidator()
    {
        return $this->belongsTo(User::class, 'check_in_validated_by');
    }

    public function checkOutValidator()
    {
        return $this->belongsTo(User::class, 'check_out_validated_by');
    }

    public function absenceValidator()
    {
        return $this->belongsTo(User::class, 'absence_validated_by');
    }

    public function isAbsent(): bool
    {
        return $this->status === 'absent';
    }

    public function needsCheckInValidation(): bool
    {
        return $this->check_in && ! $this->check_in_validated_at;
    }

    public function needsCheckOutValidation(): bool
    {
        return $this->check_out && ! $this->check_out_validated_at;
    }

    public function needsAbsenceValidation(): bool
    {
        return $this->isAbsent() && ! $this->absence_validated_at;
    }

    public function hasPendingValidation(): bool
    {
        return $this->needsCheckInValidation()
            || $this->needsCheckOutValidation()
            || $this->needsAbsenceValidation();
    }
}
