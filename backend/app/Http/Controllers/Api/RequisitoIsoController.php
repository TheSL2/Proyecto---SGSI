<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\RequisitoIso;
use App\Http\Requests\UpdateRequisitoIsoRequest;
use App\Http\Resources\RequisitoIsoResource;

class RequisitoIsoController extends Controller
{
    public function index()
    {
        $requisitos = RequisitoIso::all();
        return RequisitoIsoResource::collection($requisitos);
    }

    public function show($id)
    {
        $requisito = RequisitoIso::find($id);
        if (!$requisito) {
            return response()->json(['message' => 'Requisito ISO no encontrado'], 404);
        }
        return new RequisitoIsoResource($requisito);
    }

    public function update(UpdateRequisitoIsoRequest $request, $id)
    {
        $requisito = RequisitoIso::find($id);
        if (!$requisito) {
            return response()->json(['message' => 'Requisito ISO no encontrado'], 404);
        }

        $data = $request->validated();

        if (array_key_exists('aplicable', $data) && $data['aplicable'] === false) {
            $tieneChecklists = $requisito->checklists()->exists();
            if ($tieneChecklists) {
                return response()->json([
                    'message' => 'Este requisito ya tiene ítems de checklist vinculados. No se puede marcar como no aplicable sin revisar esas auditorías primero.',
                ], 422);
            }
        }

        $requisito->update($data);
        return new RequisitoIsoResource($requisito);
    }
}