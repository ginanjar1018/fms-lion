<?php

namespace Tests\Feature;

use App\Models\Department;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DepartmentManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_department(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $response = $this
            ->actingAs($admin)
            ->post('/departments', [
                'name' => 'Finance',
            ]);

        $response->assertStatus(201);

        $this->assertDatabaseHas('departments', [
            'name' => 'Finance',
        ]);
    }

    public function test_admin_can_update_department(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $department = Department::create([
            'name' => 'Old Department',
        ]);

        $response = $this
            ->actingAs($admin)
            ->put("/departments/{$department->id}", [
                'name' => 'New Department',
            ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('departments', [
            'id' => $department->id,
            'name' => 'New Department',
        ]);
    }

    public function test_admin_can_delete_department(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $department = Department::create([
            'name' => 'Department To Delete',
        ]);

        $response = $this
            ->actingAs($admin)
            ->delete("/departments/{$department->id}");

        $response->assertStatus(200);

        $this->assertDatabaseMissing('departments', [
            'id' => $department->id,
        ]);
    }
}