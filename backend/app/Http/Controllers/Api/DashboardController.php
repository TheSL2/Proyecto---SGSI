<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Auditoria;
use App\Models\Hallazgo;
use App\Models\ChecklistAuditoria;
use App\Models\AccionCorrectiva;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function resumen(Request $request)
    {
        $anio = (int) $request->input('anio', now()->year);

        return response()->json([
            'auditorias' => $this->resumenAuditorias($anio),
            'hallazgos_por_tipo' => $this->hallazgosPorTipo($anio),
            'tasa_cumplimiento_anexo_a' => $this->tasaCumplimientoAnexoA(),
            'acciones_correctivas' => $this->estatusAccionesCorrectivas(),
        ]);
    }

    private function resumenAuditorias(int $anio): array
    {
        $query = Auditoria::whereYear('fecha_inicio', $anio);
        return [
            'programadas' => (clone $query)->count(),
            'ejecutadas' => (clone $query)->whereIn('estado', ['En Revisión de Informe', 'Cerrada'])->count(),
            'en_ejecucion' => (clone $query)->where('estado', 'En Ejecución')->count(),
        ];
    }

    private function hallazgosPorTipo(int $anio): array
    {
        return Hallazgo::whereHas('checklist.auditoria', fn ($q) => $q->whereYear('fecha_inicio', $anio))
            ->selectRaw('tipo_hallazgo, count(*) as total')
            ->groupBy('tipo_hallazgo')
            ->pluck('total', 'tipo_hallazgo')
            ->toArray();
    }

    private function tasaCumplimientoAnexoA(): array
    {
        $base = ChecklistAuditoria::whereHas('requisitoIso', fn ($q) => $q->where('categoria', 'Anexo A'));
        $total = (clone $base)->count();
        $conforme = (clone $base)->where('estado_cumplimiento', 'Conforme')->count();

        return [
            'total_evaluados' => $total,
            'conformes' => $conforme,
            'tasa' => $total > 0 ? round(($conforme / $total) * 100, 2) : 0,
        ];
    }

    private function estatusAccionesCorrectivas(): array
    {
        return [
            'a_tiempo' => AccionCorrectiva::whereIn('estado', ['Pendiente', 'En Proceso'])->where('fecha_limite', '>=', now())->count(),
            'vencidas' => AccionCorrectiva::where('estado', 'Vencida')->count(),
            'cerradas' => AccionCorrectiva::where('estado', 'Verificada')->count(),
            'rechazadas' => AccionCorrectiva::where('estado', 'Rechazada')->count(),
        ];
    }
}