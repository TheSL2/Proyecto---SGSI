<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Auditoria;
use App\Models\Hallazgo;
use App\Models\ChecklistAuditoria;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $auditoriasPorEstado = Auditoria::selectRaw('estado, count(*) as total')
            ->groupBy('estado')
            ->pluck('total', 'estado');

        $hallazgosPorTipo = Hallazgo::selectRaw('tipo_hallazgo, count(*) as total')
            ->groupBy('tipo_hallazgo')
            ->pluck('total', 'tipo_hallazgo');

        $totalChecklists = ChecklistAuditoria::count();
        $conformes = ChecklistAuditoria::where('estado_cumplimiento', 'Conforme')->count();
        $porcentajeCumplimiento = $totalChecklists > 0 ? round(($conformes / $totalChecklists) * 100, 2) : 0;

        return response()->json([
            'auditorias' => $auditoriasPorEstado,
            'hallazgos' => $hallazgosPorTipo,
            'cumplimiento_global_iso' => [
                'total_evaluados' => $totalChecklists,
                'conformes' => $conformes,
                'porcentaje' => $porcentajeCumplimiento . '%'
            ]
        ], 200);
    }

    public function reporteAuditoria($id)
    {
        $auditoria = Auditoria::find($id);

        if (!$auditoria) {
            return response()->json(['message' => 'Auditoría no encontrada'], 404);
        }

        $checklists = ChecklistAuditoria::with('requisitoIso')
            ->where('auditoria_id', $id)
            ->get();

        $checklistIds = $checklists->pluck('id');
        
        $hallazgos = Hallazgo::with('accionesCorrectivas.responsable')
            ->whereIn('checklist_id', $checklistIds)
            ->get();

        $totalItems = $checklists->count();
        $conformes = $checklists->where('estado_cumplimiento', 'Conforme')->count();
        $porcentajeISO = $totalItems > 0 ? round(($conformes / $totalItems) * 100, 2) : 0;

        $auditoriaData = $auditoria->toArray();
        $auditoriaData['checklists'] = $checklists;
        $auditoriaData['hallazgos'] = $hallazgos;

        return response()->json([
            'auditoria' => $auditoriaData,
            'resumen_cumplimiento' => [
                'total_controles' => $totalItems,
                'conformes' => $conformes,
                'porcentaje_cumplimiento' => $porcentajeISO . '%'
            ]
        ], 200);
    }
}