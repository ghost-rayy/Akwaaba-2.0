<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TemplateFieldMapping extends Model
{
    protected $fillable = [
        'letter_template_id', 'field_key', 'field_type', 'label',
        'page_number', 'x', 'y', 'width', 'height', 'font_size',
        'font_family', 'text_alignment', 'is_required',
    ];

    public function letterTemplate()
    {
        return $this->belongsTo(LetterTemplate::class);
    }
}
