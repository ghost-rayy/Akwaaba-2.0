<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Enrollment extends Model
{
    protected $fillable = [
        'user_id', 'company_id', 'department_id', 'enrolled_by',
        'nss_number', 'nss_year', 'status', 'start_date', 'end_date',
        'endorsement_date', 'validated_at', 'rejection_reason',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function enrolledBy()
    {
        return $this->belongsTo(User::class, 'enrolled_by');
    }

    public function endorsedLetters()
    {
        return $this->hasMany(EndorsedLetter::class);
    }
}
