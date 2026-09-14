<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ScanController;
use App\Http\Controllers\TargetController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

    Route::resource('projects', ProjectController::class);

    Route::post('/projects/{project}/targets', [TargetController::class, 'store'])
        ->name('targets.store');

    Route::delete('/targets/{target}', [TargetController::class, 'destroy'])
        ->name('targets.destroy');

    // Routes Scans (Sprint 2)
    Route::post('/targets/{target}/scans', [ScanController::class, 'store'])
        ->name('scans.store');
    Route::get('/scans/{scan}', [ScanController::class, 'show'])
        ->name('scans.show');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])
        ->name('profile.edit');

    Route::patch('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');

    Route::delete('/profile', [ProfileController::class, 'destroy'])
        ->name('profile.destroy');
});

require __DIR__.'/auth.php';