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
            'checklist_id'        => 'required|exists:checklist_auditorias,id',
            'tipo_hallazgo'       => 'required|in:No Conforme Mayor,No Conforme Menor,Oportunidad de Mejora,Observacion',
            'clausula_o_control'  => 'required|string|max:100',
            'descripcion'         => 'required|string',
            'estado'              => 'nullable|in:Abierto,En Proceso,Cerrado',
            'fecha_notificacion'  => 'nullable|date',
            'estado_notificacion' => 'nullable|in:Pendiente,Notificado,Aceptado',
        ];
    }
}