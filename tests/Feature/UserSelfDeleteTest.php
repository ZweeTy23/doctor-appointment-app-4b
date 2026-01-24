<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

// Vinculamos la clase base de Laravel y el trait para limpiar la BD
uses(TestCase::class, RefreshDatabase::class);

test('Un usuario no puede eliminarse a sí mismo', function () {

    // 1) Creamos un usuario usando el factory
    $user = User::factory()->create();

    // 2) Simulamos que ya inició sesión
    $this->actingAs($user, 'web');

    // 3) Simulamos una petición HTTP DELETE al endpoint de eliminación
    // Nota: Asegúrate de que la ruta 'admin.users.destroy' exista en tu web.php
    $response = $this->delete(route('admin.users.destroy', $user));

    // 4) Esperamos que el servidor prohíba la acción (Status 403 Forbidden)
    $response->assertStatus(403);

    // 5) Verificar que el usuario siga existiendo en la base de datos
    $this->assertDatabaseHas('users', [
        'id' => $user->id,
    ]);
});
