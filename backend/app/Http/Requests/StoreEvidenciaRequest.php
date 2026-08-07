<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEvidenciaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'checklist_id' => 'required|exists:checklist_auditorias,id',
            'archivo' => 'required|file|mimes:pdf,png,jpg,jpeg,xlsx,docx|max:10240',
        ];
    }
}