<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    // System roles
    public const ROLE_ADMINISTRATOR = 'administrator';
    public const ROLE_BHW = 'barangay_health_worker';
    public const ROLE_HEALTHCARE = 'healthcare_personnel';
    public const ROLE_PATIENT = 'patient';

    public const ROLES = [
        self::ROLE_ADMINISTRATOR,
        self::ROLE_BHW,
        self::ROLE_HEALTHCARE,
        self::ROLE_PATIENT,
    ];

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
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
        ];
    }

    public function hasRole(string $role): bool
    {
        return $this->role === $role;
    }

    public function getRoleNameAttribute(): string
    {
        return match ($this->role) {
            self::ROLE_ADMINISTRATOR => 'Administrator',
            self::ROLE_BHW => 'Barangay Health Worker',
            self::ROLE_HEALTHCARE => 'Healthcare Personnel',
            self::ROLE_PATIENT => 'Patient',
            default => 'Unknown Role',
        };
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class, 'patient_id');
    }
}
