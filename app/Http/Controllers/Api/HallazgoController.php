<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Hallazgo;
use App\Http\Requests\StoreHallazgoRequest;
use App\Http\Resources\HallazgoResource;

class HallazgoController extends Controller
{

    public function index()
    {
        $hallazgos = Hallazgo::with(['checklist.auditoria', 'checklist.requisitoIso', 'accionesCorrectivas'])->get();

        return HallazgoResource::collection($hallazgos);
    }

    public function store(StoreHallazgoRequest $request)
    {
        $data = $request->validated();

        $hallazgo = Hallazgo::create($data);

        return (new HallazgoResource($hallazgo->load(['checklist.auditoria', 'accionesCorrectivas'])))
            ->response()
            ->setStatusCode(201);
    }

    public function show($id)
    {
        $hallazgo = Hallazgo::with(['checklist.auditoria', 'checklist.requisitoIso', 'accionesCorrectivas'])->find($id);
        if (!$hallazgo) {
            return response()->json(['message' => 'Hallazgo no encontrado'], 404);
        }

        return new HallazgoResource($hallazgo);
    }

    public function update(StoreHallazgoRequest $request, $id)
    {
        $hallazgo = Hallazgo::find($id);
        if (!$hallazgo) {
            return response()->json(['message' => 'Hallazgo no encontrado'], 404);
        }

        $data = $request->validated();
        
        $hallazgo->update($data);
        return new HallazgoResource($hallazgo->load(['checklist.auditoria', 'accionesCorrectivas']));
    }

    public function destroy($id)
    {
        $hallazgo = Hallazgo::find($id);
        if (!$hallazgo) {
            return response()->json(['message' => 'Hallazgo no encontrado'], 404);
        }

        $hallazgo->delete();
        return response()->json(['message' => 'Hallazgo eliminado correctamente'], 200);
    }
}