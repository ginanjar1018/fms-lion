<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FolderController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\DashboardController;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'role:admin'])
    ->name('dashboard');
Route::get('/viewer/dashboard', function () {
    return Inertia::render('Viewer/Dashboard');
})->middleware(['auth', 'role:viewer'])
  ->name('viewer.dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});
// Route::get('/admin-test', function () {
//     return 'Administrator access granted.';
// })->middleware(['auth', 'role:admin']);




Route::middleware(['auth', 'role:admin'])->group(function () {

    // Folder
    Route::get('/folders', function () {
        return Inertia::render('Folders/Index');
    })->name('folders.index');
    //departments
    Route::get('/departments', function () {
        return Inertia::render('Departments/Index');
    })->name('departments.index');
    //files
    Route::get('/files', function () {
        return Inertia::render('Files/Index');
    })->name('files.index');

    // Route::get('/folders/data', [FolderController::class, 'index'])
    //     ->name('folders.data');

    Route::post('/folders', [FolderController::class, 'store'])
        ->name('folders.store');

    Route::put('/folders/{id}', [FolderController::class, 'update'])
        ->name('folders.update');

    Route::delete('/folders/{id}', [FolderController::class, 'destroy'])
        ->name('folders.destroy');


    // Department
    // Route::get('/departments/data', [DepartmentController::class, 'index'])
    //     ->name('departments.data');

    Route::post('/departments', [DepartmentController::class, 'store'])
        ->name('departments.store');

    Route::put('/departments/{id}', [DepartmentController::class, 'update'])
        ->name('departments.update');

    Route::delete('/departments/{id}', [DepartmentController::class, 'destroy'])
        ->name('departments.destroy');
    
    // file
    // Route::get('/files/data', [FileController::class, 'index'])
    // ->name('files.data');

    // Route::post('/files', [FileController::class, 'store'])
    //     ->name('files.store');

    // Route::delete('/files/{id}', [FileController::class, 'destroy'])
    //     ->name('files.destroy');
    Route::post('/files', [FileController::class, 'store'])
    ->name('files.store');

    Route::put('/files/{id}', [FileController::class, 'update'])
        ->name('files.update');

    Route::delete('/files/{id}', [FileController::class, 'destroy'])
        ->name('files.destroy');
    // Route::get('/files/{id}/download', [FileController::class, 'download'])
    // ->name('files.download');

    
});
Route::middleware('auth')->group(function () {

    Route::get('/files/data', [FileController::class, 'index'])
        ->name('files.data');

    Route::get('/files/{id}/download', [FileController::class, 'download'])
        ->name('files.download');

    Route::get('/folders/data', [FolderController::class, 'index'])
        ->name('folders.data');

    Route::get('/departments/data', [DepartmentController::class, 'index'])
        ->name('departments.data');

});
Route::middleware(['auth', 'role:viewer'])->group(function () {

    Route::get('/viewer/files', function () {
        return Inertia::render('Viewer/Files');
    })->name('viewer.files');

    // Route::get('/files/data', [FileController::class, 'index'])
    // ->name('files.data');
    // Route::get('/files/{id}/download', [FileController::class, 'download'])
    // ->name('files.download');

});
require __DIR__.'/auth.php';
