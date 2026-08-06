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
        return [
            'titulo' => 'required|string|max:255',
            'objetivo' => 'nullable|string',
            'alcance' => 'nullable|string',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
            'auditor_lider_id' => 'nullable|exists:users,id',
            'estado' => 'in:Borrador,Planificada,En Ejecución,En Revisión de Informe,Cerrada'
        ];
    }
}