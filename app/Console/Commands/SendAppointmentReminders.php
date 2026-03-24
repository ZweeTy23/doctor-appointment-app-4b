<?php

namespace App\Console\Commands;

use App\Models\Appointment;
use App\Services\WhatsAppService;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class SendAppointmentReminders extends Command
{
    protected $signature = 'appointments:send-reminders';

    protected $description = 'Envía recordatorios por WhatsApp a los pacientes con cita mañana (agrupados por paciente)';

    public function handle(WhatsAppService $whatsapp): int
    {
        $tomorrow = Carbon::tomorrow()->toDateString();

        // Traer todas las citas programadas para mañana
        $appointments = Appointment::with(['patient.user', 'doctor.user'])
            ->where('date', $tomorrow)
            ->where('status', 'programado')
            ->get();

        if ($appointments->isEmpty()) {
            $this->info('No hay citas programadas para mañana.');

            return self::SUCCESS;
        }

        // Agrupar por patient_id → 1 mensaje por paciente aunque tenga varias citas
        $grouped = $appointments->groupBy('patient_id');
        $sent = 0;

        foreach ($grouped as $patientId => $patientAppointments) {
            $patientName = $patientAppointments->first()->patient->user->name;
            $count = $patientAppointments->count();

            try {
                $whatsapp->sendGroupedReminder($patientAppointments);

                $label = $count > 1 ? "{$count} citas" : '1 cita';
                $this->info("✅ Recordatorio enviado a {$patientName} ({$label}) — {$tomorrow}");
                $sent++;
            } catch (\Exception $e) {
                $this->error("❌ Error con paciente ID {$patientId}: ".$e->getMessage());
                \Log::error("WhatsApp reminder failed for patient {$patientId}: ".$e->getMessage());
            }
        }

        $this->info("Mensajes enviados: {$sent} / {$grouped->count()} pacientes.");

        return self::SUCCESS;
    }
}
