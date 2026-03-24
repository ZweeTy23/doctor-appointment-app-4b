<?php

namespace App\Mail;

use App\Models\Appointment;
use App\Models\Doctor;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class DailyAppointmentsDoctorMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @param  Collection<int, Appointment>  $appointments
     */
    public function __construct(
        public Doctor $doctor,
        public Collection $appointments,
        public string $date,
    ) {}

    public function envelope(): Envelope
    {
        $formatted = Carbon::parse($this->date)->format('d/m/Y');

        if ($this->appointments->isEmpty()) {
            return new Envelope(
                subject: __('Tu agenda del día').' — '.__('sin citas').' ('.$formatted.') — '.config('app.name'),
            );
        }

        $n = $this->appointments->count();

        return new Envelope(
            subject: __('Tus citas del día')." ({$formatted}) — {$n} ".__('cita(s)').' — '.config('app.name'),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'mail.daily-appointments-doctor',
            with: [
                'doctor' => $this->doctor,
                'appointments' => $this->appointments,
                'date' => $this->date,
            ],
        );
    }
}
