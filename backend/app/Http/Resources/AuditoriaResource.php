<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AuditoriaResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'titulo' => $this->titulo,
            'objetivo' => $this->objetivo,
            'alcance' => $this->alcance,
            'fecha_inicio' => $this->fecha_inicio,
            'fecha_fin' => $this->fecha_fin,
            'estado' => $this->estado,
            'auditor_lider' => new UserResource($this->whenLoaded('auditorLider')),
            'areas' => $this->whenLoaded('areas'),
            'equipo_auditor' => UserResource::collection($this->whenLoaded('equipoAuditor')),
        ];
    }
}