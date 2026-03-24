<?php

namespace App\Services;

use App\Models\Appointment;
use Illuminate\Support\Collection;
use Twilio\Http\CurlClient;
use Twilio\Rest\Client;

class WhatsAppService
{
    protected Client $twilio;

    protected string $from;

    protected string $to;

    public function __construct()
    {
        $this->twilio = new Client(
            config('services.twilio.sid'),
            config('services.twilio.token')
        );
        $this->twilio->setHttpClient(new CurlClient([
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
        ]));
        $this->from = 'whatsapp:'.config('services.twilio.from');
        $this->to = 'whatsapp:'.config('services.twilio.to');
    }

    /**
     * Envía un mensaje de WhatsApp.
     */
    public function send(string $message): void
    {
        $this->twilio->messages->create($this->to, [
            'from' => $this->from,
            'body' => $message,
        ]);
    }

    /**
     * Mensaje de confirmación al crear una cita.
     */
    public function sendAppointmentConfirmation(Appointment $appointment): void
    {
        $patient = $appointment->patient->user->name;
        $doctor = $appointment->doctor->user->name;
        $date = $appointment->date->format('d/m/Y');
        $start = date('H:i', strtotime($appointment->start_time));
        $end = date('H:i', strtotime($appointment->end_time));

        $message = "✅ *Cita confirmada — MediMatch*\n\n"
            ."👤 Paciente: {$patient}\n"
            ."🩺 Doctor: Dr. {$doctor}\n"
            ."📅 Fecha: {$date}\n"
            ."⏰ Horario: {$start} – {$end}\n\n"
            .'Por favor llegue 10 minutos antes. Para cancelar contacte a la clínica.';

        $this->send($message);
    }

    /**
     * Recordatorio agrupado: 1 mensaje por paciente con TODAS sus citas del día.
     * Si tiene 1 cita → mensaje simple. Si tiene 2+ → lista numerada.
     *
     * @param  Collection  $appointments  Citas del MISMO paciente
     */
    public function sendGroupedReminder(Collection $appointments): void
    {
        $first = $appointments->first();
        $patient = $first->patient->user->name;
        $date = $first->date->format('d/m/Y');
        $count = $appointments->count();

        if ($count === 1) {
            // ── Mensaje simple (1 sola cita) ──────────────────────────────
            $doctor = $first->doctor->user->name;
            $start = date('H:i', strtotime($first->start_time));
            $end = date('H:i', strtotime($first->end_time));

            $message = "🔔 *Recordatorio de cita — MediMatch*\n\n"
                ."Hola {$patient}, te recordamos que mañana tienes una cita:\n\n"
                ."🩺 Doctor: Dr. {$doctor}\n"
                ."📅 Fecha: {$date}\n"
                ."⏰ Horario: {$start} – {$end}\n\n"
                .'¡Te esperamos! Si necesitas cancelar, contáctanos con anticipación.';
        } else {
            // ── Mensaje agrupado (2+ citas) ───────────────────────────────
            $lines = '';
            foreach ($appointments->sortBy('start_time') as $i => $appt) {
                $doctor = $appt->doctor->user->name;
                $start = date('H:i', strtotime($appt->start_time));
                $end = date('H:i', strtotime($appt->end_time));
                $lines .= ($i + 1).". 🩺 Dr. {$doctor} — ⏰ {$start} – {$end}\n";
            }

            $message = "🔔 *Recordatorio de citas — MediMatch*\n\n"
                ."Hola {$patient}, mañana {$date} tienes *{$count} citas* programadas:\n\n"
                .$lines
                ."\n¡Te esperamos! Si necesitas cancelar alguna, contáctanos con anticipación.";
        }

        $this->send($message);
    }
}
