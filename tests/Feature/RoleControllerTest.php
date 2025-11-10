<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create a user for authentication
        $this->user = User::factory()->create();
    }

    public function test_can_view_role_index_page(): void
    {
        $response = $this->actingAs($this->user)->get(route('admin.roles.index'));

        $response->assertStatus(200);
    }

    public function test_can_view_role_create_page(): void
    {
        $response = $this->actingAs($this->user)->get(route('admin.roles.create'));

        $response->assertStatus(200);
    }

    public function test_can_create_a_role(): void
    {
        $response = $this->actingAs($this->user)->post(route('admin.roles.store'), [
            'name' => 'Test Role',
        ]);

        $response->assertRedirect(route('admin.roles.index'));
        $response->assertSessionHas('notification');
        
        $this->assertDatabaseHas('roles', [
            'name' => 'Test Role',
        ]);
    }

    public function test_can_view_role_edit_page(): void
    {
        $role = Role::create(['name' => 'Test Role', 'guard_name' => 'web']);

        $response = $this->actingAs($this->user)->get(route('admin.roles.edit', $role));

        $response->assertStatus(200);
        $response->assertSee('Test Role');
    }

    public function test_can_update_a_role(): void
    {
        $role = Role::create(['name' => 'Test Role', 'guard_name' => 'web']);

        $response = $this->actingAs($this->user)->put(route('admin.roles.update', $role), [
            'name' => 'Updated Role',
        ]);

        $response->assertRedirect(route('admin.roles.index'));
        $response->assertSessionHas('notification');
        
        $this->assertDatabaseHas('roles', [
            'id' => $role->id,
            'name' => 'Updated Role',
        ]);
    }

    public function test_can_delete_a_role(): void
    {
        $role = Role::create(['name' => 'Test Role', 'guard_name' => 'web']);

        $response = $this->actingAs($this->user)->delete(route('admin.roles.destroy', $role));

        $response->assertRedirect(route('admin.roles.index'));
        $response->assertSessionHas('notification');
        
        $this->assertDatabaseMissing('roles', [
            'id' => $role->id,
        ]);
    }

    public function test_cannot_create_role_with_duplicate_name(): void
    {
        Role::create(['name' => 'Test Role', 'guard_name' => 'web']);

        $response = $this->actingAs($this->user)->post(route('admin.roles.store'), [
            'name' => 'Test Role',
        ]);

        $response->assertSessionHasErrors('name');
    }
}
