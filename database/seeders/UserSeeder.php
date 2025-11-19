<?php
// file: database/seeders/UserSeeder.php
namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::firstOrCreate(
            ['email' => 'jose@gmail.com'],
            [
                'name'      => 'Jose Delgado',
                'password'  => Hash::make('12345678'),
                'id_number' => '1234567890',
                'phone'     => '0987654321',
                'address'   => 'Calle Falsa 123, Colonia Fake',
            ]
        );

        if (class_exists(\Spatie\Permission\Models\Role::class) && \Spatie\Permission\Models\Role::where('name', 'Doctor')->exists()) {
            $user->assignRole('Doctor');
        }
    }
}
