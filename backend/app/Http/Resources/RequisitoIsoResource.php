<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RequisitoIsoResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'categoria' => $this->categoria,
            'codigo' => $this->codigo,
            'descripcion' => $this->descripcion,
            'aplicable' => (bool) $this->aplicable,
            'orientacion_implementacion' => $this->orientacion_implementacion,
        ];
    }
}