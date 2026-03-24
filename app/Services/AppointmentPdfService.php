<?php

namespace App\Services;

use App\Models\Appointment;
use Barryvdh\DomPDF\Facade\Pdf;

class AppointmentPdfService
{
    /**
     * Render a PDF receipt for the given appointment (binary string).
     */
    public function render(Appointment $appointment): string
    {
        $appointment->loadMissing(['patient.user', 'doctor.user', 'doctor.speciality']);

        return Pdf::loadView('pdf.appointment-receipt', [
            'appointment' => $appointment,
        ])->output();
    }
}
