<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Auditoria;
use App\Http\Requests\StoreAuditoriaRequest;
use Illuminate\Http\Request;

class AuditoriaController extends Controller
{
    public function index()
    {
        $auditorias = Auditoria::with(['auditorLider', 'areas'])->get();
        return response()->json($auditorias, 200);
    }

    public function store(StoreAuditoriaRequest $request)
    {
        $data = $request->validated();

        if (($data['estado'] ?? 'Borrador') === 'Planificada') {
            if (empty($data['objetivo']) || empty($data['alcance'])) {
                return response()->json([
                    'message' => 'RN-PLAN ANUAL-01: Se requiere Objetivo y Alcance para pasar al estado Planificada.'
                ], 422);
            }
            if (empty($data['auditor_lider_id'])) {
                return response()->json([
                    'message' => 'RN-PA-02: Se requiere asignar un Auditor Líder para planificar la auditoría.'
                ], 422);
            }
        }

        $auditoria = Auditoria::create($data);

        if ($request->has('areas')) {
            $auditoria->areas()->sync($request->areas);
        }

        return response()->json($auditoria, 201);
    }

    public function show($id)
    {
        $auditoria = Auditoria::with('auditorLider')->find($id);

        if (!$auditoria) {
            return response()->json(['message' => 'Auditoría no encontrada'], 404);
        }

        return response()->json($auditoria, 200);
    }

    public function update(StoreAuditoriaRequest $request, $id)
    {
        $auditoria = Auditoria::find($id);

        if (!$auditoria) {
            return response()->json(['message' => 'Auditoría no encontrada'], 404);
        }

        $data = $request->validated();

        if (($data['estado'] ?? $auditoria->estado) === 'Planificada') {
            $objetivo = $data['objetivo'] ?? $auditoria->objetivo;
            $alcance = $data['alcance'] ?? $auditoria->alcance;
            $lider = $data['auditor_lider_id'] ?? $auditoria->auditor_lider_id;

            if (empty($objetivo) || empty($alcance)) {
                return response()->json([
                    'message' => 'RN-PLAN ANUAL-01: Toda auditoría planificada debe incluir Objetivo y Alcance.'
                ], 422);
            }
            if (empty($lider)) {
                return response()->json([
                    'message' => 'RN-PA-02: Una auditoría no puede iniciar/planificarse sin un Auditor Líder.'
                ], 422);
            }
        }

        if (($data['estado'] ?? '') === 'Cerrado') {
            $hallazgosPendientes = $auditoria->hallazgos()
                ->whereHas('accionesCorrectivas', function ($query) {
                    $query->whereIn('estado', ['Pendiente']);
                })->exists();

            if ($hallazgosPendientes) {
                return response()->json([
                    'message' => 'RN-REP-01: No se puede cerrar la auditoría porque existen Acciones Correctivas pendientes de verificación.'
                ], 422);
            }
        }

        $auditoria->update($data);
        if ($request->has('areas')) {
            $auditoria->areas()->sync($request->areas);
        }

        return response()->json($auditoria, 200);
    }

    public function destroy($id)
    {
        $auditoria = Auditoria::find($id);

        if (!$auditoria) {
            return response()->json(['message' => 'Auditoría no encontrada'], 404);
        }

        $auditoria->delete();
        return response()->json(['message' => 'Auditoría eliminada correctamente'], 200);
    }
}