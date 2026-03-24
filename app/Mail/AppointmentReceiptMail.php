<?php

namespace App\Mail;

use App\Models\Appointment;
use App\Services\AppointmentPdfService;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class AppointmentReceiptMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @param  Collection<int, Appointment>|null  $doctorDayAppointments  Citas del mismo doctor en la misma fecha (solo rol doctor).
     */
    public function __construct(
        public Appointment $appointment,
        public string $recipientRole,
        public ?Collection $doctorDayAppointments = null,
    ) {}

    public function envelope(): Envelope
    {
        if ($this->recipientRole === 'patient') {
            return new Envelope(
                subject: __('Tu cita fue registrada').' — '.config('app.name'),
            );
        }

        return new Envelope(
            subject: __('Agenda del día').' ('.$this->appointment->date->format('d/m/Y').') — '.config('app.name'),
        );
    }

    public function content(): Content
    {
        $view = $this->recipientRole === 'patient'
            ? 'mail.appointment-receipt-patient'
            : 'mail.appointment-receipt-doctor';

        return new Content(
            view: $view,
            with: [
                'appointment' => $this->appointment,
                'doctorDayAppointments' => $this->doctorDayAppointments ?? collect(),
            ],
        );
    }

    /**
     * @return array<int, Attachment>
     */
    public function attachments(): array
    {
        if ($this->recipientRole !== 'patient') {
            return [];
        }

        $binary = app(AppointmentPdfService::class)->render($this->appointment);

        return [
            Attachment::fromData(fn () => $binary, 'comprobante-cita-'.$this->appointment->id.'.pdf')
                ->withMime('application/pdf'),
        ];
    }
}
