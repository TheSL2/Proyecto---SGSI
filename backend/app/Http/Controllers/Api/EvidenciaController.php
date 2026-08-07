<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Evidencia;
use App\Http\Requests\StoreEvidenciaRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EvidenciaController extends Controller
{
    public function index()
    {
        $evidencias = Evidencia::with(['checklist', 'usuario'])->get();
        return response()->json($evidencias, 200);
    }

    public function store(StoreEvidenciaRequest $request)
    {
        $file = $request->file('archivo');

        $hashSha256 = hash_file('sha256', $file->getRealPath());

        $path = $file->store('evidencias', 'public');

        $evidencia = Evidencia::create([
            'checklist_id' => $request->checklist_id,
            'nombre_archivo' => $file->getClientOriginalName(),
            'ruta_almacenamiento' => $path,
            'hash_sha256' => $hashSha256,
            'subido_por' => $request->user()->id,
        ]);

        return response()->json($evidencia, 201);
    }

    public function show($id)
    {
        $evidencia = Evidencia::with(['checklist', 'usuario'])->find($id);
        if (!$evidencia) {
            return response()->json(['message' => 'Evidencia no encontrada'], 404);
        }
        return response()->json($evidencia, 200);
    }

    public function destroy(Request $request, $id)
    {
        $evidencia = Evidencia::with('checklist.auditoria')->find($id);

        if (!$evidencia) {
            return response()->json(['message' => 'Evidencia no encontrada'], 404);
        }

        $estadoAuditoria = $evidencia->checklist->auditoria->estado ?? 'Borrador';

        if (in_array($estadoAuditoria, ['En Ejecución', 'En Revisión de Informe', 'Cerrada']) && $request->user()->rol !== 'Administrador') {
            return response()->json([
                'message' => 'RN-EV-02: Inmutabilidad garantizada. No se pueden eliminar evidencias de auditorías en ejecución o posteriores.'
            ], 403);
        }

        Storage::disk('public')->delete($evidencia->ruta_almacenamiento);
        $evidencia->delete();

        return response()->json(['message' => 'Evidencia eliminada correctamente'], 200);
    }
}