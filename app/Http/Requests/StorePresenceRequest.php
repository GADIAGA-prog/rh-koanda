<?php

namespace App\Http\Requests;

use App\Models\Enums\StatutPresence;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class StorePresenceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', \App\Models\Presence::class);
    }

    public function rules(): array
    {
        return [
            'employe_id'    => ['required', 'exists:employes,id'],
            'date_presence' => ['required', 'date'],
            'heure_arrivee' => ['nullable', 'date_format:H:i'],
            'heure_depart'  => ['nullable', 'date_format:H:i', 'after_or_equal:heure_arrivee'],
            'statut'        => ['required', new Enum(StatutPresence::class)],
            'commentaire'   => ['nullable', 'string', 'max:255'],
        ];
    }

    public function attributes(): array
    {
        return ['employe_id' => 'employé', 'date_presence' => 'date'];
    }
}
