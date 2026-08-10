<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AccionCorrectiva;
use App\Models\Hallazgo;
use App\Http\Requests\StoreAccionCorrectivaRequest;
use App\Http\Resources\AccionCorrectivaResource;
use App\Services\AuditoriaService;

class AccionCorrectivaController extends Controller
{
    public function index()
    {
        $acciones = AccionCorrectiva::with(['hallazgo', 'responsable', 'evidenciaCierre', 'verificadoPor'])->get();
        return AccionCorrectivaResource::collection($acciones);
    }

    public function store(StoreAccionCorrectivaRequest $request)
    {
        $data = $request->validated();
        $hallazgo = Hallazgo::find($data['hallazgo_id']);

        if (in_array($hallazgo->tipo_hallazgo, ['No Conforme Mayor', 'No Conforme Menor']) && empty($data['causa_raiz'])) {
            return response()->json([
                'message' => 'RN-AC-01: Es obligatorio registrar el Análisis de Causa Raíz para No Conformidades.'
            ], 422);
        }

        $accion = AccionCorrectiva::create($data);
        return (new AccionCorrectivaResource($accion->load(['hallazgo', 'responsable', 'evidenciaCierre'])))
            ->response()
            ->setStatusCode(201);
    }

    public function show($id)
    {
        $accion = AccionCorrectiva::with(['hallazgo', 'responsable', 'evidenciaCierre', 'verificadoPor'])->find($id);
        if (!$accion) {
            return response()->json(['message' => 'Acción Correctiva no encontrada'], 404);
        }
        return new AccionCorrectivaResource($accion);
    }

    public function update(StoreAccionCorrectivaRequest $request, $id)
    {
        $accion = AccionCorrectiva::find($id);
        if (!$accion) {
            return response()->json(['message' => 'Acción Correctiva no encontrada'], 404);
        }

        $data = $request->validated();
        $hallazgo = $accion->hallazgo;

        if (in_array($hallazgo->tipo_hallazgo, ['No Conforme Mayor', 'No Conforme Menor']) && empty($data['causa_raiz'] ?? $accion->causa_raiz)) {
            return response()->json([
                'message' => 'RN-AC-01: Es obligatorio registrar el Análisis de Causa Raíz para No Conformidades.'
            ], 422);
        }

        $nuevoEstado = $data['estado'] ?? $accion->estado;

        if ($nuevoEstado === 'Verificada') {
            if (empty($data['evidencia_cierre_id']) && empty($accion->evidencia_cierre_id)) {
                return response()->json([
                    'message' => 'Para verificar y cerrar una Acción Correctiva es obligatorio asociar una Evidencia Digital de Cierre.'
                ], 422);
            }

            if ($request->user()->id === $accion->responsable_id) {
                return response()->json([
                    'message' => 'RN-AC-02: El responsable de la acción no puede verificar su propio cierre.'
                ], 403);
            }

            if (!in_array($request->user()->rol, ['Auditor', 'Consultor', 'Administrador'])) {
                return response()->json([
                    'message' => 'RN-AC-02: Solo un Auditor Líder o auditor designado puede verificar la efectividad de la acción.'
                ], 403);
            }

            AuditoriaService::log('ACCION_CORRECTIVA_VERIFICADA', [
                'accion_id' => $accion->id,
                'verificado_por' => $request->user()->id,
            ]);

            $data['verificado_por'] = $request->user()->id;
        }

        $accion->update($data);
        return new AccionCorrectivaResource($accion->load(['hallazgo', 'responsable', 'evidenciaCierre', 'verificadoPor']));
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