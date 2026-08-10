<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'rol' => 'sometimes|in:Administrador,Consultor,Auditor,Auditado,Alta Dirección',
            'area_id' => 'sometimes|nullable|exists:areas,id',
            'activo' => 'sometimes|boolean',
        ];
    }
}
