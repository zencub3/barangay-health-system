<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    protected $fillable = [
    'user_id',
    'consultation_date',
    'time_slot',
    'symptoms',
    'other_service',
    'status',
    'rejection_reason',
];

    // Define relationship to User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function patient()
    {
    return $this->belongsTo(User::class, 'user_id');
    }
}
