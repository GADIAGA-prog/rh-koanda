<?php

namespace App\Models\Enums;

enum StatutMission: string
{
    case BROUILLON = 'brouillon';
    case SOUMISE = 'soumise';
    case VALIDEE = 'validee';
    case REFUSEE = 'refusee';
    case CLOTUREE = 'cloturee';

    public function libelle(): string
    {
        return match ($this) {
            self::BROUILLON => 'Brouillon',
            self::SOUMISE => 'Soumise',
            self::VALIDEE => 'Validée',
            self::REFUSEE => 'Refusée',
            self::CLOTUREE => 'Clôturée',
        };
    }

    public function couleur(): string
    {
        return match ($this) {
            self::BROUILLON => 'slate',
            self::SOUMISE => 'amber',
            self::VALIDEE => 'emerald',
            self::REFUSEE => 'rose',
            self::CLOTUREE => 'sky',
        };
    }
}
