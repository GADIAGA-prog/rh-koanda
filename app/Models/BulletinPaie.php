<?php

namespace App\Models;

use App\Models\Concerns\BelongsToFiliale;
use App\Models\Enums\StatutBulletin;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class BulletinPaie extends Model
{
    use BelongsToFiliale, SoftDeletes;

    protected $table = 'bulletins_paie';

    protected $fillable = [
        'filiale_id', 'employe_id', 'periode', 'salaire_base', 'total_gains',
        'salaire_brut', 'total_cotisations', 'total_retenues', 'net_a_payer',
        'cout_employeur', 'statut',
    ];

    protected $casts = [
        'salaire_base' => 'decimal:2',
        'total_gains' => 'decimal:2',
        'salaire_brut' => 'decimal:2',
        'total_cotisations' => 'decimal:2',
        'total_retenues' => 'decimal:2',
        'net_a_payer' => 'decimal:2',
        'cout_employeur' => 'decimal:2',
        'statut' => StatutBulletin::class,
    ];

    public function employe(): BelongsTo { return $this->belongsTo(Employe::class); }
    public function lignes(): HasMany { return $this->hasMany(LigneBulletin::class)->orderBy('ordre'); }
}
