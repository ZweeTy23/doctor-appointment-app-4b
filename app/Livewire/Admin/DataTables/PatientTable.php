<?php

namespace App\Livewire\Admin\Datatables;

use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use App\Models\Patient;

class PatientTable extends DataTableComponent
{
    public function builder(): Builder
    {
        return Patient::query()->with(['user', 'bloodType']);
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
            Column::make("Teléfono", "user.phone")
                ->sortable(),
            Column::make("Tipo de Sangre", "bloodType.name")
                ->sortable(),
            Column::make("Acciones")
                ->label(function($row){
                    return view('admin.patients.actions', ['patient' => $row]);
                }),
        ];
    }
}
