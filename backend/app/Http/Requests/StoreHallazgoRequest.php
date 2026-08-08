<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreHallazgoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'checklist_id' => 'nullable|exists:checklist_auditorias,id',
            'tipo_hallazgo' => 'required|in:No Conforme Mayor,No Conforme Menor,Oportunidad de Mejora,Observacion',
            'descripcion' => 'nullable|string',
            'estado' => 'nullable|in:Abierto,En Proceso,Cerrado'
        ];
    }
}