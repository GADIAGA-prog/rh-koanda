<?php

namespace App\Models;

use App\Models\Enums\TypeRubrique;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LigneBulletin extends Model
{
    protected $table = 'lignes_bulletin';

    protected $fillable = ['bulletin_paie_id', 'libelle', 'type', 'base', 'taux', 'montant', 'ordre'];

    protected $casts = [
        'base' => 'decimal:2',
        'taux' => 'decimal:4',
        'montant' => 'decimal:2',
        'type' => TypeRubrique::class,
    ];

    public function bulletin(): BelongsTo { return $this->belongsTo(BulletinPaie::class, 'bulletin_paie_id'); }
}
