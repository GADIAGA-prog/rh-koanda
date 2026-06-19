<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAbsenceRequest extends FormRequest
{
    public function authorize(): bool
    {
        $absence = $this->route('absence');

        return $absence
            ? $this->user()->can('update', $absence)
            : $this->user()->can('create', \App\Models\Absence::class);
    }

    public function rules(): array
    {
        return [
            'employe_id'   => ['required', 'exists:employes,id'],
            'date_debut'   => ['required', 'date'],
            'date_fin'     => ['required', 'date', 'after_or_equal:date_debut'],
            'justifiee'    => ['nullable', 'boolean'],
            'motif'        => ['nullable', 'string', 'max:255'],
            'justificatif' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
        ];
    }

    public function prepareForValidation(): void
    {
        $this->merge(['justifiee' => $this->boolean('justifiee')]);
    }

    public function attributes(): array
    {
        return ['employe_id' => 'employé', 'date_debut' => 'date de début', 'date_fin' => 'date de fin'];
    }
}
