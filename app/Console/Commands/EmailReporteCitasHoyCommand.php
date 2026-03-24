<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class EmailReporteCitasHoyCommand extends Command
{
    protected $signature = 'appointments:email-reporte-hoy';

    protected $description = 'Envía por correo el reporte de todas las citas programadas para hoy (admin + cada doctor)';

    public function handle(): int
    {
        return $this->call('appointments:send-daily-digest');
    }
}
