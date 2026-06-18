<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateEmployeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('employe'));
    }

    public function rules(): array
    {
        $id = $this->route('employe')->id;

        return [
            'matricule' => ['required', 'string', 'max:30', Rule::unique('employes', 'matricule')->ignore($id)],
            'nom' => ['required', 'string', 'max:255'],
            'prenom' => ['required', 'string', 'max:255'],
            'sexe' => ['nullable', Rule::in(['M', 'F'])],
            'date_naissance' => ['nullable', 'date', 'before:today'],
            'telephone' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:255'],
            'filiale_id' => ['required', 'exists:filiales,id'],
            'site_id' => ['nullable', 'exists:sites,id'],
            'departement_id' => ['nullable', 'exists:departements,id'],
            'poste_id' => ['nullable', 'exists:postes,id'],
            'manager_id' => ['nullable', 'exists:employes,id'],
            'date_embauche' => ['nullable', 'date'],
            'statut' => ['required', Rule::in(['actif', 'suspendu', 'depart', 'conge'])],
        ];
    }
}
