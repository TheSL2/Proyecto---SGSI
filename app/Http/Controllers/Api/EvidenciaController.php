<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Evidencia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\StoreEvidenciaRequest;
use App\Http\Resources\EvidenciaResource;
use App\Services\AuditoriaService;

class EvidenciaController extends Controller
{
    public function index()
    {
        $evidencias = Evidencia::with(['checklist', 'hallazgo', 'usuario'])->get();
        return EvidenciaResource::collection($evidencias);
    }

    public function store(StoreEvidenciaRequest $request)
    {
        $file = $request->file('archivo');

        $hashSha256 = hash_file('sha256', $file->getRealPath());

        $path = $file->store('evidencias', 'public');

        $evidencia = Evidencia::create([
            'checklist_id' => $request->checklist_id,
            'hallazgo_id' => $request->hallazgo_id,
            'nombre_archivo' => $file->getClientOriginalName(),
            'ruta_almacenamiento' => $path,
            'hash_sha256' => $hashSha256,
            'subido_por' => $request->user()->id,
        ]);

        return (new EvidenciaResource($evidencia->load(['checklist', 'hallazgo', 'usuario'])))
            ->response()
            ->setStatusCode(201);
    }

    public function show($id)
    {
        $evidencia = Evidencia::with(['checklist', 'hallazgo', 'usuario'])->find($id);
        if (!$evidencia) {
            return response()->json(['message' => 'Evidencia no encontrada'], 404);
        }
        return new EvidenciaResource($evidencia);
    }

    public function destroy(Request $request, $id)
    {

        $evidencia = Evidencia::with(['checklist.auditoria', 'hallazgo.checklist.auditoria'])->find($id);

        if (!$evidencia) {
            return response()->json(['message' => 'Evidencia no encontrada'], 404);
        }

        $estadoAuditoria = $evidencia->checklist?->auditoria?->estado
            ?? $evidencia->hallazgo?->checklist?->auditoria?->estado
            ?? 'Borrador';

        if (in_array($estadoAuditoria, ['En Ejecución', 'En Revisión de Informe', 'Cerrada']) && $request->user()->rol !== 'Administrador') {
            return response()->json([
                'message' => 'Inmutabilidad garantizada. No se pueden eliminar evidencias de auditorías en ejecución o posteriores.'
            ], 403);
        }

        Storage::disk('public')->delete($evidencia->ruta_almacenamiento);
        $evidencia->delete();

        AuditoriaService::log('EVIDENCIA_ELIMINADA', ['evidencia_id' => $evidencia->id]);

        return response()->json(['message' => 'Evidencia eliminada correctamente'], 200);
    }
}