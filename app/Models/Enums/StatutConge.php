<?php

namespace App\Models\Enums;

enum StatutConge: string
{
    case EN_ATTENTE = 'en_attente';
    case VALIDE = 'valide';
    case REFUSE = 'refuse';
    case ANNULE = 'annule';

    public function libelle(): string
    {
        return match ($this) {
            self::EN_ATTENTE => 'En attente',
            self::VALIDE => 'Validé',
            self::REFUSE => 'Refusé',
            self::ANNULE => 'Annulé',
        };
    }

    public function couleur(): string
    {
        return match ($this) {
            self::EN_ATTENTE => 'amber',
            self::VALIDE => 'emerald',
            self::REFUSE => 'rose',
            self::ANNULE => 'slate',
        };
    }
}
