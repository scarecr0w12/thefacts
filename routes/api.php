<?php

use App\Http\Controllers\ClaimController;
use App\Http\Controllers\EvidenceController;
use App\Http\Controllers\VoteController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::middleware('throttle:60,1')->group(function () {
    Route::get('/claims', [ClaimController::class, 'apiIndex']);
    Route::get('/claims/{claim}', [ClaimController::class, 'apiShow']);
});

Route::middleware(['auth:sanctum', 'throttle:30,1'])->group(function () {
    Route::post('/claims', [ClaimController::class, 'apiStore']);
    Route::post('/claims/{claim}/evidence', [EvidenceController::class, 'apiStore']);
    Route::post('/evidence/{evidence}/vote', [VoteController::class, 'apiStore']);
});
