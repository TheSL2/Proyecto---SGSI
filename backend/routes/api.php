<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\AuditoriaController;
use App\Http\Controllers\Api\ChecklistController;
use App\Http\Controllers\Api\EvidenciaController;
use App\Http\Controllers\Api\HallazgoController;
use App\Http\Controllers\Api\AccionCorrectivaController;
use App\Http\Controllers\Api\DashboardController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    Route::apiResource('auditorias', AuditoriaController::class);
    
    Route::apiResource('checklists', ChecklistController::class);

    Route::apiResource('evidencias', EvidenciaController::class);

    Route::apiResource('hallazgos', HallazgoController::class);

    Route::apiResource('acciones-correctivas', AccionCorrectivaController::class);

    Route::get('/dashboard/resumen', [DashboardController::class, 'resumen']);
});