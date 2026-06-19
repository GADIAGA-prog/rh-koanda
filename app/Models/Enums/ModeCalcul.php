<?php

namespace App\Models\Enums;

enum ModeCalcul: string
{
    case FIXE = 'fixe';
    case POURCENTAGE = 'pourcentage';

    public function libelle(): string
    {
        return match ($this) {
            self::FIXE => 'Montant fixe',
            self::POURCENTAGE => 'Pourcentage',
        };
    }
}
