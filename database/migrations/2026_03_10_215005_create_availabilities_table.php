<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Crear la tabla de disponibilidad de doctores.
     * Cada fila representa un bloque de 15 minutos para un día de la semana.
     */
    public function up(): void
    {
        Schema::create('availabilities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('doctor_id')->constrained()->cascadeOnDelete();
            $table->tinyInteger('day_of_week');  // 0=Lunes, 1=Martes … 6=Domingo
            $table->time('start_time');
            $table->time('end_time');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            // Índice compuesto para búsquedas rápidas de disponibilidad
            $table->index(['doctor_id', 'day_of_week', 'start_time']);
        });
    }

    /**
     * Revertir la migración.
     */
    public function down(): void
    {
        Schema::dropIfExists('availabilities');
    }
};
