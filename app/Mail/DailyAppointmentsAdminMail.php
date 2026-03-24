<?php

namespace App\Mail;

use App\Models\Appointment;
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
     */
    public function __construct(
        public Collection $appointments,
        public string $date,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: __('Reporte diario de citas').' — '.$this->date,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'mail.daily-appointments-admin',
            with: [
                'appointments' => $this->appointments,
                'date' => $this->date,
            ],
        );
    }
}
