<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEvaluationRequest extends FormRequest
{
    public function authorize(): bool
    {
        $evaluation = $this->route('evaluation');

        return $evaluation
            ? $this->user()->can('update', $evaluation)
            : $this->user()->can('create', \App\Models\EvaluationPerformance::class);
    }

    public function rules(): array
    {
        return [
            'employe_id'     => ['required', 'exists:employes,id'],
            'periode'        => ['required', 'string', 'max:30'],
            'objectifs'      => ['nullable', 'string', 'max:2000'],
            'note_globale'   => ['nullable', 'numeric', 'min:0', 'max:20'],
            'commentaire'    => ['nullable', 'string', 'max:2000'],
            'prime_proposee' => ['nullable', 'numeric', 'min:0'],
        ];
    }

    public function attributes(): array
    {
        return ['employe_id' => 'employé', 'note_globale' => 'note globale'];
    }
}
