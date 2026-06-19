<?php

namespace App\Models\Enums;

enum TypeRubrique: string
{
    case GAIN = 'gain';
    case RETENUE = 'retenue';
    case COTISATION = 'cotisation';

    public function libelle(): string
    {
        return match ($this) {
            self::GAIN => 'Gain',
            self::RETENUE => 'Retenue',
            self::COTISATION => 'Cotisation',
        };
    }

    public function couleur(): string
    {
        return match ($this) {
            self::GAIN => 'emerald',
            self::RETENUE => 'rose',
            self::COTISATION => 'amber',
        };
    }
}
