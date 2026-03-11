<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\User;
use App\Models\Speciality;
use App\Models\Availability;
use App\Models\Appointment;

class Doctor extends Model
{
    protected $fillable = ['user_id', 'speciality_id', 'medical_license_number', 'biography'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function speciality(): BelongsTo
    {
        return $this->belongsTo(Speciality::class);
    }

    // Relación uno a muchos con disponibilidad horaria
    public function availabilities(): HasMany
    {
        return $this->hasMany(Availability::class);
    }

    // Relación uno a muchos con citas médicas
    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }
}

