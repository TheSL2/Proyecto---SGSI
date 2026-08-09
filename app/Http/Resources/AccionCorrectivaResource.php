<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AccionCorrectivaResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'causa_raiz' => $this->causa_raiz,
            'descripcion_accion' => $this->descripcion_accion,
            'fecha_limite' => $this->fecha_limite,
            'estado' => $this->estado,
            'hallazgo_id' => $this->hallazgo_id,
            'responsable' => new UserResource($this->whenLoaded('responsable')),
            'evidencia_cierre' => new EvidenciaResource($this->whenLoaded('evidenciaCierre')),
            'verificado_por' => new UserResource($this->whenLoaded('verificadoPor')),
        ];
    }
}