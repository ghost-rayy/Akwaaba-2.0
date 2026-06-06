<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    protected $fillable = [
        'name', 'email', 'phone', 'location', 'postal_address', 'posting_date',
        'digital_signature_path', 'stamp_path', 'posting_letter_path', 'registration_number',
        'contact_person', 'is_active',
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function departments()
    {
        return $this->hasMany(Department::class);
    }

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }

    public function letterTemplates()
    {
        return $this->hasMany(LetterTemplate::class);
    }

    public function attendance()
    {
        return $this->hasMany(Attendance::class);
    }

    public function evaluations()
    {
        return $this->hasMany(Evaluation::class);
    }
}
