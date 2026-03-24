<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\AppointmentReceiptMail;
use App\Models\Appointment;
use App\Models\Availability;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Speciality;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class AppointmentController extends Controller
{
    /**
     * Listar todas las citas médicas.
     */
    public function index()
    {
        return view('admin.appointments.index');
    }

    /**
     * Formulario para crear una nueva cita.
     * Flujo: Especialidad → Doctores → Horarios disponibles.
     */
    public function create(Request $request)
    {
        $specialities = Speciality::has('doctors')->get();
        $patients = Patient::with('user')->get();
        $availableDoctors = collect();
        $availableSlots = [];

        // Paso 1: Si se selecciona especialidad, mostrar doctores
        if ($request->filled('speciality_id')) {
            $availableDoctors = Doctor::with(['user', 'speciality'])
                ->where('speciality_id', $request->speciality_id)
                ->get();
        }

        // Paso 2: Si se selecciona doctor y fecha, mostrar horarios disponibles
        if ($request->filled(['doctor_id', 'date'])) {
            $date = $request->date;
            $doctorId = $request->doctor_id;
            $dayOfWeek = (date('N', strtotime($date)) - 1); // 0=Lunes, 6=Domingo

            // Obtener bloques de disponibilidad del doctor para ese día
            $availabilities = Availability::where('doctor_id', $doctorId)
                ->where('day_of_week', $dayOfWeek)
                ->where('is_active', true)
                ->orderBy('start_time')
                ->get();

            // Obtener citas existentes del doctor en esa fecha
            $existingAppointments = Appointment::where('doctor_id', $doctorId)
                ->where('date', $date)
                ->where('status', '!=', 'cancelado')
                ->get();

            foreach ($availabilities as $avail) {
                // Verificar si ya hay una cita en este bloque
                $isOccupied = $existingAppointments->contains(function ($appt) use ($avail) {
                    return $appt->start_time < $avail->end_time && $appt->end_time > $avail->start_time;
                });

                if (! $isOccupied) {
                    $availableSlots[] = [
                        'start' => date('H:i', strtotime($avail->start_time)),
                        'end' => date('H:i', strtotime($avail->end_time)),
                        'label' => date('H:i', strtotime($avail->start_time)).' - '.date('H:i', strtotime($avail->end_time)),
                    ];
                }
            }
        }

        return view('admin.appointments.create', compact(
            'specialities',
            'patients',
            'availableDoctors',
            'availableSlots'
        ));
    }

    /**
     * Almacenar una nueva cita (1 hora).
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'doctor_id' => 'required|exists:doctors,id',
            'date' => 'required|date|after_or_equal:today',
            'start_time' => 'required|date_format:H:i',
            'reason' => 'nullable|string',
        ]);

        $startTime = $data['start_time'].':00';
        $h = (int) substr($data['start_time'], 0, 2);
        $endTime = sprintf('%02d:00:00', $h + 1);
        $date = $data['date'];
        $doctorId = $data['doctor_id'];

        // ─── Validación 1: Disponibilidad del doctor ───
        $dayOfWeek = (date('N', strtotime($date)) - 1);
        $hasAvailability = Availability::where('doctor_id', $doctorId)
            ->where('day_of_week', $dayOfWeek)
            ->where('is_active', true)
            ->where('start_time', '<=', $startTime)
            ->where('end_time', '>=', $endTime)
            ->exists();

        if (! $hasAvailability) {
            return redirect()->back()
                ->withInput()
                ->with('swal', [
                    'icon' => 'error',
                    'title' => 'Sin disponibilidad',
                    'text' => 'El doctor no tiene disponibilidad en ese horario.',
                ]);
        }

        // ─── Validación 2: Conflicto de citas (overlap) ───
        $conflict = Appointment::where('doctor_id', $doctorId)
            ->where('date', $date)
            ->where('status', '!=', 'cancelado')
            ->where(function ($query) use ($startTime, $endTime) {
                $query->where('start_time', '<', $endTime)
                    ->where('end_time', '>', $startTime);
            })
            ->exists();

        if ($conflict) {
            return redirect()->back()
                ->withInput()
                ->with('swal', [
                    'icon' => 'error',
                    'title' => 'Conflicto de horario',
                    'text' => 'El doctor ya tiene una cita programada en ese rango de tiempo.',
                ]);
        }

        // ─── Crear la cita ───
        $appointment = Appointment::create([
            'patient_id' => $data['patient_id'],
            'doctor_id' => $doctorId,
            'date' => $date,
            'start_time' => $startTime,
            'end_time' => $endTime,
            'status' => 'programado',
            'reason' => $data['reason'] ?? null,
        ]);

        // ─── Confirmación WhatsApp + comprobante PDF por correo ───
        $appointment->load(['patient.user', 'doctor.user']);

        try {
            app(WhatsAppService::class)->sendAppointmentConfirmation($appointment);
        } catch (\Exception $e) {
            \Log::error('WhatsApp confirmation failed: '.$e->getMessage());
        }

        try {
            $patientEmail = $appointment->patient->user->email ?? null;
            $doctorEmail = $appointment->doctor->user->email ?? null;

            if (is_string($patientEmail) && filter_var($patientEmail, FILTER_VALIDATE_EMAIL)) {
                Mail::to($patientEmail)->send(new AppointmentReceiptMail($appointment, 'patient'));
            }
            if (is_string($doctorEmail) && filter_var($doctorEmail, FILTER_VALIDATE_EMAIL)) {
                Mail::to($doctorEmail)->send(new AppointmentReceiptMail($appointment, 'doctor'));
            }
        } catch (\Throwable $e) {
            \Log::error('Appointment receipt email failed: '.$e->getMessage());
        }

        return redirect()
            ->route('admin.appointments.index')
            ->with('swal', [
                'icon' => 'success',
                'title' => 'Cita programada',
                'text' => 'La cita se guardó. Se envió confirmación por WhatsApp (si aplica) y el comprobante PDF por correo al paciente y al doctor.',
            ]);
    }

    /**
     * Formulario para editar una cita.
     */
    public function edit(Appointment $appointment)
    {
        $appointment->load(['patient.user', 'doctor.user']);

        return view('admin.appointments.edit', compact('appointment'));
    }

    /**
     * Actualizar una cita existente.
     */
    public function update(Request $request, Appointment $appointment)
    {
        $data = $request->validate([
            'status' => 'required|in:programado,cancelado,completado',
            'date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
        ]);

        $startTime = $data['start_time'].':00';
        $h = (int) substr($data['start_time'], 0, 2);
        $endTime = sprintf('%02d:00:00', $h + 1);

        // Validar conflictos solo si no se está cancelando
        if ($data['status'] !== 'cancelado') {
            $conflict = Appointment::where('doctor_id', $appointment->doctor_id)
                ->where('date', $data['date'])
                ->where('id', '!=', $appointment->id)
                ->where('status', '!=', 'cancelado')
                ->where(function ($query) use ($startTime, $endTime) {
                    $query->where('start_time', '<', $endTime)
                        ->where('end_time', '>', $startTime);
                })
                ->exists();

            if ($conflict) {
                return redirect()->back()
                    ->withInput()
                    ->with('swal', [
                        'icon' => 'error',
                        'title' => 'Conflicto de horario',
                        'text' => 'Ya existe otra cita en ese rango de tiempo.',
                    ]);
            }
        }

        $appointment->update([
            'status' => $data['status'],
            'date' => $data['date'],
            'start_time' => $startTime,
            'end_time' => $endTime,
        ]);

        return redirect()
            ->route('admin.appointments.index')
            ->with('swal', [
                'icon' => 'success',
                'title' => 'Cita actualizada',
                'text' => 'La cita ha sido actualizada correctamente.',
            ]);
    }

    /**
     * Eliminar una cita.
     */
    public function destroy(Appointment $appointment)
    {
        $appointment->delete();

        return redirect()
            ->route('admin.appointments.index')
            ->with('swal', [
                'icon' => 'success',
                'title' => 'Cita eliminada',
                'text' => 'La cita ha sido eliminada.',
            ]);
    }
}
