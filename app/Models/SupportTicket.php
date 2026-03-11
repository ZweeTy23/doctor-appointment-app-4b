<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupportTicket extends Model
{
    /**
     * Campos asignables en masa.
     */
    protected $fillable = [
        'title',
        'description',
        'status',
        'priority',
        'user_id',
        'admin_response',
    ];

    /**
     * Relación: el ticket pertenece a un usuario.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
