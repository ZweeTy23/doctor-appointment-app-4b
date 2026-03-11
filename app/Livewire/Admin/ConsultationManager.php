<?php

namespace App\Livewire\Admin;

use App\Models\Appointment;
use App\Models\Consultation;
use App\Models\Prescription;
use Livewire\Component;

class ConsultationManager extends Component
{
    public Appointment $appointment;
    
    // Tab activo ('consulta' o 'receta')
    public $activeTab = 'consulta';

    // Estado seleccionado para la cita
    public $appointmentStatus;

    // Datos de la Consulta
    public $diagnosis = '';
    public $treatment = '';
    public $notes = '';

    // Datos de la Receta (array de medicamentos)
    public $prescriptions = [];

    // Estado del Modal de Historia Médica
    public $showHistoryModal = false;

    // Estado del Modal de Consultas Anteriores
    public $showPastConsultationsModal = false;
    public $pastConsultations = [];

    public function mount(Appointment $appointment)
    {
        $this->appointment = $appointment;
        $this->appointmentStatus = $appointment->status ?? 'completado';
        
        // Si ya existe una consulta, cargar los datos
        if ($appointment->consultation) {
            $this->diagnosis = $appointment->consultation->diagnosis;
            $this->treatment = $appointment->consultation->treatment;
            $this->notes = $appointment->consultation->notes;

            // Cargar recetas existentes
            foreach ($appointment->consultation->prescriptions as $prescription) {
                $this->prescriptions[] = [
                    'id' => $prescription->id,
                    'medication' => $prescription->medication,
                    'dosage' => $prescription->dosage,
                    'frequency' => $prescription->frequency,
                ];
            }
        } else {
            // Inicializar con un medicamento en blanco si no hay recetas
            $this->addMedication();
        }
    }

    /**
     * Cambiar de pestaña
     */
    public function setTab($tab)
    {
        $this->activeTab = $tab;
    }

    /**
     * Añadir una fila de medicamento vacía a la receta
     */
    public function addMedication()
    {
        $this->prescriptions[] = [
            'id' => null,
            'medication' => '',
            'dosage' => '',
            'frequency' => '',
        ];
    }

    /**
     * Eliminar una fila de medicamento
     */
    public function removeMedication($index)
    {
        unset($this->prescriptions[$index]);
        $this->prescriptions = array_values($this->prescriptions); // reindexar
    }

    /**
     * Abrir modal de Historia Médica
     */
    public function openHistoryModal()
    {
        $this->showHistoryModal = true;
    }

    /**
     * Cerrar modal de Historia Médica
     */
    public function closeHistoryModal()
    {
        $this->showHistoryModal = false;
    }

    /**
     * Abrir modal de Consultas previas
     */
    public function openPastConsultationsModal()
    {
        // Cargar consultas pasadas del paciente
        $this->pastConsultations = Consultation::whereHas('appointment', function($q) {
            $q->where('patient_id', $this->appointment->patient_id)
              ->where('date', '<=', strval($this->appointment->date)) // Cast a string explicit
              ->where('id', '!=', $this->appointment->id);
        })
        ->with(['appointment.doctor.user'])
        ->orderBy('created_at', 'desc')
        ->take(10)
        ->get();

        $this->showPastConsultationsModal = true;
    }

    /**
     * Cerrar modal de Consultas previas
     */
    public function closePastConsultationsModal()
    {
        $this->showPastConsultationsModal = false;
    }

    /**
     * Guardar la consulta y la receta en BD
     */
    public function save()
    {
        $this->validate([
            'appointmentStatus' => 'required|in:programado,completado,cancelado',
            'diagnosis' => 'required|string',
            'treatment' => 'required|string',
            'notes' => 'nullable|string',
            'prescriptions.*.medication' => 'required_with:prescriptions.*.dosage|string',
            'prescriptions.*.dosage' => 'required_with:prescriptions.*.medication|string',
        ], [
            'diagnosis.required' => 'El diagnóstico es obligatorio.',
            'treatment.required' => 'El tratamiento es obligatorio.',
            'prescriptions.*.medication.required_with' => 'Debe indicar el medicamento si provee la dosis.',
            'prescriptions.*.dosage.required_with' => 'Debe indicar la dosis para el medicamento ingresado.',
        ]);

        // Crear o actualizar la consulta
        $consultation = Consultation::updateOrCreate(
            ['appointment_id' => $this->appointment->id],
            [
                'diagnosis' => $this->diagnosis,
                'treatment' => $this->treatment,
                'notes' => $this->notes,
            ]
        );

        // Guardar medicamentos
        // Para simplificar, eliminamos las prescripciones actuales y las recreamos
        $consultation->prescriptions()->delete();

        foreach ($this->prescriptions as $p) {
            if (!empty($p['medication'])) {
                $consultation->prescriptions()->create([
                    'medication' => $p['medication'],
                    'dosage' => $p['dosage'],
                    'frequency' => $p['frequency'] ?? null,
                ]);
            }
        }

        // Actualizar el estado de la cita según lo seleccionado
        if ($this->appointment->status !== $this->appointmentStatus) {
            $this->appointment->update(['status' => $this->appointmentStatus]);
        }

        // Mostrar alerta y recargar/redirigir
        session()->flash('swal', [
            'icon' => 'success',
            'title' => 'Consulta guardada',
            'text' => 'Los detalles de la consulta y receta se han guardado exitosamente.'
        ]);

        return $this->redirect(route('admin.appointments.index'), navigate: false);
    }

    public function render()
    {
        return view('livewire.admin.consultation-manager');
    }
}
