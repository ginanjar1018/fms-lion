<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Folder;

class FolderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $folders = Folder::whereNull('parent_id')
            ->with('childrenRecursive')
            ->get();

        return response()->json($folders);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'parent_id' => ['nullable', 'exists:folders,id'],
        ]);

        $folder = Folder::create($validated);

        return response()->json($folder, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'parent_id' => ['nullable', 'exists:folders,id'],
        ]);

        $folder = Folder::findOrFail($id);

        $folder->update($validated);

        return response()->json($folder);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $folder = Folder::findOrFail($id);

        $folder->delete();

        return response()->json([
            'message' => 'Folder deleted successfully.',
        ]);
    }
}
