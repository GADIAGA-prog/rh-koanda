<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PosteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('organisation.create') || $this->user()->can('organisation.update');
    }

    public function rules(): array
    {
        $user = $this->user();
        $filialeRule = ['required', 'exists:filiales,id'];
        if (! $user->peutVoirToutLeGroupe()) {
            $filialeRule[] = Rule::in($user->filialesAccessibles());
        }

        return [
            'filiale_id'     => $filialeRule,
            'departement_id' => ['nullable', 'exists:departements,id'],
            'intitule'       => ['required', 'string', 'max:255'],
            'categorie'      => ['nullable', 'string', 'max:100'],
        ];
    }

    public function attributes(): array
    {
        return ['filiale_id' => 'filiale', 'departement_id' => 'département'];
    }
}
