<?php

namespace App\Console\Commands;

use App\Mail\DailyAppointmentsAdminMail;
use App\Mail\DailyAppointmentsDoctorMail;
use App\Models\Appointment;
use App\Models\Doctor;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendDailyAppointmentDigest extends Command
{
    protected $signature = 'appointments:send-daily-digest';

    protected $description = 'Envía por correo el reporte de citas del día al administrador y a cada doctor (incluye médicos sin citas)';

    public function handle(): int
    {
        $dateString = now()->toDateString();

        $appointments = Appointment::query()
            ->with(['patient.user', 'doctor.user', 'doctor.speciality'])
            ->whereDate('date', $dateString)
            ->where('status', 'programado')
            ->orderBy('start_time')
            ->get();

        $allDoctors = Doctor::query()
            ->with(['user', 'speciality'])
            ->get()
            ->sortBy(fn (Doctor $d) => mb_strtolower($d->user->name ?? ''))
            ->values();

        $digestEmail = config('services.admin.digest_email');
        if (is_string($digestEmail) && filter_var($digestEmail, FILTER_VALIDATE_EMAIL)) {
            try {
                Mail::to($digestEmail)->send(new DailyAppointmentsAdminMail($appointments, $allDoctors, $dateString));
                $this->info("Reporte de administrador enviado a {$digestEmail}.");
            } catch (\Throwable $e) {
                $this->error('Error al enviar correo al administrador: '.$e->getMessage());
                \Log::error('Daily digest admin mail failed: '.$e->getMessage());
            }
        } else {
            $this->warn('ADMIN_DIGEST_EMAIL / ADMIN_EMAIL no está configurado o no es válido en .env.');
        }

        $sentDoctors = 0;
        foreach ($allDoctors as $doctor) {
            $doctorEmail = $doctor->user->email ?? null;

            if (! is_string($doctorEmail) || ! filter_var($doctorEmail, FILTER_VALIDATE_EMAIL)) {
                $this->warn("Doctor ID {$doctor->id} sin correo válido; se omite.");

                continue;
            }

            $group = $appointments->where('doctor_id', $doctor->id)->values();

            try {
                Mail::to($doctorEmail)->send(new DailyAppointmentsDoctorMail($doctor, $group, $dateString));
                $sentDoctors++;
                $label = $group->isEmpty() ? 'sin citas' : $group->count().' cita(s)';
                $this->info("Correo enviado a {$doctor->user->name} ({$doctorEmail}) — {$label}.");
            } catch (\Throwable $e) {
                $this->error("Error al enviar al doctor {$doctor->id}: ".$e->getMessage());
                \Log::error("Daily digest doctor mail failed (doctor {$doctor->id}): ".$e->getMessage());
            }
        }

        if ($allDoctors->isEmpty()) {
            $this->info('No hay médicos registrados en el sistema.');
        }

        $this->info("Correos a médicos enviados: {$sentDoctors} / {$allDoctors->count()}.");

        return self::SUCCESS;
    }
}
