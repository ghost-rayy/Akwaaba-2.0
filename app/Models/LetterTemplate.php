<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LetterTemplate extends Model
{
    protected $fillable = [
        'company_id', 'name', 'type', 'template_file_path',
        'pages_count', 'is_active',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function fieldMappings()
    {
        return $this->hasMany(TemplateFieldMapping::class);
    }

    public function endorsedLetters()
    {
        return $this->hasMany(EndorsedLetter::class);
    }
}
