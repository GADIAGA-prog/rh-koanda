<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DepartementRequest extends FormRequest
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
            'filiale_id' => $filialeRule,
            'site_id'    => ['nullable', 'exists:sites,id'],
            'nom'        => ['required', 'string', 'max:255'],
            'code'       => ['nullable', 'string', 'max:30'],
        ];
    }

    public function attributes(): array
    {
        return ['filiale_id' => 'filiale', 'site_id' => 'site'];
    }
}
