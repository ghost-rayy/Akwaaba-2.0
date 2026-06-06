<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EndorsedLetter extends Model
{
    protected $fillable = [
        'enrollment_id', 'letter_template_id', 'endorsed_by',
        'generated_file_path', 'status', 'validated_file_path', 'validated_at', 'validated_by',
    ];

    public function enrollment()
    {
        return $this->belongsTo(Enrollment::class);
    }

    public function letterTemplate()
    {
        return $this->belongsTo(LetterTemplate::class);
    }

    public function endorsedBy()
    {
        return $this->belongsTo(User::class, 'endorsed_by');
    }

    public function validatedBy()
    {
        return $this->belongsTo(User::class, 'validated_by');
    }
}
