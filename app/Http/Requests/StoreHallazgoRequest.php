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
        $esActualizacion = $this->isMethod('patch') || $this->isMethod('put');

        return [
            'checklist_id' => [$esActualizacion ? 'sometimes' : 'required', 'exists:checklist_auditorias,id'],
            'tipo_hallazgo' => [$esActualizacion ? 'sometimes' : 'required', 'in:No Conforme Mayor,No Conforme Menor,Oportunidad de Mejora,Observacion'],
            'clausula_o_control' => [$esActualizacion ? 'sometimes' : 'required', 'string', 'max:100'],
            'descripcion' => [$esActualizacion ? 'sometimes' : 'required', 'string'],
            'estado' => 'nullable|in:Abierto,En Proceso,Cerrado',
            'fecha_notificacion' => 'nullable|date',
            'estado_notificacion' => 'nullable|in:Pendiente,Notificado,Aceptado',
        ];
    }
}