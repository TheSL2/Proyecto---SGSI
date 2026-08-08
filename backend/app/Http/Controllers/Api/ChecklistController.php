<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ChecklistAuditoria;
use App\Models\RequisitoIso;
use App\Http\Requests\StoreChecklistRequest;

class ChecklistController extends Controller
{
    public function index()
    {
        $checklist = ChecklistAuditoria::with(['auditoria', 'requisitoIso'])->get();
        return response()->json($checklist, 200);
    }

    public function store(StoreChecklistRequest $request)
    {
        $data = $request->validated();

        $requisito = RequisitoIso::find($data['requisito_iso_id']);
        if ($requisito && !$requisito->aplicable && $data['estado_cumplimiento'] !== 'No Aplicable') {
            return response()->json([
                'message' => 'Este requisito ha sido marcado como NO APLICABLE en el catálogo del sistema.'
            ], 422);
        }

        if ($data['estado_cumplimiento'] === 'No Aplicable' && empty($data['justificacion'])) {
            return response()->json([
                'message' => 'RN-CHECK LIST-01: Si un control se marca como No Aplicable, es obligatorio registrar la justificación técnica.'
            ], 422);
        }

        $item = ChecklistAuditoria::create($data);

        $item->load(['auditoria', 'requisitoIso']);

        $responseData = $item->toArray();

        if (in_array($item->estado_cumplimiento, ['No Conforme Mayor', 'No Conforme Menor'])) {
            $responseData['alerta_rn_ck_02'] = 'RN-CK-02: Este ítem evaluado como No Conformidad requiere el registro obligatorio de un Hallazgo vinculado.';
        }

        return response()->json($responseData, 201);
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

        $requisito = RequisitoIso::find($data['requisito_iso_id'] ?? $item->requisito_iso_id);
        if ($requisito && !$requisito->aplicable && ($data['estado_cumplimiento'] ?? $item->estado_cumplimiento) !== 'No Aplicable') {
            return response()->json([
                'message' => 'Este requisito ha sido marcado como NO APLICABLE en el catálogo del sistema.'
            ], 422);
        }

        if (($data['estado_cumplimiento'] ?? $item->estado_cumplimiento) === 'No Aplicable' && empty($data['justificacion'] ?? $item->justificacion)) {
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