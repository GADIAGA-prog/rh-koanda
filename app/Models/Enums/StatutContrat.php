<?php

namespace App\Models\Enums;

enum StatutContrat: string
{
    case ACTIF = 'actif';
    case EXPIRE = 'expire';
    case RESILIE = 'resilie';
    case SUSPENDU = 'suspendu';

    public function libelle(): string
    {
        return ucfirst($this->value);
    }
}
