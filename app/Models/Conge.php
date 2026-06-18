<?php

namespace App\Models;

use App\Models\Concerns\BelongsToFiliale;
use App\Models\Enums\StatutConge;
use App\Models\Enums\TypeConge;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Conge extends Model
{
    use BelongsToFiliale, LogsActivity;

    protected $fillable = [
        'filiale_id', 'employe_id', 'type_conge', 'date_debut', 'date_fin',
        'nombre_jours', 'motif', 'statut_validation', 'validateur_id', 'valide_le', 'motif_refus',
    ];

    protected $casts = [
        'date_debut' => 'date',
        'date_fin' => 'date',
        'valide_le' => 'datetime',
        'nombre_jours' => 'decimal:1',
        'type_conge' => TypeConge::class,
        'statut_validation' => StatutConge::class,
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['type_conge', 'date_debut', 'date_fin', 'statut_validation'])
            ->logOnlyDirty();
    }

    public function employe(): BelongsTo { return $this->belongsTo(Employe::class); }
    public function validateur(): BelongsTo { return $this->belongsTo(User::class, 'validateur_id'); }

    public function scopeEnAttente($query)
    {
        return $query->where('statut_validation', StatutConge::EN_ATTENTE->value);
    }
}
