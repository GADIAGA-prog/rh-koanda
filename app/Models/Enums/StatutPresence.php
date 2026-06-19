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
        return match ($this) {
            self::PRESENT => 'Présent',
            self::RETARD => 'Retard',
            self::ABSENT => 'Absent',
            self::CONGE => 'Congé',
        };
    }

    public function couleur(): string
    {
        return match ($this) {
            self::PRESENT => 'emerald',
            self::RETARD => 'amber',
            self::ABSENT => 'rose',
            self::CONGE => 'sky',
        };
    }

    /** Abréviation pour les cellules du tableau mensuel. */
    public function abrege(): string
    {
        return match ($this) {
            self::PRESENT => 'P',
            self::RETARD => 'R',
            self::ABSENT => 'A',
            self::CONGE => 'C',
        };
    }
}
