<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Availability extends Model
{
    protected $fillable = [
        'doctor_id',
        'day_of_week',
        'start_time',
        'end_time',
        'is_active',
    ];

    /**
     * Nombres de los días de la semana en español.
     */
    public const DAYS = [
        0 => 'Lunes',
        1 => 'Martes',
        2 => 'Miércoles',
        3 => 'Jueves',
        4 => 'Viernes',
        5 => 'Sábado',
        6 => 'Domingo',
    ];

    /**
     * Relación: la disponibilidad pertenece a un doctor.
     */
    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Doctor::class);
    }
}
