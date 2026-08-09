<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRequisitoIsoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'categoria' => 'sometimes|in:Clausula,Anexo A',
            'codigo' => 'sometimes|string|max:50',
            'descripcion' => 'sometimes|string',
            'aplicable' => 'sometimes|boolean',
            'orientacion_implementacion' => 'nullable|string',
        ];
    }
}