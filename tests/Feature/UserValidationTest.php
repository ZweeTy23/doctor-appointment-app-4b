<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

// IMPORTANTE: Esto carga el entorno de Laravel para poder usar Factories y Facades
uses(TestCase::class, RefreshDatabase::class);

test('requiere id_number, phone y address para crear un usuario', function () {
    $admin = User::factory()->create();

    $response = $this->actingAs($admin)->post(route('admin.users.store'), [
        'name' => 'Prueba Validación',
        'email' => 'test@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
        'id_number' => '',
        'phone' => '',
        'address' => '',
        'role_id' => 1
    ]);

    $response->assertSessionHasErrors(['id_number', 'phone', 'address']);
});
