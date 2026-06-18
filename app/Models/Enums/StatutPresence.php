<?php

namespace App\Models\Enums;

enum StatutPresence: string
{
    case PRESENT = 'present';
    case RETARD = 'retard';
    case ABSENT = 'absent';
    case CONGE = 'conge';

    public function libelle(): string
    {
        return ucfirst($this->value);
    }
}
