<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EvidenciaResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'nombre_archivo' => $this->nombre_archivo,
            'ruta_almacenamiento' => $this->ruta_almacenamiento,
            'url' => $this->ruta_almacenamiento ? asset('storage/' . $this->ruta_almacenamiento) : null,
            'hash_sha256' => $this->hash_sha256,
            'checklist_id' => $this->checklist_id,
            'hallazgo_id' => $this->hallazgo_id,
            'subido_por' => new UserResource($this->whenLoaded('usuario')),
            'created_at' => $this->created_at,
        ];
    }
}