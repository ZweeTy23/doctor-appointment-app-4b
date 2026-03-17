<?php

namespace App\Console\Commands;

use App\Models\Appointment;
use App\Services\WhatsAppService;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class SendAppointmentReminders extends Command
{
    /**
     * Nombre del comando: php artisan appointments:send-reminders
     */
    protected $signature = 'appointments:send-reminders';

    protected $description = 'Envía recordatorios por WhatsApp a los pacientes con cita mañana';

    public function handle(WhatsAppService $whatsapp): int
    {
        $tomorrow = Carbon::tomorrow()->toDateString();

        // Buscar todas las citas programadas para mañana
        $appointments = Appointment::with(['patient.user', 'doctor.user'])
            ->where('date', $tomorrow)
            ->where('status', 'programado')
            ->get();

        if ($appointments->isEmpty()) {
            $this->info('No hay citas programadas para mañana.');
            return self::SUCCESS;
        }

        foreach ($appointments as $appointment) {
            try {
                $whatsapp->sendAppointmentReminder($appointment);
                $this->info("✅ Recordatorio enviado: {$appointment->patient->user->name} — {$tomorrow}");
            } catch (\Exception $e) {
                $this->error("❌ Error con cita ID {$appointment->id}: " . $e->getMessage());
                \Log::error('WhatsApp reminder failed for appointment ' . $appointment->id . ': ' . $e->getMessage());
            }
        }

        $this->info("Total enviados: {$appointments->count()}");
        return self::SUCCESS;
    }
}
