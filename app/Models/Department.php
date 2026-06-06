<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    protected $fillable = [
        'company_id', 'name', 'head_id', 'supervisor_id', 'is_active',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function head()
    {
        return $this->belongsTo(User::class, 'head_id');
    }

    public function supervisor()
    {
        return $this->belongsTo(User::class, 'supervisor_id');
    }

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }
}
