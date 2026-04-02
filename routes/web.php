<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ReportController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Dashboard Routes
Route::get('/', [DashboardController::class, 'index'])->name('dashboard.home');
Route::get('/projects', [DashboardController::class, 'allProjects'])->name('dashboard.projects');

// Project Routes
Route::prefix('projects')->group(function () {
    Route::get('/{id}', [ProjectController::class, 'show'])->name('projects.show');
    Route::get('/{id}/edit', [ProjectController::class, 'edit'])->name('projects.edit');
    Route::post('/{id}/update', [ProjectController::class, 'update'])->name('projects.update');
    Route::get('/{id}/installation', [ProjectController::class, 'installation'])->name('projects.installation');
    Route::get('/{id}/programming', [ProjectController::class, 'programming'])->name('projects.programming');
    Route::get('/{id}/troubleshooting', [ProjectController::class, 'troubleshooting'])->name('projects.troubleshooting');
    Route::get('/{id}/report-progress', [ProjectController::class, 'reportProgress'])->name('projects.report-progress');
    Route::get('/{id}/documentation', [ProjectController::class, 'documentation'])->name('projects.documentation');
});

// Report Routes
Route::prefix('reports')->group(function () {
    Route::post('/progress/{projectId}', [ReportController::class, 'storeProgress'])->name('reports.progress.store');
    Route::post('/documentation', [ReportController::class, 'storeDocumentation'])->name('reports.documentation.store');
});


