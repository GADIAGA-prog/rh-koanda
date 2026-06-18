<?php

namespace App\Models\Enums;

enum Confidentialite: string
{
    case PUBLIC = 'public';
    case RH = 'rh';
    case DIRECTION = 'direction';

    public function libelle(): string
    {
        return match ($this) {
            self::PUBLIC => 'Public',
            self::RH => 'Réservé RH',
            self::DIRECTION => 'Réservé Direction',
        };
    }
}
