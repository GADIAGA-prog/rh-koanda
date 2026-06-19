<?php

namespace App\Models\Enums;

enum StatutBulletin: string
{
    case BROUILLON = 'brouillon';
    case VALIDE = 'valide';
    case PAYE = 'paye';

    public function libelle(): string
    {
        return match ($this) {
            self::BROUILLON => 'Brouillon',
            self::VALIDE => 'Validé',
            self::PAYE => 'Payé',
        };
    }

    public function couleur(): string
    {
        return match ($this) {
            self::BROUILLON => 'slate',
            self::VALIDE => 'emerald',
            self::PAYE => 'sky',
        };
    }
}
