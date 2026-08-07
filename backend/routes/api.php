<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\AuditoriaController;
use App\Http\Controllers\Api\ChecklistController;
use App\Http\Controllers\Api\EvidenciaController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    Route::apiResource('auditorias', AuditoriaController::class);
    
    Route::apiResource('checklists', ChecklistController::class);

    Route::apiResource('evidencias', EvidenciaController::class);
});