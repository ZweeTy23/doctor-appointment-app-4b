<?php

namespace App\Console\Commands;

use App\Mail\AppointmentReceiptMail;
use App\Models\Appointment;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class ResendAppointmentReceiptCommand extends Command
{
    protected $signature = 'appointments:resend-receipt {id? : ID de la cita (por defecto: la más reciente)}';

    protected $description = 'Reenvía los dos correos de comprobante (paciente con PDF + agenda doctor) al correo del administrador';

    public function handle(): int
    {
        $adminEmail = config('services.admin.email');
        if (! is_string($adminEmail) || ! filter_var($adminEmail, FILTER_VALIDATE_EMAIL)) {
            $this->error('Configura ADMIN_EMAIL en .env con un correo válido.');

            return self::FAILURE;
        }

        $id = $this->argument('id');
        if ($id !== null) {
            $appointment = Appointment::query()->find($id);
            if ($appointment === null) {
                $this->error("No existe la cita con ID {$id}.");

                return self::FAILURE;
            }
        } else {
            $appointment = Appointment::query()->latest('id')->first();
            if ($appointment === null) {
                $this->error('No hay citas en la base de datos.');

                return self::FAILURE;
            }
        }

        $appointment->load(['patient.user', 'doctor.user']);

        $doctorDayAppointments = Appointment::query()
            ->with(['patient.user'])
            ->where('doctor_id', $appointment->doctor_id)
            ->whereDate('date', $appointment->date)
            ->where('status', 'programado')
            ->orderBy('start_time')
            ->get();

        Mail::to($adminEmail)->send(new AppointmentReceiptMail($appointment, 'patient', $doctorDayAppointments));
        Mail::to($adminEmail)->send(new AppointmentReceiptMail($appointment, 'doctor', $doctorDayAppointments));

        $this->info("Correos de prueba enviados a {$adminEmail} (cita #{$appointment->id}).");

        return self::SUCCESS;
    }
}
