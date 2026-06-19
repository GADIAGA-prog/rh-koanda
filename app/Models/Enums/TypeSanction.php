<?php

namespace App\Models\Enums;

enum TypeSanction: string
{
    case DEMANDE_EXPLICATION = 'demande_explication';
    case AVERTISSEMENT = 'avertissement';
    case BLAME = 'blame';
    case MISE_A_PIED = 'mise_a_pied';
    case LICENCIEMENT = 'licenciement';

    public function libelle(): string
    {
        return match ($this) {
            self::DEMANDE_EXPLICATION => "Demande d'explication",
            self::AVERTISSEMENT => 'Avertissement',
            self::BLAME => 'Blâme',
            self::MISE_A_PIED => 'Mise à pied',
            self::LICENCIEMENT => 'Licenciement',
        };
    }

    public function couleur(): string
    {
        return match ($this) {
            self::DEMANDE_EXPLICATION => 'sky',
            self::AVERTISSEMENT => 'amber',
            self::BLAME => 'orange',
            self::MISE_A_PIED => 'rose',
            self::LICENCIEMENT => 'rose',
        };
    }
}
