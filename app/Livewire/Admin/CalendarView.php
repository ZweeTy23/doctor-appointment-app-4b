<?php

namespace App\Livewire\Admin;

use App\Models\Appointment;
use App\Models\Availability;
use App\Models\Doctor;
use Carbon\Carbon;
use Livewire\Component;

class CalendarView extends Component
{
    public $selectedDoctorId;
    public $currentMonth;
    public $currentYear;
    public $selectedDate = null;
    public $dayDetail = [];

    public function mount()
    {
        $this->currentMonth = now()->month;
        $this->currentYear = now()->year;

        // Seleccionar el primer doctor disponible
        $firstDoctor = Doctor::with('user')->first();
        $this->selectedDoctorId = $firstDoctor?->id;
    }

    /**
     * Cambiar doctor seleccionado.
     */
    public function updatedSelectedDoctorId()
    {
        $this->selectedDate = null;
        $this->dayDetail = [];
    }

    /**
     * Navegar al mes anterior.
     */
    public function previousMonth()
    {
        $date = Carbon::create($this->currentYear, $this->currentMonth, 1)->subMonth();
        $this->currentMonth = $date->month;
        $this->currentYear = $date->year;
        $this->selectedDate = null;
        $this->dayDetail = [];
    }

    /**
     * Navegar al mes siguiente.
     */
    public function nextMonth()
    {
        $date = Carbon::create($this->currentYear, $this->currentMonth, 1)->addMonth();
        $this->currentMonth = $date->month;
        $this->currentYear = $date->year;
        $this->selectedDate = null;
        $this->dayDetail = [];
    }

    /**
     * Seleccionar un día para ver el detalle de horas.
     */
    public function selectDay($date)
    {
        $this->selectedDate = $date;
        $this->loadDayDetail($date);
    }

    /**
     * Cargar el detalle de horas para un día específico.
     */
    private function loadDayDetail($date)
    {
        $carbonDate = Carbon::parse($date);
        $dayOfWeek = $carbonDate->dayOfWeekIso - 1; // 0=Lunes, 6=Domingo

        // Obtener disponibilidad del doctor para ese día de la semana
        $availabilities = Availability::where('doctor_id', $this->selectedDoctorId)
            ->where('day_of_week', $dayOfWeek)
            ->where('is_active', true)
            ->orderBy('start_time')
            ->get();

        // Obtener citas del doctor para ese día
        $appointments = Appointment::where('doctor_id', $this->selectedDoctorId)
            ->where('date', $date)
            ->where('status', '!=', 'cancelado')
            ->with('patient.user')
            ->orderBy('start_time')
            ->get();

        $this->dayDetail = [];

        // Generar horas de 08:00 a 17:00
        for ($h = 8; $h < 17; $h++) {
            $hourStart = sprintf('%02d:00:00', $h);
            $hourEnd = sprintf('%02d:00:00', $h + 1);

            // ¿Tiene disponibilidad en esta hora?
            $isAvailable = $availabilities->contains(function ($avail) use ($hourStart) {
                return $avail->start_time == $hourStart;
            });

            // ¿Tiene cita en esta hora?
            $appointment = $appointments->first(function ($appt) use ($hourStart, $hourEnd) {
                return $appt->start_time < $hourEnd && $appt->end_time > $hourStart;
            });

            $status = 'no_disponible'; // rojo
            $label = 'No disponible';
            $patientName = null;

            if ($isAvailable && $appointment) {
                $status = 'ocupado'; // gris
                $label = 'Ocupado';
                $patientName = $appointment->patient->user->name ?? 'Paciente';
            } elseif ($isAvailable) {
                $status = 'disponible'; // verde
                $label = 'Disponible';
            }

            $this->dayDetail[] = [
                'hour'        => sprintf('%02d:00 - %02d:00', $h, $h + 1),
                'status'      => $status,
                'label'       => $label,
                'patientName' => $patientName,
            ];
        }
    }

    /**
     * Obtener el estado de un día completo del mes.
     */
    public function getDayStatus($date)
    {
        $carbonDate = Carbon::parse($date);
        $dayOfWeek = $carbonDate->dayOfWeekIso - 1;

        // Contar bloques disponibles para ese día de la semana
        $totalAvailable = Availability::where('doctor_id', $this->selectedDoctorId)
            ->where('day_of_week', $dayOfWeek)
            ->where('is_active', true)
            ->count();

        if ($totalAvailable === 0) {
            return 'no_disponible'; // rojo: sin disponibilidad
        }

        // Contar citas en ese día
        $totalAppointments = Appointment::where('doctor_id', $this->selectedDoctorId)
            ->where('date', $date)
            ->where('status', '!=', 'cancelado')
            ->count();

        if ($totalAppointments >= $totalAvailable) {
            return 'ocupado'; // gris: todo lleno
        }

        if ($totalAppointments > 0) {
            return 'parcial'; // naranja: algo disponible
        }

        return 'disponible'; // verde: todo libre
    }

    public function render()
    {
        $doctors = Doctor::with(['user', 'speciality'])->get();

        // Generar datos del calendario
        $firstDay = Carbon::create($this->currentYear, $this->currentMonth, 1);
        $daysInMonth = $firstDay->daysInMonth;

        // Día de la semana en el que empieza el mes (1=Lunes, 7=Domingo)
        $startDayOfWeek = $firstDay->dayOfWeekIso;

        // Generar array de días con su estado
        $calendarDays = [];

        // Espacios vacíos antes del primer día
        for ($i = 1; $i < $startDayOfWeek; $i++) {
            $calendarDays[] = ['day' => null, 'date' => null, 'status' => null];
        }

        // Días del mes
        for ($d = 1; $d <= $daysInMonth; $d++) {
            $date = Carbon::create($this->currentYear, $this->currentMonth, $d)->format('Y-m-d');
            $calendarDays[] = [
                'day'    => $d,
                'date'   => $date,
                'status' => $this->getDayStatus($date),
                'isToday' => $date === now()->format('Y-m-d'),
            ];
        }

        $monthName = $firstDay->locale('es')->isoFormat('MMMM YYYY');

        $selectedDoctor = $this->selectedDoctorId
            ? Doctor::with(['user', 'speciality'])->find($this->selectedDoctorId)
            : null;

        return view('livewire.admin.calendar-view', compact(
            'doctors',
            'calendarDays',
            'monthName',
            'selectedDoctor'
        ));
    }
}
