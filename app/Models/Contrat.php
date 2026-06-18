<?php

namespace App\Models;

use App\Models\Concerns\BelongsToFiliale;
use App\Models\Enums\StatutContrat;
use App\Models\Enums\TypeContrat;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Contrat extends Model
{
    use BelongsToFiliale, SoftDeletes, LogsActivity;

    protected $fillable = [
        'filiale_id', 'employe_id', 'reference', 'type_contrat', 'date_debut',
        'date_fin', 'salaire_base', 'devise', 'statut', 'fichier_contrat', 'observations',
    ];

    protected $casts = [
        'date_debut' => 'date',
        'date_fin' => 'date',
        'salaire_base' => 'decimal:2',
        'type_contrat' => TypeContrat::class,
        'statut' => StatutContrat::class,
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['type_contrat', 'date_debut', 'date_fin', 'salaire_base', 'statut'])
            ->logOnlyDirty();
    }

    public function employe(): BelongsTo { return $this->belongsTo(Employe::class); }

    public function scopeActifs($query) { return $query->where('statut', StatutContrat::ACTIF->value); }

    public function scopeExpirantAvant($query, $date)
    {
        return $query->where('statut', StatutContrat::ACTIF->value)
            ->whereNotNull('date_fin')
            ->whereDate('date_fin', '<=', $date);
    }
}
