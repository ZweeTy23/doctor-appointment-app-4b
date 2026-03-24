<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Recordatorios de citas: se ejecuta todos los días a las 08:00 AM
// Envía WhatsApp a los pacientes con cita programada para el día siguiente
Schedule::command('appointments:send-reminders')->dailyAt('08:00');

// Reporte diario: correo al administrador y a cada doctor con citas programadas para hoy
Schedule::command('appointments:send-daily-digest')->dailyAt('08:00');
