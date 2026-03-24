<?php

namespace App\Mail;

use App\Models\Appointment;
use App\Models\Doctor;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class DailyAppointmentsAdminMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @param  Collection<int, Appointment>  $appointments
     * @param  Collection<int, Doctor>  $doctors
     */
    public function __construct(
        public Collection $appointments,
        public Collection $doctors,
        public string $date,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: __('Reporte del día — resumen por médico').' ('.$this->date.')',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'mail.daily-appointments-admin',
            with: [
                'appointments' => $this->appointments,
                'doctors' => $this->doctors,
                'date' => $this->date,
            ],
        );
    }
}
