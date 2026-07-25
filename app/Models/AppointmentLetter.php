<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppointmentLetter extends Model
{
    protected $fillable = [
        'enrollment_id', 'letter_template_id', 'issued_by', 'generated_file_path',
    ];

    public function enrollment()
    {
        return $this->belongsTo(Enrollment::class);
    }

    public function letterTemplate()
    {
        return $this->belongsTo(LetterTemplate::class);
    }

    public function issuedBy()
    {
        return $this->belongsTo(User::class, 'issued_by');
    }
}
