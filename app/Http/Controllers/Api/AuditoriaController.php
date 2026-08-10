<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Auditoria;
use App\Http\Requests\StoreAuditoriaRequest;
use App\Http\Resources\AuditoriaResource;
use App\Models\User;
use Illuminate\Http\Request;

class AuditoriaController extends Controller
{
    private const FLUJO_ESTADOS = [
        'Borrador',
        'Planificada',
        'En Ejecución',
        'En Revisión de Informe',
        'Cerrada',
    ];

    private function transicionEsValida(string $actual, string $nuevo): bool
    {
        if ($actual === $nuevo) {
            return true;
        }

        $indiceActual = array_search($actual, self::FLUJO_ESTADOS);
        $indiceNuevo = array_search($nuevo, self::FLUJO_ESTADOS);

        if ($indiceActual === false || $indiceNuevo === false) {
            return false;
        }

        return $indiceNuevo === $indiceActual + 1;
    }

    private function usuariosEnConflicto(array $areasIds, array $candidatosIds)
    {
        $areasIds = array_filter($areasIds);
        $candidatosIds = array_values(array_unique(array_filter($candidatosIds)));

        if (empty($areasIds) || empty($candidatosIds)) {
            return collect();
        }

        return User::whereIn('id', $candidatosIds)
            ->whereIn('area_id', $areasIds)
            ->pluck('id');
    }

    public function index()
    {
        $auditorias = Auditoria::with(['auditorLider', 'areas'])->get();
        return AuditoriaResource::collection($auditorias);
    }

    public function store(StoreAuditoriaRequest $request)
    {
        $data = $request->validated();

        if (($data['estado'] ?? 'Borrador') === 'Planificada') {
            if (empty($data['objetivo']) || empty($data['alcance'])) {
                return response()->json(['message' => 'Se requiere Objetivo y Alcance para pasar al estado Planificada.'], 422);
            }
            if (empty($data['auditor_lider_id'])) {
                return response()->json(['message' => 'Se requiere asignar un Auditor Líder para planificar la auditoría.'], 422);
            }
        }

        if (($data['estado'] ?? 'Borrador') === 'En Ejecución') {
            if (empty($data['auditor_lider_id']) || empty($data['equipo_auditor'])) {
                return response()->json(['message' => 'Una auditoría no puede iniciar sin un Auditor Líder y al menos un equipo auditor asignado.'], 422);
            }
        }

        $candidatos = array_merge(
            $request->input('equipo_auditor', []),
            [$data['auditor_lider_id'] ?? null]
        );
        $conflicto = $this->usuariosEnConflicto($request->input('areas', []), $candidatos);

        if ($conflicto->isNotEmpty()) {
            return response()->json([
                'message' => 'Un usuario no puede auditar (como líder o parte del equipo) un área a la que pertenece.',
                'usuarios_en_conflicto' => $conflicto,
            ], 422);
        }

        $auditoria = Auditoria::create($data)->fresh();

        if ($request->has('areas')) {
            $auditoria->areas()->sync($request->areas);
        }
        if ($request->has('equipo_auditor')) {
            $auditoria->equipoAuditor()->sync($request->equipo_auditor);
        }

        return (new AuditoriaResource($auditoria->load(['auditorLider', 'areas', 'equipoAuditor'])))
            ->response()
            ->setStatusCode(201);
    }

    public function show($id)
    {
        $auditoria = Auditoria::with(['auditorLider', 'areas', 'equipoAuditor'])->find($id);

        if (!$auditoria) {
            return response()->json(['message' => 'Auditoría no encontrada'], 404);
        }

        return new AuditoriaResource($auditoria);
    }

    public function update(StoreAuditoriaRequest $request, $id)
    {
        $auditoria = Auditoria::find($id);

        if (!$auditoria) {
            return response()->json(['message' => 'Auditoría no encontrada'], 404);
        }

        $data = $request->validated();

        if (isset($data['estado']) && ! $this->transicionEsValida($auditoria->estado, $data['estado'])) {
            return response()->json([
                'message' => "No se puede pasar de '{$auditoria->estado}' a '{$data['estado']}'. El flujo debe respetar el orden: "
                    . implode(' → ', self::FLUJO_ESTADOS) . '.',
            ], 422);
        }

        if (($data['estado'] ?? $auditoria->estado) === 'Planificada') {
            $objetivo = $data['objetivo'] ?? $auditoria->objetivo;
            $alcance = $data['alcance'] ?? $auditoria->alcance;
            $lider = $data['auditor_lider_id'] ?? $auditoria->auditor_lider_id;

            if (empty($objetivo) || empty($alcance)) {
                return response()->json([
                    'message' => 'Toda auditoría planificada debe incluir Objetivo y Alcance.'
                ], 422);
            }
            if (empty($lider)) {
                return response()->json([
                    'message' => 'Una auditoría no puede iniciar/planificarse sin un Auditor Líder.'
                ], 422);
            }
        }

        if (($data['estado'] ?? $auditoria->estado) === 'En Ejecución') {
            $lider = $data['auditor_lider_id'] ?? $auditoria->auditor_lider_id;
            $tieneEquipo = $request->has('equipo_auditor')
                ? !empty($request->input('equipo_auditor'))
                : $auditoria->equipoAuditor()->exists();

            if (empty($lider) || !$tieneEquipo) {
                return response()->json(['message' => 'Una auditoría no puede iniciar sin un Auditor Líder y al menos un equipo auditor asignado.'], 422);
            }
        }

        $areasIds = $request->has('areas')
            ? $request->input('areas', [])
            : $auditoria->areas()->pluck('areas.id')->all();

        $equipoIds = $request->has('equipo_auditor')
            ? $request->input('equipo_auditor', [])
            : $auditoria->equipoAuditor()->pluck('users.id')->all();

        $liderId = $data['auditor_lider_id'] ?? $auditoria->auditor_lider_id;

        $conflicto = $this->usuariosEnConflicto($areasIds, array_merge($equipoIds, [$liderId]));

        if ($conflicto->isNotEmpty()) {
            return response()->json([
                'message' => 'Un usuario no puede auditar (como líder o parte del equipo) un área a la que pertenece.',
                'usuarios_en_conflicto' => $conflicto,
            ], 422);
        }

        $auditoria->update($data);
        if ($request->has('areas')) {
            $auditoria->areas()->sync($request->areas);
        }
        if ($request->has('equipo_auditor')) {
            $auditoria->equipoAuditor()->sync($request->equipo_auditor);
        }

        return new AuditoriaResource($auditoria->load(['auditorLider', 'areas']));
    }

    public function destroy($id)
    {
        $auditoria = Auditoria::find($id);

        if (!$auditoria) {
            return response()->json(['message' => 'Auditoría no encontrada'], 404);
        }

        $auditoria->delete();
        return response()->json(['message' => 'Auditoría eliminada correctamente'], 200);
    }
}