<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FolderManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_folder(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $response = $this
            ->actingAs($admin)
            ->post('/folders', [
                'name' => 'Test Folder',
                'parent_id' => null,
            ]);

        $response->assertStatus(201);

        $this->assertDatabaseHas('folders', [
            'name' => 'Test Folder',
            'parent_id' => null,
        ]);
    }

    public function test_admin_can_rename_folder(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $folder = \App\Models\Folder::create([
            'name' => 'Old Folder',
            'parent_id' => null,
        ]);

        $response = $this
            ->actingAs($admin)
            ->put("/folders/{$folder->id}", [
                'name' => 'New Folder',
            ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('folders', [
            'id' => $folder->id,
            'name' => 'New Folder',
        ]);
    }

    public function test_admin_can_delete_folder(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $folder = \App\Models\Folder::create([
            'name' => 'Folder To Delete',
            'parent_id' => null,
        ]);

        $response = $this
            ->actingAs($admin)
            ->delete("/folders/{$folder->id}");

        $response->assertStatus(200);

        $this->assertDatabaseMissing('folders', [
            'id' => $folder->id,
        ]);
    }
}