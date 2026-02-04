<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Patient extends Model
{
    protected $fillable = [
        'user_id',
        'blood_type_id',
        'allergies',
        'chronic_diseases',
        'surgery_history',
        'observations',
        'emergency_contact_name',
        'emergency_contact_phone',
        'emergency_relationship',
    ];

    /**
     * Relación uno a uno inversa con User
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relación con BloodType
     */
    public function bloodType(): BelongsTo
    {
        return $this->belongsTo(BloodType::class);
    }
}
