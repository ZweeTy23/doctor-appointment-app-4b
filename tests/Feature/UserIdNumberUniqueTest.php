<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

test('impide crear un usuario con un id_number duplicado', function () {
    $admin = User::factory()->create();

    // 1. Creamos un usuario que ya existe con un ID específico
    User::factory()->create(['id_number' => '20261106']);

    // 2. Intentamos crear otro con el mismo ID
    $response = $this->actingAs($admin)->post(route('admin.users.store'), [
        'name' => 'Usuario Duplicado',
        'email' => 'nuevo@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
        'id_number' => '20261106',
        'phone' => '9991234567',
        'address' => 'Mérida, Yucatán',
        'role_id' => 1
    ]);

    $response->assertSessionHasErrors(['id_number']);
});
