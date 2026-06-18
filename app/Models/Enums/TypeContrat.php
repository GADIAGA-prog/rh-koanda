<?php

namespace App\Models\Enums;

enum TypeContrat: string
{
    case CDI = 'cdi';
    case CDD = 'cdd';
    case STAGE = 'stage';
    case CONSULTANT = 'consultant';
    case PRESTATAIRE = 'prestataire';

    public function libelle(): string
    {
        return match ($this) {
            self::CDI => 'CDI',
            self::CDD => 'CDD',
            self::STAGE => 'Stage',
            self::CONSULTANT => 'Consultant',
            self::PRESTATAIRE => 'Prestataire',
        };
    }
}
