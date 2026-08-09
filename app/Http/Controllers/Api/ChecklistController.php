<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ChecklistAuditoria;
use App\Http\Requests\StoreChecklistRequest;
use App\Http\Resources\ChecklistAuditoriaResource;

class ChecklistController extends Controller
{
    public function index()
    {
        $checklist = ChecklistAuditoria::with(['auditoria', 'requisitoIso'])->get();
        return ChecklistAuditoriaResource::collection($checklist);
    }

    public function store(StoreChecklistRequest $request)
    {
        $data = $request->validated();

        if ($data['estado_cumplimiento'] === 'No Aplicable' && empty($data['justificacion'])) {
            return response()->json([
                'message' => 'RN-CHECK LIST-01: Si un control se marca como No Aplicable, es obligatorio registrar la justificación técnica.'
            ], 422);
        }

        $item = ChecklistAuditoria::create($data);
        return response()->json($item->load(['auditoria', 'requisitoIso']), 201);
    }

    public function show($id)
    {
        $item = ChecklistAuditoria::with(['auditoria', 'requisitoIso'])->find($id);
        if (!$item) {
            return response()->json(['message' => 'Ítem no encontrado'], 404);
        }
        return response()->json($item, 200);
    }

    public function update(StoreChecklistRequest $request, $id)
    {
        $item = ChecklistAuditoria::find($id);
        if (!$item) {
            return response()->json(['message' => 'Ítem no encontrado'], 404);
        }

        $data = $request->validated();

        if ($data['estado_cumplimiento'] === 'No Aplicable' && empty($data['justificacion'])) {
            return response()->json([
                'message' => 'RN-CHECK LIST-01: Si un control se marca como No Aplicable, es obligatorio registrar la justificación técnica.'
            ], 422);
        }

        $item->update($data);
        return response()->json($item->load(['auditoria', 'requisitoIso']), 200);
    }

    public function destroy($id)
    {
        $item = ChecklistAuditoria::find($id);
        if (!$item) {
            return response()->json(['message' => 'Ítem no encontrado'], 404);
        }

        $item->delete();
        return response()->json(['message' => 'Ítem del checklist eliminado correctamente'], 200);
    }
}