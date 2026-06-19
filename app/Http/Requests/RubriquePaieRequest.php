<?php

namespace App\Http\Requests;

use App\Models\Enums\ModeCalcul;
use App\Models\Enums\TypeRubrique;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;

class RubriquePaieRequest extends FormRequest
{
    public function authorize(): bool
    {
        $rubrique = $this->route('rubrique');

        return $rubrique
            ? $this->user()->can('update', $rubrique)
            : $this->user()->can('create', \App\Models\RubriquePaie::class);
    }

    public function rules(): array
    {
        $user = $this->user();
        $filialeRule = ['nullable', 'exists:filiales,id'];
        if (! $user->peutVoirToutLeGroupe()) {
            $filialeRule = ['required', Rule::in($user->filialesAccessibles())];
        }

        return [
            'filiale_id'  => $filialeRule,
            'code'        => ['required', 'string', 'max:30'],
            'libelle'     => ['required', 'string', 'max:255'],
            'type'        => ['required', new Enum(TypeRubrique::class)],
            'mode_calcul' => ['required', new Enum(ModeCalcul::class)],
            'montant'     => ['nullable', 'numeric', 'min:0', 'required_if:mode_calcul,fixe'],
            'taux'        => ['nullable', 'numeric', 'min:0', 'max:100', 'required_if:mode_calcul,pourcentage'],
            'base_calcul' => ['nullable', Rule::in(['salaire_base', 'brut'])],
            'imposable'   => ['nullable', 'boolean'],
            'ordre'       => ['nullable', 'integer', 'min:0'],
            'actif'       => ['nullable', 'boolean'],
        ];
    }

    public function prepareForValidation(): void
    {
        $this->merge([
            'imposable' => $this->boolean('imposable'),
            'actif' => $this->boolean('actif'),
        ]);
    }
}
