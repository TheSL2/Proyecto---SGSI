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
        $esActualizacion = $this->isMethod('patch') || $this->isMethod('put');

        return [
            'hallazgo_id' => [$esActualizacion ? 'sometimes' : 'required', 'exists:hallazgos,id'],
            'causa_raiz' => 'nullable|string',
            'descripcion_accion' => [$esActualizacion ? 'sometimes' : 'required', 'string'],
            'responsable_id' => [$esActualizacion ? 'sometimes' : 'required', 'exists:users,id'],
            'fecha_limite' => [$esActualizacion ? 'sometimes' : 'required', 'date', 'after_or_equal:today'],
            'estado' => 'nullable|in:Pendiente,En Proceso,Verificada,Rechazada',
            'evidencia_cierre_id' => 'nullable|exists:evidencias,id',
        ];
    }
}