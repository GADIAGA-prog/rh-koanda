<?php

namespace App\Http\Requests;

use App\Models\Enums\StatutContrat;
use App\Models\Enums\TypeContrat;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;

class StoreContratRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', \App\Models\Contrat::class);
    }

    public function rules(): array
    {
        return [
            'employe_id'   => ['required', 'exists:employes,id'],
            'reference'    => ['nullable', 'string', 'max:50'],
            'type_contrat' => ['required', new Enum(TypeContrat::class)],
            'date_debut'   => ['required', 'date'],
            'date_fin'     => ['nullable', 'date', 'after_or_equal:date_debut'],
            'salaire_base' => ['required', 'numeric', 'min:0'],
            'devise'       => ['required', 'string', 'size:3'],
            'statut'       => ['required', new Enum(StatutContrat::class)],
            'observations' => ['nullable', 'string', 'max:2000'],
        ];
    }

    public function attributes(): array
    {
        return [
            'employe_id'   => 'employé',
            'type_contrat' => 'type de contrat',
            'date_debut'   => 'date de début',
            'date_fin'     => 'date de fin',
            'salaire_base' => 'salaire de base',
        ];
    }
}
