<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\File;
use App\Models\Folder;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        return Inertia::render('Dashboard', [
            'stats' => [
                'folders' => Folder::count(),
                'files' => File::count(),
                'departments' => Department::count(),
            ],

            'latestFiles' => File::with(['folder', 'department', 'uploader'])
                ->latest()
                ->take(10)
                ->get(),
        ]);
    }
}