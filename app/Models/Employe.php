<?php

namespace App\Models;

use App\Models\Concerns\BelongsToFiliale;
use App\Models\Enums\StatutEmploye;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Employe extends Model
{
    use BelongsToFiliale, SoftDeletes, LogsActivity;

    protected $fillable = [
        'matricule', 'nom', 'prenom', 'sexe', 'date_naissance', 'lieu_naissance',
        'cnib', 'telephone', 'email', 'adresse', 'situation_familiale', 'nombre_enfants',
        'filiale_id', 'site_id', 'departement_id', 'poste_id', 'manager_id', 'user_id',
        'date_embauche', 'photo', 'statut',
    ];

    protected $casts = [
        'date_naissance' => 'date',
        'date_embauche' => 'date',
        'statut' => StatutEmploye::class,
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['nom', 'prenom', 'poste_id', 'departement_id', 'statut', 'filiale_id'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    // Accesseurs ---------------------------------------------------------
    public function getNomCompletAttribute(): string
    {
        return trim("{$this->prenom} {$this->nom}");
    }

    public function getInitialesAttribute(): string
    {
        return strtoupper(mb_substr($this->prenom, 0, 1) . mb_substr($this->nom, 0, 1));
    }

    // Relations ----------------------------------------------------------
    public function site(): BelongsTo { return $this->belongsTo(Site::class); }
    public function departement(): BelongsTo { return $this->belongsTo(Departement::class); }
    public function poste(): BelongsTo { return $this->belongsTo(Poste::class); }
    public function manager(): BelongsTo { return $this->belongsTo(Employe::class, 'manager_id'); }
    public function user(): BelongsTo { return $this->belongsTo(User::class); }

    public function subordonnes(): HasMany { return $this->hasMany(Employe::class, 'manager_id'); }
    public function contrats(): HasMany { return $this->hasMany(Contrat::class); }
    public function conges(): HasMany { return $this->hasMany(Conge::class); }
    public function presences(): HasMany { return $this->hasMany(Presence::class); }
    public function absences(): HasMany { return $this->hasMany(Absence::class); }
    public function documents(): HasMany { return $this->hasMany(DocumentRh::class); }
    public function soldesConges(): HasMany { return $this->hasMany(SoldeConge::class); }
    public function evaluations(): HasMany { return $this->hasMany(EvaluationPerformance::class); }
    public function sanctions(): HasMany { return $this->hasMany(Sanction::class); }

    public function contratActif(): ?Contrat
    {
        return $this->contrats()->where('statut', 'actif')->latest('date_debut')->first();
    }

    // Scopes de requête --------------------------------------------------
    public function scopeActifs($query) { return $query->where('statut', StatutEmploye::ACTIF->value); }

    public function scopeRecherche($query, ?string $terme)
    {
        if (! $terme) return $query;
        return $query->where(function ($q) use ($terme) {
            $q->where('nom', 'like', "%{$terme}%")
              ->orWhere('prenom', 'like', "%{$terme}%")
              ->orWhere('matricule', 'like', "%{$terme}%")
              ->orWhere('email', 'like', "%{$terme}%");
        });
    }
}
