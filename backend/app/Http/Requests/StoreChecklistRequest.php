<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreChecklistRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'auditoria_id' => 'required|exists:auditorias,id',
            'requisito_iso_id' => 'required|exists:requisito_isos,id',
            'estado_cumplimiento' => 'required|in:Conforme,No Conforme Mayor,No Conforme Menor,Oportunidad de Mejora,No Aplicable',
            'observaciones' => 'nullable|string',
            'justificacion' => 'nullable|string',
        ];
    }
}