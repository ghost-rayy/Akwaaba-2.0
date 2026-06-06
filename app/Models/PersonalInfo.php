<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PersonalInfo extends Model
{
    protected $fillable = [
        'user_id', 'full_name', 'nss_number', 'phone', 'email',
        'place_of_residence', 'region_of_residence',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
