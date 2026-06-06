<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EducationInfo extends Model
{
    protected $fillable = [
        'user_id', 'university', 'city_of_school', 'region_of_school',
        'form_of_education', 'programme_of_study',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
