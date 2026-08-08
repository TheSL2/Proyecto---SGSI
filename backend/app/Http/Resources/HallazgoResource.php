<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class HallazgoResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'tipo_hallazgo' => $this->tipo_hallazgo,
            'clausula_o_control' => $this->clausula_o_control,
            'descripcion' => $this->descripcion,
            'estado' => $this->estado,
            'fecha_notificacion' => $this->fecha_notificacion,
            'estado_notificacion' => $this->estado_notificacion,
            'checklist' => new ChecklistAuditoriaResource($this->whenLoaded('checklist')),
            'acciones_correctivas' => AccionCorrectivaResource::collection($this->whenLoaded('accionesCorrectivas')),
        ];
    }
}