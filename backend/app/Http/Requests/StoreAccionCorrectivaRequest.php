<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAccionCorrectivaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'hallazgo_id' => 'required|exists:hallazgos,id',
            'causa_raiz' => 'nullable|string',
            'descripcion_accion' => 'required|string',
            'responsable_id' => 'required|exists:users,id',
            'fecha_limite' => 'required|date',
            'estado' => 'nullable|in:Pendiente,En Proceso,Verificada,Rechazada',
            'evidencia_cierre_id' => 'nullable|exists:evidencias,id',
            'verificado_por' => 'nullable|exists:users,id',
        ];
    }
}