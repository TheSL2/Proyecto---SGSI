<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\RequisitoIso;

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
            'requisito_iso_id' => [
                'required',
                'exists:requisito_isos,id',
                function ($attribute, $value, $fail) {
                    $requisito = RequisitoIso::find($value);
                    if ($requisito && !$requisito->aplicable) {
                        $fail('RN-APLICABILIDAD: Este requisito no aplica a la organización (definido en la SoA) y no puede vincularse a un checklist.');
                    }
                },
            ],
            'estado_cumplimiento' => 'required|in:Conforme,No Conforme Mayor,No Conforme Menor,Oportunidad de Mejora,No Aplicable',
            'observaciones' => 'nullable|string',
            'justificacion' => 'nullable|string',
        ];
    }
}