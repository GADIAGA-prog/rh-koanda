<?php

namespace App\Http\Requests;

use App\Models\Enums\TypeSanction;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class StoreSanctionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', \App\Models\Sanction::class);
    }

    public function rules(): array
    {
        return [
            'employe_id'    => ['required', 'exists:employes,id'],
            'type'          => ['required', new Enum(TypeSanction::class)],
            'date_sanction' => ['required', 'date'],
            'motif'         => ['required', 'string', 'max:2000'],
            'document'      => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png,doc,docx', 'max:10240'],
        ];
    }

    public function attributes(): array
    {
        return ['employe_id' => 'employé', 'date_sanction' => 'date'];
    }
}
