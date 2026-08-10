<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAreaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $esActualizacion = $this->isMethod('patch') || $this->isMethod('put');

        return [
            'nombre' => [$esActualizacion ? 'sometimes' : 'required', 'string', 'max:255'],
            'descripcion' => 'nullable|string',
        ];
    }
}
