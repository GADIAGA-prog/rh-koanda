<?php

namespace App\Models\Enums;

enum StatutFormation: string
{
    case PLANIFIEE = 'planifiee';
    case EN_COURS = 'en_cours';
    case TERMINEE = 'terminee';
    case ANNULEE = 'annulee';

    public function libelle(): string
    {
        return match ($this) {
            self::PLANIFIEE => 'Planifiée',
            self::EN_COURS => 'En cours',
            self::TERMINEE => 'Terminée',
            self::ANNULEE => 'Annulée',
        };
    }

    public function couleur(): string
    {
        return match ($this) {
            self::PLANIFIEE => 'sky',
            self::EN_COURS => 'amber',
            self::TERMINEE => 'emerald',
            self::ANNULEE => 'rose',
        };
    }
}
