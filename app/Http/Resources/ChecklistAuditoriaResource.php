<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ChecklistAuditoriaResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'estado_cumplimiento' => $this->estado_cumplimiento,
            'observaciones' => $this->observaciones,
            'justificacion' => $this->justificacion,
            'auditoria' => new AuditoriaResource($this->whenLoaded('auditoria')),
            'requisito_iso' => new RequisitoIsoResource($this->whenLoaded('requisitoIso')),
        ];
    }
}