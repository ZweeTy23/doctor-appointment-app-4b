<?php

use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

test('actualiza correctamente el rol de un usuario', function () {
    $admin = User::factory()->create();
    $user = User::factory()->create();

    // Aseguramos que el rol exista
    $newRole = Role::firstOrCreate(['name' => 'patient']);

    $response = $this->actingAs($admin)->put(route('admin.users.update', $user), [
        'name' => $user->name,
        'email' => $user->email,
        'id_number' => $user->id_number,
        'phone' => $user->phone,
        'address' => $user->address,
        'role_id' => $newRole->id
    ]);

    // Verificamos el cambio en el modelo fresco de la DB
    expect($user->fresh()->hasRole('patient'))->toBeTrue();
    $response->assertRedirect(route('admin.users.index'));
});
