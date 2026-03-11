<?php

namespace Database\Seeders;

use App\Models\Availability;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Speciality;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class DoctorAvailabilitySeeder extends Seeder
{
    /**
     * Crear 2 doctores por cada especialidad con disponibilidad pre-configurada,
     * y algunos pacientes de ejemplo.
     */
    public function run(): void
    {
        // Asegurar que el rol "Doctor" y "Paciente" existan
        $doctorRole = Role::firstOrCreate(['name' => 'Doctor']);
        $patientRole = Role::firstOrCreate(['name' => 'Paciente']);

        $specialities = Speciality::all();

        // Nombres de doctores ficticios
        $doctorNames = [
            'Dr. Carlos Pérez', 'Dra. María González',
            'Dr. Luis Torres', 'Dra. Ana Martínez',
            'Dr. Roberto Sánchez', 'Dra. Laura Ramírez',
            'Dr. Fernando López', 'Dra. Patricia Díaz',
            'Dr. Miguel Hernández', 'Dra. Sofía Vega',
            'Dr. Andrés Morales', 'Dra. Claudia Ruiz',
            'Dr. Diego Castro', 'Dra. Valeria Flores',
            'Dr. Ricardo Navarro', 'Dra. Gabriela Reyes',
            'Dr. Alejandro Mendoza', 'Dra. Isabella Ortiz',
            'Dr. Sebastián Vargas', 'Dra. Camila Jiménez',
        ];

        $counter = 0;
        foreach ($specialities as $speciality) {
            for ($i = 0; $i < 2; $i++) {
                $name = $doctorNames[$counter] ?? 'Doctor Demo ' . ($counter + 1);
                $email = 'doctor' . ($counter + 1) . '@medimatch.com';

                // Crear usuario
                $user = User::firstOrCreate(
                    ['email' => $email],
                    [
                        'name'      => $name,
                        'password'  => Hash::make('12345678'),
                        'id_number' => 'MED-' . str_pad($counter + 1, 5, '0', STR_PAD_LEFT),
                        'phone'     => '999' . str_pad($counter + 1, 7, '0', STR_PAD_LEFT),
                        'address'   => 'Consultorio ' . ($counter + 1) . ', Torre Médica MediMatch',
                    ]
                );

                $user->assignRole($doctorRole);

                // Crear doctor
                $doctor = Doctor::firstOrCreate(
                    ['user_id' => $user->id],
                    [
                        'speciality_id'          => $speciality->id,
                        'medical_license_number' => 'LIC-' . str_pad($counter + 1, 6, '0', STR_PAD_LEFT),
                        'biography'              => 'Especialista en ' . $speciality->name . ' con amplia experiencia clínica.',
                    ]
                );

                // Crear disponibilidad: Lunes a Viernes, 08:00–17:00 (bloques de 1 hora)
                for ($day = 0; $day <= 4; $day++) { // 0=Lunes, 4=Viernes
                    for ($h = 8; $h < 17; $h++) {
                        Availability::firstOrCreate([
                            'doctor_id'   => $doctor->id,
                            'day_of_week' => $day,
                            'start_time'  => sprintf('%02d:00:00', $h),
                            'end_time'    => sprintf('%02d:00:00', $h + 1),
                        ], [
                            'is_active' => true,
                        ]);
                    }
                }

                $counter++;
            }
        }

        // Crear pacientes de ejemplo
        $patientNames = [
            'Paciente Demo 1'  => 'paciente1@medimatch.com',
            'Paciente Demo 2'  => 'paciente2@medimatch.com',
            'Paciente Demo 3'  => 'paciente3@medimatch.com',
            'Paciente Demo 4'  => 'paciente4@medimatch.com',
            'Paciente Demo 5'  => 'paciente5@medimatch.com',
        ];

        foreach ($patientNames as $name => $email) {
            $user = User::firstOrCreate(
                ['email' => $email],
                [
                    'name'      => $name,
                    'password'  => Hash::make('12345678'),
                    'id_number' => 'PAC-' . substr(md5($email), 0, 8),
                    'phone'     => '998' . rand(1000000, 9999999),
                    'address'   => 'Dirección de prueba',
                ]
            );

            $user->assignRole($patientRole);

            Patient::firstOrCreate(
                ['user_id' => $user->id]
            );
        }
    }
}
