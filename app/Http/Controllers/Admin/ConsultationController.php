<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Appointment;

class ConsultationController extends Controller
{
    /**
     * Mostrar la vista de consulta médica para una cita específica.
     */
    public function show(Appointment $appointment)
    {
        // Solo permitir ver consultas de citas que no estén canceladas
        if ($appointment->status === 'cancelado') {
            return redirect()->route('admin.appointments.index')
                ->with('swal', [
                    'icon' => 'error',
                    'title' => 'Cita Cancelada',
                    'text' => 'No se puede consultar una cita cancelada.'
                ]);
        }

        // Cargar relaciones necesarias
        $appointment->load(['patient.user', 'doctor.user', 'consultation.prescriptions']);

        return view('admin.consultations.show', compact('appointment'));
    }
}
