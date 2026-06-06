<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Evaluation extends Model
{
    protected $fillable = [
        'user_id', 'company_id', 'evaluator_id', 'period_start', 'period_end',
        'punctuality_score', 'performance_score', 'attitude_score',
        'teamwork_score', 'overall_score', 'comments', 'recommendation',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function evaluator()
    {
        return $this->belongsTo(User::class, 'evaluator_id');
    }
}
