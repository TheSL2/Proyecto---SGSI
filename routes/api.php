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
use App\Http\Controllers\Api\InformeController;
use App\Http\Controllers\Api\RequisitoIsoController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:login');

Route::middleware(['auth:sanctum', 'throttle:api'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    Route::apiResource('auditorias', AuditoriaController::class);
    
    Route::apiResource('checklists', ChecklistController::class);

    Route::apiResource('evidencias', EvidenciaController::class);

    Route::apiResource('hallazgos', HallazgoController::class);

    Route::apiResource('acciones-correctivas', AccionCorrectivaController::class);

    Route::get('/dashboard/resumen', [DashboardController::class, 'resumen']);

    Route::get('/auditorias/{id}/informe', [InformeController::class, 'generar']);

    Route::get('/requisito-isos', [RequisitoIsoController::class, 'index']);
    Route::get('/requisito-isos/{id}', [RequisitoIsoController::class, 'show']);
    Route::patch('/requisito-isos/{id}', [RequisitoIsoController::class, 'update']);

});