<?php

namespace App\Http\Controllers;

use App\Models\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FileController extends Controller
{
    public function index(Request $request)
    {
        $files = File::with(['folder', 'department', 'uploader'])
            ->when($request->search, function ($query, $search) {
                $query->where(function ($query) use ($search) {
                    $query->where('title', 'ilike', '%' . $search . '%')
                        ->orWhere('file_name', 'ilike', '%' . $search . '%');
                });
            })
            ->when($request->department_id, function ($query, $departmentId) {
                $query->where('department_id', $departmentId);
            })
            ->when($request->folder_id, function ($query, $folderId) {
                $query->where('folder_id', $folderId);
            })
            ->latest()
            ->paginate(5)
            ->withQueryString();

        return response()->json($files);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'department_id' => ['required', 'exists:departments,id'],
            'folder_id' => ['nullable', 'exists:folders,id'],
            'file' => ['required', 'file', 'max:51200'],
        ]);

        $uploadedFile = $request->file('file');

        $filePath = $uploadedFile->store('documents', 'public');

        if (!$filePath) {
            return response()->json([
                'message' => 'File upload failed.',
            ], 500);
        }

        $file = File::create([
            'title' => $validated['title'],
            'department_id' => $validated['department_id'],
            'folder_id' => $validated['folder_id'] ?? null,
            'uploaded_by' => $request->user()->id,
            'file_name' => $uploadedFile->getClientOriginalName(),
            'file_path' => $filePath,
        ]);

        return response()->json(
            $file->load(['folder', 'department', 'uploader']),
            201
        );
    }
    public function update(Request $request, string $id)
    {
        $file = File::findOrFail($id);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'department_id' => ['required', 'exists:departments,id'],
            'folder_id' => ['nullable', 'exists:folders,id'],
            'file' => ['nullable', 'file', 'max:51200'],
        ]);

        $file->title = $validated['title'];
        $file->department_id = $validated['department_id'];
        $file->folder_id = $validated['folder_id'] ?? null;

        if ($request->hasFile('file')) {
            $uploadedFile = $request->file('file');

            $newFilePath = $uploadedFile->store('documents', 'public');

            if (Storage::disk('public')->exists($file->file_path)) {
                Storage::disk('public')->delete($file->file_path);
            }

            $file->file_path = $newFilePath;
            $file->file_name = $uploadedFile->getClientOriginalName();
        }

        $file->save();

        return response()->json(
            $file->load(['folder', 'department', 'uploader'])
        );
    }
    public function download(string $id)
    {
        $file = File::findOrFail($id);

        if (!Storage::disk('public')->exists($file->file_path)) {
            abort(404, 'File not found.');
        }

        return Storage::disk('public')->download(
            $file->file_path,
            $file->file_name
        );
    }

    public function destroy(string $id)
    {
        $file = File::findOrFail($id);

        if (Storage::disk('public')->exists($file->file_path)) {
            Storage::disk('public')->delete($file->file_path);
        }

        $file->delete();

        return response()->json([
            'message' => 'File deleted successfully.',
        ]);
    }
}
