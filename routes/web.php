<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\TwoFactorController;
use App\Models\Area;
use App\Models\User;
use App\Models\RequisitoIso;
use App\Models\Auditoria;
use App\Models\ChecklistAuditoria;
use App\Models\Hallazgo;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/auditorias', function () {
        return view('auditorias.index');
    })->name('web.auditorias.index');

    Route::get('/auditorias/create', function () {
        return view('auditorias.create', [
            'areas' => Area::orderBy('nombre')->get(['id', 'nombre']),
            'auditores' => User::where('rol', 'Auditor')->where('activo', true)->orderBy('name')->get(['id', 'name']),
        ]);
    })->name('web.auditorias.create');

    Route::get('/auditorias/{id}/edit', function (string $id) {
        return view('auditorias.edit', [
            'id' => $id,
            'areas' => Area::orderBy('nombre')->get(['id', 'nombre']),
            'auditores' => User::where('rol', 'Auditor')->where('activo', true)->orderBy('name')->get(['id', 'name']),
        ]);
    })->name('web.auditorias.edit');

    Route::get('/auditorias/{id}', function (string $id) {
        return view('auditorias.show', ['id' => $id]);
    })->name('web.auditorias.show');

    Route::get('/checklists', function () {
        return view('checklists.index');
    })->name('web.checklists.index');

    Route::get('/checklists/create', function () {
        return view('checklists.create', [
            'auditorias' => Auditoria::orderBy('titulo')->get(['id', 'titulo']),
            'requisitos' => RequisitoIso::where('aplicable', true)->orderBy('codigo')->get(['id', 'codigo', 'descripcion']),
        ]);
    })->name('web.checklists.create');

    Route::get('/checklists/{id}/edit', function (string $id) {
        return view('checklists.edit', [
            'id' => $id,
            'auditorias' => Auditoria::orderBy('titulo')->get(['id', 'titulo']),
            'requisitos' => RequisitoIso::where('aplicable', true)->orderBy('codigo')->get(['id', 'codigo', 'descripcion']),
        ]);
    })->name('web.checklists.edit');

    Route::get('/checklists/{id}', function (string $id) {
        return view('checklists.show', ['id' => $id]);
    })->name('web.checklists.show');

    Route::get('/hallazgos', function () {
        return view('hallazgos.index');
    })->name('web.hallazgos.index');

    Route::get('/hallazgos/create', function () {
        return view('hallazgos.create', [
            'checklists' => ChecklistAuditoria::with(['auditoria', 'requisitoIso'])
                ->latest()
                ->take(200)
                ->get(),
        ]);
    })->name('web.hallazgos.create');

    Route::get('/hallazgos/{id}/edit', function (string $id) {
        return view('hallazgos.edit', [
            'id' => $id,
            'checklists' => ChecklistAuditoria::with(['auditoria', 'requisitoIso'])
                ->latest()
                ->take(200)
                ->get(),
        ]);
    })->name('web.hallazgos.edit');

    Route::get('/hallazgos/{id}', function (string $id) {
        return view('hallazgos.show', ['id' => $id]);
    })->name('web.hallazgos.show');

    Route::get('/acciones-correctivas', function () {
        return view('acciones-correctivas.index');
    })->name('web.acciones-correctivas.index');

    Route::get('/acciones-correctivas/create', function () {
        return view('acciones-correctivas.create', [
            'hallazgos' => Hallazgo::with(['checklist.auditoria'])->latest()->take(200)->get(),
            'usuarios' => User::where('activo', true)->orderBy('name')->get(['id', 'name', 'rol']),
        ]);
    })->name('web.acciones-correctivas.create');

    Route::get('/acciones-correctivas/{id}/edit', function (string $id) {
        return view('acciones-correctivas.edit', [
            'id' => $id,
            'hallazgos' => Hallazgo::with(['checklist.auditoria'])->latest()->take(200)->get(),
            'usuarios' => User::where('activo', true)->orderBy('name')->get(['id', 'name', 'rol']),
        ]);
    })->name('web.acciones-correctivas.edit');

    Route::get('/acciones-correctivas/{id}', function (string $id) {
        return view('acciones-correctivas.show', ['id' => $id]);
    })->name('web.acciones-correctivas.show');

    Route::get('/evidencias', function () {
        return view('evidencias.index');
    })->name('web.evidencias.index');

    Route::get('/evidencias/create', function () {
        return view('evidencias.create', [
            'checklists' => ChecklistAuditoria::with(['auditoria', 'requisitoIso'])->latest()->take(200)->get(),
            'hallazgos' => Hallazgo::with(['checklist.auditoria'])->latest()->take(200)->get(),
        ]);
    })->name('web.evidencias.create');

    Route::get('/evidencias/{id}', function (string $id) {
        return view('evidencias.show', ['id' => $id]);
    })->name('web.evidencias.show');

    Route::get('/areas', function () {
        return view('areas.index');
    })->name('web.areas.index');

    Route::get('/usuarios', function () {
        return view('usuarios.index');
    })->name('web.usuarios.index');

    Route::get('/requisitos-iso', function () {
        return view('requisitos-iso.index');
    })->name('web.requisitos-iso.index');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/2fa/setup', [TwoFactorController::class, 'show'])->name('2fa.setup');
    Route::post('/2fa/confirm', [TwoFactorController::class, 'confirm'])->name('2fa.confirm');
    Route::post('/2fa/disable', [TwoFactorController::class, 'disable'])->name('2fa.disable');

    Route::get('/2fa/challenge', [TwoFactorController::class, 'challenge'])->name('2fa.challenge');
    Route::post('/2fa/verify', [TwoFactorController::class, 'verify'])->name('2fa.verify');
});

require __DIR__.'/auth.php';