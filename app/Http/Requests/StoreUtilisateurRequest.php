<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreUtilisateurRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', User::class);
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role' => ['required', Rule::in(User::ROLES)],
            'filiale_id' => ['nullable', 'exists:filiales,id'],
            'filiales_gerees' => ['nullable', 'array'],
            'filiales_gerees.*' => ['exists:filiales,id'],
            'employe_id' => ['nullable', 'exists:employes,id'],
            'actif' => ['boolean'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge(['actif' => $this->boolean('actif')]);
    }

    public function attributes(): array
    {
        return [
            'name' => 'nom',
            'role' => 'rôle',
            'filiale_id' => 'filiale principale',
            'filiales_gerees' => 'filiales gérées',
        ];
    }
}
