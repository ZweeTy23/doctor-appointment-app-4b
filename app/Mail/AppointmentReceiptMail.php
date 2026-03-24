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

class AppointmentReceiptMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Appointment $appointment,
        public string $recipientRole,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: __('Comprobante de cita médica').' — '.config('app.name'),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'mail.appointment-receipt',
            with: [
                'appointment' => $this->appointment,
                'recipientRole' => $this->recipientRole,
            ],
        );
    }

    /**
     * @return array<int, Attachment>
     */
    public function attachments(): array
    {
        $binary = app(AppointmentPdfService::class)->render($this->appointment);

        return [
            Attachment::fromData(fn () => $binary, 'comprobante-cita-'.$this->appointment->id.'.pdf')
                ->withMime('application/pdf'),
        ];
    }
}
