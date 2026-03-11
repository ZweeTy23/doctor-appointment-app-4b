<?php

namespace App\Livewire\Admin;

use App\Models\Availability;
use App\Models\Doctor;
use Livewire\Component;

class ScheduleManager extends Component
{
    public $doctorId;
    public $selectedSlots = [];

    /**
     * Días de la semana (0=Lunes … 4=Viernes).
     */
    public $days = [
        0 => 'Lunes',
        1 => 'Martes',
        2 => 'Miércoles',
        3 => 'Jueves',
        4 => 'Viernes',
    ];

    /**
     * Horas disponibles: 08:00 a 17:00 (bloques de 1 hora).
     */
    public $hours = [];

    public function mount($doctorId)
    {
        $this->doctorId = $doctorId;
        $this->generateHours();
        $this->loadExistingSchedule();
    }

    /**
     * Generar las horas de 08:00 a 17:00.
     */
    private function generateHours()
    {
        for ($h = 8; $h < 17; $h++) {
            $this->hours[] = sprintf('%02d:00', $h);
        }
    }

    /**
     * Cargar la disponibilidad existente del doctor.
     */
    private function loadExistingSchedule()
    {
        $availabilities = Availability::where('doctor_id', $this->doctorId)
            ->where('is_active', true)
            ->get();

        foreach ($availabilities as $avail) {
            $key = $avail->day_of_week . '_' . date('H:i', strtotime($avail->start_time));
            $this->selectedSlots[$key] = true;
        }
    }

    /**
     * Toggle un bloque individual de 1 hora.
     */
    public function toggleSlot($day, $hour)
    {
        $key = $day . '_' . $hour;
        if (isset($this->selectedSlots[$key])) {
            unset($this->selectedSlots[$key]);
        } else {
            $this->selectedSlots[$key] = true;
        }
    }

    /**
     * Toggle todos los bloques de un día completo.
     */
    public function toggleDay($day)
    {
        $allSelected = true;
        foreach ($this->hours as $hour) {
            if (!isset($this->selectedSlots[$day . '_' . $hour])) {
                $allSelected = false;
                break;
            }
        }

        foreach ($this->hours as $hour) {
            $key = $day . '_' . $hour;
            if ($allSelected) {
                unset($this->selectedSlots[$key]);
            } else {
                $this->selectedSlots[$key] = true;
            }
        }
    }

    /**
     * Guardar la disponibilidad en la base de datos.
     */
    public function save()
    {
        // Eliminar toda la disponibilidad actual del doctor
        Availability::where('doctor_id', $this->doctorId)->delete();

        // Insertar los bloques seleccionados
        foreach ($this->selectedSlots as $key => $value) {
            if (!$value) continue;

            [$day, $hour] = explode('_', $key, 2);
            $startTime = $hour . ':00';
            $h = (int) substr($hour, 0, 2);
            $endTime = sprintf('%02d:00:00', $h + 1);

            Availability::create([
                'doctor_id'   => $this->doctorId,
                'day_of_week' => (int) $day,
                'start_time'  => $startTime,
                'end_time'    => $endTime,
                'is_active'   => true,
            ]);
        }

        // Redirigir al calendario con mensaje de éxito
        session()->flash('swal', [
            'icon'  => 'success',
            'title' => 'Horario guardado',
            'text'  => 'La disponibilidad ha sido actualizada correctamente.',
        ]);

        return $this->redirect(route('admin.calendar.index'), navigate: false);
    }

    public function render()
    {
        return view('livewire.admin.schedule-manager');
    }
}
