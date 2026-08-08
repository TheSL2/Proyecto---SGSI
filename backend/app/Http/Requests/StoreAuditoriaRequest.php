<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAuditoriaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $esActualizacion = $this->isMethod('patch') || $this->isMethod('put');

        return [
            'titulo' => [$esActualizacion ? 'sometimes' : 'required', 'string', 'max:255'],
            'objetivo' => 'nullable|string',
            'alcance' => 'nullable|string',
            'fecha_inicio' => [$esActualizacion ? 'sometimes' : 'required', 'date'],
            'fecha_fin' => [$esActualizacion ? 'sometimes' : 'required', 'date', 'after_or_equal:fecha_inicio'],
            'auditor_lider_id' => 'nullable|exists:users,id',
            'estado' => 'in:Borrador,Planificada,En Ejecución,En Revisión de Informe,Cerrada',
            'equipo_auditor' => 'nullable|array',
            'equipo_auditor.*' => 'exists:users,id',
        ];
    }
}