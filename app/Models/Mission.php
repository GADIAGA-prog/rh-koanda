<?php

namespace App\Models;

use App\Models\Concerns\BelongsToFiliale;
use App\Models\Enums\StatutMission;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Mission extends Model
{
    use BelongsToFiliale, SoftDeletes, LogsActivity;

    protected $fillable = [
        'filiale_id', 'employe_id', 'objet', 'destination', 'lieu_depart',
        'date_depart', 'date_retour', 'nombre_jours', 'moyen_transport',
        'indemnite_journaliere', 'autres_frais', 'montant_total', 'devise',
        'statut', 'validateur_id', 'valide_le', 'motif_refus', 'observations',
    ];

    protected $casts = [
        'date_depart' => 'date',
        'date_retour' => 'date',
        'valide_le' => 'datetime',
        'indemnite_journaliere' => 'decimal:2',
        'autres_frais' => 'decimal:2',
        'montant_total' => 'decimal:2',
        'statut' => StatutMission::class,
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['objet', 'destination', 'montant_total', 'statut'])
            ->logOnlyDirty();
    }

    public function employe(): BelongsTo { return $this->belongsTo(Employe::class); }
    public function validateur(): BelongsTo { return $this->belongsTo(User::class, 'validateur_id'); }

    public function estModifiable(): bool
    {
        return in_array($this->statut, [StatutMission::BROUILLON, StatutMission::REFUSEE], true);
    }
}
