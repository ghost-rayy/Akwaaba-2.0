<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'name', 'email', 'password', 'role', 'company_id',
        'phone', 'nss_number', 'must_change_password', 'form_step',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'must_change_password' => 'boolean',
        ];
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function enrollment()
    {
        return $this->hasOne(Enrollment::class);
    }

    public function personalInfo()
    {
        return $this->hasOne(PersonalInfo::class);
    }

    public function educationInfo()
    {
        return $this->hasOne(EducationInfo::class);
    }

    public function attendance()
    {
        return $this->hasMany(Attendance::class);
    }

    public function evaluations()
    {
        return $this->hasMany(Evaluation::class, 'user_id');
    }

    public function evaluationsGiven()
    {
        return $this->hasMany(Evaluation::class, 'evaluator_id');
    }

    public function endorsedLetters()
    {
        return $this->hasMany(EndorsedLetter::class, 'endorsed_by');
    }

    public function enrolledPersonnel()
    {
        return $this->hasMany(Enrollment::class, 'enrolled_by');
    }

    public function headedDepartments()
    {
        return $this->hasMany(Department::class, 'head_id');
    }

    public function supervisedDepartments()
    {
        return $this->hasMany(Department::class, 'supervisor_id');
    }

    public function documents()
    {
        return $this->hasMany(Document::class);
    }

    public function passportPhoto()
    {
        return $this->hasOne(Document::class)->ofMany(
            ['id' => 'max'],
            fn ($query) => $query->where('type', 'passport')
        );
    }

    public function profilePhotoUrl(): ?string
    {
        $path = $this->passportPhoto?->file_path;

        return $path ? asset('storage/'.$path) : null;
    }

    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin';
    }

    public function isCompanyAdmin(): bool
    {
        return $this->role === 'company_admin';
    }

    public function isHrStaff(): bool
    {
        return $this->role === 'hr_staff';
    }

    public function isNssPersonnel(): bool
    {
        return $this->role === 'nss_personnel';
    }

    public function isCompanyUser(): bool
    {
        return in_array($this->role, ['company_admin', 'hr_staff']);
    }
}
