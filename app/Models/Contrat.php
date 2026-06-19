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

    /** Contrats actifs à échéance entre aujourd'hui et N jours. */
    public function scopeARenouveler($query, int $jours = 30)
    {
        return $query->expirantAvant(now()->addDays($jours))
            ->whereDate('date_fin', '>=', today());
    }

    public function scopeRecherche($query, ?string $terme)
    {
        if (! $terme) return $query;
        return $query->where(function ($q) use ($terme) {
            $q->where('reference', 'like', "%{$terme}%")
              ->orWhereHas('employe', fn ($e) => $e->recherche($terme));
        });
    }

    /** Nombre de jours avant l'échéance (négatif si déjà dépassée). */
    public function getJoursAvantEcheanceAttribute(): ?int
    {
        return $this->date_fin ? today()->diffInDays($this->date_fin, false) : null;
    }

    /** Le contrat est-il actif et proche de son échéance ? */
    public function aRenouveler(int $jours = 30): bool
    {
        return $this->statut === StatutContrat::ACTIF
            && $this->date_fin !== null
            && $this->jours_avant_echeance >= 0
            && $this->jours_avant_echeance <= $jours;
    }
}
