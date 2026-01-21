<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ClaimController as AdminClaimController;
use App\Http\Controllers\Admin\EvidenceController as AdminEvidenceController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\LLMController;
use App\Http\Controllers\Admin\AuditLogController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'is_admin'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Claims Management
    Route::get('/claims', [AdminClaimController::class, 'index'])->name('claims.index');
    Route::get('/claims/{claim}', [AdminClaimController::class, 'show'])->name('claims.show');
    Route::put('/claims/{claim}', [AdminClaimController::class, 'update'])->name('claims.update');
    Route::delete('/claims/{claim}', [AdminClaimController::class, 'destroy'])->name('claims.destroy');

    // Evidence Management
    Route::get('/evidence', [AdminEvidenceController::class, 'index'])->name('evidence.index');
    Route::get('/evidence/{evidence}', [AdminEvidenceController::class, 'show'])->name('evidence.show');
    Route::put('/evidence/{evidence}', [AdminEvidenceController::class, 'update'])->name('evidence.update');
    Route::delete('/evidence/{evidence}', [AdminEvidenceController::class, 'destroy'])->name('evidence.destroy');

    // User Management
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::get('/users/{user}', [UserController::class, 'show'])->name('users.show');
    Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');

    // LLM Management
    Route::get('/llm/config', [LLMController::class, 'config'])->name('llm.config');
    Route::post('/llm/config', [LLMController::class, 'updateConfig'])->name('llm.update-config');
    Route::get('/llm/usage', [LLMController::class, 'usage'])->name('llm.usage');

    // Audit Logs
    Route::get('/audit-logs', [AuditLogController::class, 'index'])->name('audit-logs.index');
    Route::get('/audit-logs/{log}', [AuditLogController::class, 'show'])->name('audit-logs.show');
});
