<?php

namespace Database\Seeders;

use App\Models\Speciality;
use Illuminate\Database\Seeder;

class SpecialitySeeder extends Seeder
{
    public function run(): void
    {
        $specialities = [
            'Medicina General', 'Cardiología', 'Dermatología', 'Pediatría',
            'Neurología', 'Ortopedia', 'Ginecología', 'Psiquiatría',
            'Oftalmología', 'Oncología',
        ];

        foreach ($specialities as $name) {
            Speciality::firstOrCreate(['name' => $name]);
        }
    }
}
