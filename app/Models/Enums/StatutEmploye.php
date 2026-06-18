<?php

namespace App\Models\Enums;

enum StatutEmploye: string
{
    case ACTIF = 'actif';
    case SUSPENDU = 'suspendu';
    case DEPART = 'depart';
    case CONGE = 'conge';

    public function libelle(): string
    {
        return match ($this) {
            self::ACTIF => 'Actif',
            self::SUSPENDU => 'Suspendu',
            self::DEPART => 'Parti',
            self::CONGE => 'En congé',
        };
    }

    public function couleur(): string
    {
        return match ($this) {
            self::ACTIF => 'emerald',
            self::SUSPENDU => 'amber',
            self::DEPART => 'slate',
            self::CONGE => 'sky',
        };
    }
}
