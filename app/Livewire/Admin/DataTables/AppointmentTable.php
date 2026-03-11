<?php

namespace App\Livewire\Admin\Datatables;

use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use App\Models\Appointment;

class AppointmentTable extends DataTableComponent
{
    public function builder(): Builder
    {
        return Appointment::query()->with(['patient.user', 'doctor.user']);
    }

    public function configure(): void
    {
        $this->setPrimaryKey('id');
    }

    public function columns(): array
    {
        return [
            Column::make("Id", "id")
                ->sortable(),

            Column::make("Paciente", "patient.user.name")
                ->sortable()
                ->searchable(),

            Column::make("Doctor", "doctor.user.name")
                ->sortable()
                ->searchable(),

            Column::make("Fecha", "date")
                ->sortable()
                ->format(fn ($value) => $value->format('d/m/Y')),

            Column::make("Hora", "start_time")
                ->sortable()
                ->format(fn ($value) => date('H:i', strtotime($value))),

            Column::make("Hora Fin", "end_time")
                ->sortable()
                ->format(fn ($value) => date('H:i', strtotime($value))),

            Column::make("Estado", "status")
                ->sortable()
                ->format(function ($value) {
                    $colors = [
                        'programado' => 'bg-blue-100 text-blue-800',
                        'completado' => 'bg-green-100 text-green-800',
                        'cancelado'  => 'bg-red-100 text-red-800',
                    ];
                    $labels = [
                        'programado' => 'Programado',
                        'completado' => 'Completado',
                        'cancelado'  => 'Cancelado',
                    ];
                    $class = $colors[$value] ?? 'bg-gray-100 text-gray-800';
                    $label = $labels[$value] ?? $value;
                    return '<span class="px-2 py-1 rounded-full text-xs font-semibold ' . $class . '">' . $label . '</span>';
                })
                ->html(),

            Column::make("Acciones")
                ->label(fn ($row) => view('admin.appointments.actions', ['appointment' => $row])),
        ];
    }
}
