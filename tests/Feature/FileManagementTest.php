<?php

namespace Tests\Feature;

use App\Models\Department;
use App\Models\File;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class FileManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_upload_file(): void
    {
        Storage::fake('public');

        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $department = Department::create([
            'name' => 'IT',
        ]);

        $file = UploadedFile::fake()->create(
            'document.pdf',
            100,
            'application/pdf'
        );

        $response = $this
            ->actingAs($admin)
            ->post('/files', [
                'title' => 'Test Document',
                'department_id' => $department->id,
                'folder_id' => null,
                'file' => $file,
            ]);

        $response->assertStatus(201);

        $this->assertDatabaseHas('files', [
            'title' => 'Test Document',
            'department_id' => $department->id,
            'file_name' => 'document.pdf',
            'uploaded_by' => $admin->id,
        ]);

        $uploadedFile = File::where('title', 'Test Document')->first();

        Storage::disk('public')->assertExists($uploadedFile->file_path);
    }

    public function test_admin_can_edit_file_information(): void
    {
        Storage::fake('public');

        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $department = Department::create([
            'name' => 'IT',
        ]);

        $file = UploadedFile::fake()->create(
            'document.pdf',
            100,
            'application/pdf'
        );

        $filePath = $file->store('documents', 'public');

        $document = File::create([
            'title' => 'Old Title',
            'department_id' => $department->id,
            'folder_id' => null,
            'uploaded_by' => $admin->id,
            'file_name' => 'document.pdf',
            'file_path' => $filePath,
        ]);

        $response = $this
            ->actingAs($admin)
            ->put("/files/{$document->id}", [
                'title' => 'New Title',
                'department_id' => $department->id,
                'folder_id' => null,
            ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('files', [
            'id' => $document->id,
            'title' => 'New Title',
        ]);
    }

    public function test_admin_can_delete_file(): void
    {
        Storage::fake('public');

        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $department = Department::create([
            'name' => 'IT',
        ]);

        $file = UploadedFile::fake()->create(
            'document.pdf',
            100,
            'application/pdf'
        );

        $filePath = $file->store('documents', 'public');

        $document = File::create([
            'title' => 'File To Delete',
            'department_id' => $department->id,
            'folder_id' => null,
            'uploaded_by' => $admin->id,
            'file_name' => 'document.pdf',
            'file_path' => $filePath,
        ]);

        $response = $this
            ->actingAs($admin)
            ->delete("/files/{$document->id}");

        $response->assertStatus(200);

        $this->assertDatabaseMissing('files', [
            'id' => $document->id,
        ]);

        Storage::disk('public')->assertMissing($filePath);
    }
}