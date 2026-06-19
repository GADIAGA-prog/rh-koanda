<?php

namespace App\Http\Requests;

use App\Models\Enums\Confidentialite;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;

class StoreDocumentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', \App\Models\DocumentRh::class);
    }

    public function rules(): array
    {
        return [
            'employe_id'      => ['required', 'exists:employes,id'],
            'type_document'   => ['required', Rule::in(['contrat', 'diplome', 'cnib', 'attestation', 'fiche_poste', 'certificat', 'autre'])],
            'titre'           => ['required', 'string', 'max:255'],
            'fichier'         => ['required', 'file', 'mimes:pdf,jpg,jpeg,png,doc,docx', 'max:10240'],
            'date_expiration' => ['nullable', 'date'],
            'confidentialite' => ['required', new Enum(Confidentialite::class)],
        ];
    }

    public function attributes(): array
    {
        return ['employe_id' => 'employé', 'type_document' => 'type de document'];
    }
}
