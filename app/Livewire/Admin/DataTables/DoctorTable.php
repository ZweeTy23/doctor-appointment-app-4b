<?php

namespace App\Livewire\Admin\Datatables;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use App\Models\Doctor;

class DoctorTable extends DataTableComponent
{
    public function builder(): Builder
    {
        return Doctor::query()
            ->with('user')
            ->leftJoin('specialities', 'doctors.speciality_id', '=', 'specialities.id')
            ->select('doctors.*', 'specialities.name as speciality_name');
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

            Column::make("Nombre", "user.name")
                ->sortable()
                ->searchable(),

            Column::make("Email", "user.email")
                ->sortable()
                ->searchable(),

            Column::make("Especialidad", "speciality_name")
                ->sortable()
                ->label(fn($row) => $row->speciality_name ?? 'N/A'),

            Column::make("Licencia", "medical_license_number")
                ->sortable()
                ->label(fn($row) => $row->medical_license_number ?? 'N/A'),

            Column::make("Biografía", "biography")
                ->label(fn($row) => $row->biography ? Str::limit($row->biography, 60) : 'N/A'),

            Column::make("Acciones")
                ->label(fn($row) => view('admin.doctors.actions', ['doctor' => $row])),
        ];
    }
}
