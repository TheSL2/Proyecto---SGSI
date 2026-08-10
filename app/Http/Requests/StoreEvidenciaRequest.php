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
            'checklist_id' => 'nullable|required_without:hallazgo_id|exists:checklist_auditorias,id',
            'hallazgo_id'  => 'nullable|required_without:checklist_id|exists:hallazgos,id',
            'archivo'      => 'required|file|mimes:pdf,png,jpg,jpeg,xlsx,docx|max:10240',
        ];
    }
}