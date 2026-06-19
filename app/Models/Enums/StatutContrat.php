<?php

namespace App\Models\Enums;

enum StatutContrat: string
{
    case ACTIF = 'actif';
    case EXPIRE = 'expire';
    case RESILIE = 'resilie';
    case SUSPENDU = 'suspendu';

    public function libelle(): string
    {
        return match ($this) {
            self::ACTIF => 'Actif',
            self::EXPIRE => 'Expiré',
            self::RESILIE => 'Résilié',
            self::SUSPENDU => 'Suspendu',
        };
    }

    public function couleur(): string
    {
        return match ($this) {
            self::ACTIF => 'emerald',
            self::EXPIRE => 'amber',
            self::RESILIE => 'rose',
            self::SUSPENDU => 'slate',
        };
    }
}
