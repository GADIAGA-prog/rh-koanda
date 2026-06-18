<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCongeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', \App\Models\Conge::class);
    }

    public function rules(): array
    {
        return [
            'employe_id' => ['required', 'exists:employes,id'],
            'type_conge' => ['required', Rule::in(['annuel', 'maladie', 'maternite', 'paternite', 'exceptionnel', 'sans_solde'])],
            'date_debut' => ['required', 'date'],
            'date_fin' => ['required', 'date', 'after_or_equal:date_debut'],
            'motif' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
