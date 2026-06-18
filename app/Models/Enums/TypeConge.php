<?php

namespace App\Models\Enums;

enum TypeConge: string
{
    case ANNUEL = 'annuel';
    case MALADIE = 'maladie';
    case MATERNITE = 'maternite';
    case PATERNITE = 'paternite';
    case EXCEPTIONNEL = 'exceptionnel';
    case SANS_SOLDE = 'sans_solde';

    public function libelle(): string
    {
        return match ($this) {
            self::ANNUEL => 'Congé annuel',
            self::MALADIE => 'Congé maladie',
            self::MATERNITE => 'Maternité',
            self::PATERNITE => 'Paternité',
            self::EXCEPTIONNEL => 'Exceptionnel',
            self::SANS_SOLDE => 'Sans solde',
        };
    }
}
