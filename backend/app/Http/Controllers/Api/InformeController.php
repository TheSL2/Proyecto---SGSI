<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Auditoria;
use Barryvdh\DomPDF\Facade\Pdf;

class InformeController extends Controller
{
    public function generar($id)
    {
        $auditoria = Auditoria::with([
            'auditorLider', 'equipoAuditor', 'areas',
            'checklists.requisitoIso',
            'checklists.hallazgos.accionesCorrectivas',
        ])->find($id);

        if (!$auditoria) {
            return response()->json(['message' => 'Auditoría no encontrada'], 404);
        }

        if (!in_array($auditoria->estado, ['Cerrada', 'En Revisión de Informe'])) {
            return response()->json([
                'message' => 'RN-INFORMES-01: El informe oficial solo puede generarse si la auditoría está Cerrada o En Revisión de Informe.'
            ], 422);
        }

        if (empty($auditoria->conclusiones)) {
            return response()->json([
                'message' => 'El informe requiere que el auditor líder redacte las Conclusiones antes de generarse.'
            ], 422);
        }


        $hallazgos = $auditoria->checklists->flatMap->hallazgos;
        $sinAccion = $hallazgos->filter(fn ($h) => $h->accionesCorrectivas->isEmpty());

        if ($sinAccion->isNotEmpty()) {
            return response()->json([
                'message' => 'RN-INFORMES-01: El 100% de los hallazgos debe tener un plan de acción asignado.',
                'hallazgos_pendientes' => $sinAccion->pluck('id'),
            ], 422);
        }

        $pdf = Pdf::loadView('informes.auditoria', compact('auditoria', 'hallazgos'))->setPaper('letter');
        return $pdf->download("informe-auditoria-{$auditoria->id}.pdf");
    }
}