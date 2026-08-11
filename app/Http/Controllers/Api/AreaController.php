<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Area;
use App\Http\Requests\StoreAreaRequest;
use App\Http\Resources\AreaResource;

class AreaController extends Controller
{
    public function index()
    {
        $areas = Area::orderBy('nombre')->get();
        return AreaResource::collection($areas);
    }

    public function store(StoreAreaRequest $request)
    {
        $area = Area::create($request->validated());
        return (new AreaResource($area))->response()->setStatusCode(201);
    }

    public function show($id)
    {
        $area = Area::find($id);
        if (!$area) {
            return response()->json(['message' => 'Área no encontrada'], 404);
        }
        return new AreaResource($area);
    }

    public function update(StoreAreaRequest $request, $id)
    {
        $area = Area::find($id);
        if (!$area) {
            return response()->json(['message' => 'Área no encontrada'], 404);
        }
        $area->update($request->validated());
        return new AreaResource($area);
    }

    public function destroy($id)
    {
        $area = Area::find($id);
        if (!$area) {
            return response()->json(['message' => 'Área no encontrada'], 404);
        }

        // Evita romper la trazabilidad si el area ya esta ligada a usuarios o auditorias.
        if ($area->usuarios()->exists() || $area->auditorias()->exists()) {
            return response()->json([
                'message' => 'No se puede eliminar: el área ya tiene usuarios o auditorías vinculadas.',
            ], 422);
        }

        $area->delete();
        return response()->json(['message' => 'Área eliminada correctamente'], 200);
    }
}
