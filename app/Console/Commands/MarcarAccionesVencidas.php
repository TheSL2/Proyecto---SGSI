<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\AccionCorrectiva;
use App\Notifications\AccionCorrectivaVencidaNotification;
use Illuminate\Support\Facades\Notification as Notify;

class MarcarAccionesVencidas extends Command
{
    protected $signature = 'acciones:marcar-vencidas';
    protected $description = 'RN-AC-03: marca como Vencida las acciones correctivas con fecha_limite expirada y notifica al responsable y auditor líder.';

    public function handle(): int
    {
        $vencidas = AccionCorrectiva::whereIn('estado', ['Pendiente', 'En Proceso'])
            ->whereDate('fecha_limite', '<', now())
            ->with(['responsable', 'hallazgo.checklist.auditoria.auditorLider'])
            ->get();

        foreach ($vencidas as $accion) {
            $accion->update(['estado' => 'Vencida']);

            $destinatarios = collect([
                $accion->responsable,
                $accion->hallazgo->checklist->auditoria->auditorLider ?? null,
            ])->filter()->unique('id');

            Notify::send($destinatarios, new AccionCorrectivaVencidaNotification($accion));
        }

        $this->info("{$vencidas->count()} acción(es) marcada(s) como Vencida.");
        return self::SUCCESS;
    }
}