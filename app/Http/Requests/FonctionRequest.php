<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FonctionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('organisation.create') || $this->user()->can('organisation.update');
    }

    public function rules(): array
    {
        return [
            'libelle'     => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
