<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Hallazgo;
use App\Http\Requests\StoreHallazgoRequest;

class HallazgoController extends Controller
{
    public function index()
    {
        $hallazgos = Hallazgo::with(['checklist.requisitoIso', 'accionesCorrectivas'])->get();
        return response()->json($hallazgos, 200);
    }

    public function store(StoreHallazgoRequest $request)
    {
        $data = $request->validated();

        $data['fecha_notificacion']  = now();
        $data['estado_notificacion'] = 'Pendiente';
        $data['estado'] = $data['estado'] ?? 'Abierto';
        
        $hallazgo = Hallazgo::create($data);
        return response()->json($hallazgo->load(['checklist.requisitoIso', 'accionesCorrectivas']), 201);
    }

    public function show($id)
    {
        $hallazgo = Hallazgo::with(['checklist.requisitoIso', 'accionesCorrectivas'])->find($id);
        if (!$hallazgo) {
            return response()->json(['message' => 'Hallazgo no encontrado'], 404);
        }
        return response()->json($hallazgo, 200);
    }

    public function update(StoreHallazgoRequest $request, $id)
    {
        $hallazgo = Hallazgo::find($id);
        if (!$hallazgo) {
            return response()->json(['message' => 'Hallazgo no encontrado'], 404);
        }

        $data = $request->validated();

        $hallazgo->update($data);
        return response()->json($hallazgo->load(['checklist.requisitoIso', 'accionesCorrectivas']), 200);
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