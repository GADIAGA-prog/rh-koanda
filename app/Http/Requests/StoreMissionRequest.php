<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMissionRequest extends FormRequest
{
    public function authorize(): bool
    {
        $mission = $this->route('mission');

        return $mission
            ? $this->user()->can('update', $mission)
            : $this->user()->can('create', \App\Models\Mission::class);
    }

    public function rules(): array
    {
        return [
            'employe_id'            => ['required', 'exists:employes,id'],
            'objet'                 => ['required', 'string', 'max:255'],
            'destination'           => ['required', 'string', 'max:255'],
            'lieu_depart'           => ['nullable', 'string', 'max:255'],
            'date_depart'           => ['required', 'date'],
            'date_retour'           => ['required', 'date', 'after_or_equal:date_depart'],
            'nombre_jours'          => ['nullable', 'integer', 'min:1', 'max:365'],
            'moyen_transport'       => ['nullable', 'string', 'max:100'],
            'indemnite_journaliere' => ['required', 'numeric', 'min:0'],
            'autres_frais'          => ['nullable', 'numeric', 'min:0'],
            'devise'                => ['required', 'string', 'size:3'],
            'observations'          => ['nullable', 'string', 'max:2000'],
        ];
    }

    public function attributes(): array
    {
        return [
            'employe_id' => 'employé',
            'date_depart' => 'date de départ',
            'date_retour' => 'date de retour',
            'indemnite_journaliere' => 'indemnité journalière',
        ];
    }
}
