<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Affectation extends Model
{
    protected $fillable = [
        'employe_id', 'ancienne_filiale_id', 'nouvelle_filiale_id',
        'ancien_poste_id', 'nouveau_poste_id', 'date_effet', 'motif', 'decide_par',
    ];

    protected $casts = ['date_effet' => 'date'];

    public function employe(): BelongsTo { return $this->belongsTo(Employe::class); }
}
