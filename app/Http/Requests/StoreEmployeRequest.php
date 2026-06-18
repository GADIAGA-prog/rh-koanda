<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreEmployeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', \App\Models\Employe::class);
    }

    public function rules(): array
    {
        return [
            'matricule' => ['nullable', 'string', 'max:30', Rule::unique('employes', 'matricule')],
            'nom' => ['required', 'string', 'max:255'],
            'prenom' => ['required', 'string', 'max:255'],
            'sexe' => ['nullable', Rule::in(['M', 'F'])],
            'date_naissance' => ['nullable', 'date', 'before:today'],
            'lieu_naissance' => ['nullable', 'string', 'max:255'],
            'cnib' => ['nullable', 'string', 'max:30'],
            'telephone' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:255'],
            'adresse' => ['nullable', 'string', 'max:255'],
            'filiale_id' => ['required', 'exists:filiales,id'],
            'site_id' => ['nullable', 'exists:sites,id'],
            'departement_id' => ['nullable', 'exists:departements,id'],
            'poste_id' => ['nullable', 'exists:postes,id'],
            'manager_id' => ['nullable', 'exists:employes,id'],
            'date_embauche' => ['nullable', 'date'],
            'statut' => ['required', Rule::in(['actif', 'suspendu', 'depart', 'conge'])],
        ];
    }

    public function attributes(): array
    {
        return ['filiale_id' => 'filiale', 'poste_id' => 'poste', 'departement_id' => 'département'];
    }
}
