<?php

namespace App\Http\Requests;

use App\Models\Enums\StatutFormation;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;

class FormationRequest extends FormRequest
{
    public function authorize(): bool
    {
        $formation = $this->route('formation');

        return $formation
            ? $this->user()->can('update', $formation)
            : $this->user()->can('create', \App\Models\Formation::class);
    }

    public function rules(): array
    {
        $user = $this->user();
        $filialeRule = ['required', 'exists:filiales,id'];
        if (! $user->peutVoirToutLeGroupe()) {
            $filialeRule[] = Rule::in($user->filialesAccessibles());
        }

        return [
            'filiale_id' => $filialeRule,
            'intitule'   => ['required', 'string', 'max:255'],
            'objectif'   => ['nullable', 'string', 'max:2000'],
            'organisme'  => ['nullable', 'string', 'max:255'],
            'date_debut' => ['nullable', 'date'],
            'date_fin'   => ['nullable', 'date', 'after_or_equal:date_debut'],
            'cout'       => ['nullable', 'numeric', 'min:0'],
            'devise'     => ['required', 'string', 'size:3'],
            'statut'     => ['required', new Enum(StatutFormation::class)],
        ];
    }

    public function attributes(): array
    {
        return ['filiale_id' => 'filiale'];
    }
}
