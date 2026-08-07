<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AccionCorrectiva;
use App\Models\Hallazgo;
use App\Http\Requests\StoreAccionCorrectivaRequest;

class AccionCorrectivaController extends Controller
{
    public function index()
    {
        $acciones = AccionCorrectiva::with(['hallazgo', 'responsable', 'evidenciaCierre'])->get();
        return response()->json($acciones, 200);
    }

    public function store(StoreAccionCorrectivaRequest $request)
    {
        $data = $request->validated();
        $hallazgo = Hallazgo::find($data['hallazgo_id']);
        
        if (in_array($hallazgo->tipo_hallazgo, ['No Conforme Mayor', 'No Conforme Menor']) && empty($data['causa_raiz'])) {
            return response()->json([
                'message' => 'RN-HALLAZGO-01: Es obligatorio registrar el Análisis de Causa Raíz para No Conformidades.'
            ], 422);
        }

        $accion = AccionCorrectiva::create($data);
        return response()->json($accion->load(['hallazgo', 'responsable', 'evidenciaCierre']), 201);
    }

    public function show($id)
    {
        $accion = AccionCorrectiva::with(['hallazgo', 'responsable', 'evidenciaCierre'])->find($id);
        if (!$accion) {
            return response()->json(['message' => 'Acción Correctiva no encontrada'], 404);
        }
        return response()->json($accion, 200);
    }

    public function update(StoreAccionCorrectivaRequest $request, $id)
    {
        $accion = AccionCorrectiva::find($id);
        if (!$accion) {
            return response()->json(['message' => 'Acción Correctiva no encontrada'], 404);
        }

        $data = $request->validated();
        $hallazgo = $accion->hallazgo;
        
        if (in_array($hallazgo->tipo_hallazgo, ['No Conforme Mayor', 'No Conforme Menor']) && empty($data['causa_raiz'])) {
            return response()->json([
                'message' => 'RN-HALLAZGO-01: Es obligatorio registrar el Análisis de Causa Raíz para No Conformidades.'
            ], 422);
        }
        
        if (($data['estado'] ?? $accion->estado) === 'Verificada' && empty($data['evidencia_cierre_id']) && empty($accion->evidencia_cierre_id)) {
            return response()->json([
                'message' => 'Para verificar y cerrar una Acción Correctiva es obligatorio asociar una Evidencia Digital de Cierre.'
            ], 422);
        }

        $accion->update($data);
        return response()->json($accion->load(['hallazgo', 'responsable', 'evidenciaCierre']), 200);
    }

    public function destroy($id)
    {
        $accion = AccionCorrectiva::find($id);
        if (!$accion) {
            return response()->json(['message' => 'Acción Correctiva no encontrada'], 404);
        }

        $accion->delete();
        return response()->json(['message' => 'Acción Correctiva eliminada correctamente'], 200);
    }
}