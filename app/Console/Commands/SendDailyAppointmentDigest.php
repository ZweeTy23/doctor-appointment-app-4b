<?php

namespace App\Console\Commands;

use App\Mail\DailyAppointmentsAdminMail;
use App\Mail\DailyAppointmentsDoctorMail;
use App\Models\Appointment;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendDailyAppointmentDigest extends Command
{
    protected $signature = 'appointments:send-daily-digest';

    protected $description = 'Envía por correo el reporte de citas del día al administrador y a cada doctor';

    public function handle(): int
    {
        $dateString = now()->toDateString();

        $appointments = Appointment::query()
            ->with(['patient.user', 'doctor.user', 'doctor.speciality'])
            ->whereDate('date', $dateString)
            ->where('status', 'programado')
            ->orderBy('start_time')
            ->get();

        $digestEmail = config('services.admin.digest_email');
        if (is_string($digestEmail) && filter_var($digestEmail, FILTER_VALIDATE_EMAIL)) {
            try {
                Mail::to($digestEmail)->send(new DailyAppointmentsAdminMail($appointments, $dateString));
                $this->info("Reporte de administrador enviado a {$digestEmail}.");
            } catch (\Throwable $e) {
                $this->error('Error al enviar correo al administrador: '.$e->getMessage());
                \Log::error('Daily digest admin mail failed: '.$e->getMessage());
            }
        } else {
            $this->warn('ADMIN_DIGEST_EMAIL / ADMIN_EMAIL no está configurado o no es válido en .env.');
        }

        $sentDoctors = 0;
        foreach ($appointments->groupBy('doctor_id') as $group) {
            /** @var Appointment $first */
            $first = $group->first();
            $doctor = $first->doctor;
            $doctorEmail = $doctor->user->email ?? null;

            if (! is_string($doctorEmail) || ! filter_var($doctorEmail, FILTER_VALIDATE_EMAIL)) {
                $this->warn("Doctor ID {$doctor->id} sin correo válido; se omite.");

                continue;
            }

            try {
                Mail::to($doctorEmail)->send(new DailyAppointmentsDoctorMail($doctor, $group, $dateString));
                $sentDoctors++;
                $this->info("Reporte enviado al doctor {$doctor->user->name} ({$doctorEmail}).");
            } catch (\Throwable $e) {
                $this->error("Error al enviar al doctor {$doctor->id}: ".$e->getMessage());
                \Log::error("Daily digest doctor mail failed (doctor {$doctor->id}): ".$e->getMessage());
            }
        }

        if ($appointments->isEmpty()) {
            $this->info('No hay citas programadas para hoy; los correos reflejan listas vacías donde aplica.');
        }

        $this->info("Doctores notificados: {$sentDoctors}.");

        return self::SUCCESS;
    }
}
