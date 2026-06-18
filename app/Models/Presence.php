<?php

namespace App\Models;

use App\Models\Concerns\BelongsToFiliale;
use App\Models\Enums\StatutPresence;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Presence extends Model
{
    use BelongsToFiliale;

    protected $fillable = [
        'filiale_id', 'employe_id', 'date_presence',
        'heure_arrivee', 'heure_depart', 'statut', 'commentaire',
    ];

    protected $casts = [
        'date_presence' => 'date',
        'statut' => StatutPresence::class,
    ];

    public function employe(): BelongsTo { return $this->belongsTo(Employe::class); }
}
