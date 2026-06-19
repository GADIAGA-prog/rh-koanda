<?php

namespace App\Http\Requests;

use App\Models\Enums\TypeContrat;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class RenouvelerContratRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('renouveler', $this->route('contrat'));
    }

    public function rules(): array
    {
        return [
            'reference'    => ['nullable', 'string', 'max:50'],
            'type_contrat' => ['nullable', new Enum(TypeContrat::class)],
            'date_debut'   => ['required', 'date'],
            'date_fin'     => ['nullable', 'date', 'after_or_equal:date_debut'],
            'salaire_base' => ['nullable', 'numeric', 'min:0'],
            'devise'       => ['nullable', 'string', 'size:3'],
            'observations' => ['nullable', 'string', 'max:2000'],
        ];
    }

    public function attributes(): array
    {
        return [
            'date_debut'   => 'date de début',
            'date_fin'     => 'date de fin',
            'salaire_base' => 'salaire de base',
        ];
    }
}
