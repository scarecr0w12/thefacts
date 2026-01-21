<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\ClaimController;
use App\Http\Controllers\EvidenceController;
use App\Http\Controllers\InstallerController;
use App\Http\Controllers\VoteController;
use Illuminate\Support\Facades\Route;

// Installation routes (no middleware)
Route::get('/install', [InstallerController::class, 'show'])->name('installer.show')->withoutMiddleware(['web']);
Route::post('/install', [InstallerController::class, 'store'])->name('installer.store')->withoutMiddleware(['web']);

// Auth routes
Route::get('/register', [RegisterController::class, 'show'])->name('register');
Route::post('/register', [RegisterController::class, 'store']);

Route::get('/login', [LoginController::class, 'show'])->name('login');
Route::post('/login', [LoginController::class, 'store']);

Route::post('/logout', [LoginController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

// Public routes
Route::get('/', [ClaimController::class, 'index'])->name('claims.index');

// Protected routes
Route::middleware('auth')->group(function () {
    Route::get('/claims/create', [ClaimController::class, 'create'])->name('claims.create');
    Route::post('/claims', [ClaimController::class, 'store'])->name('claims.store');
});

Route::get('/claims/{claim}', [ClaimController::class, 'show'])->name('claims.show');
Route::post('/claims/{claim}/evidence', [EvidenceController::class, 'store'])
    ->middleware('auth')
    ->name('evidence.store');

Route::post('/evidence/{evidence}/vote', [VoteController::class, 'store'])
    ->middleware('auth')
    ->name('votes.store');

