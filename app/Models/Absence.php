<?php

namespace App\Models;

use App\Models\Concerns\BelongsToFiliale;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Absence extends Model
{
    use BelongsToFiliale;

    protected $fillable = [
        'filiale_id', 'employe_id', 'date_debut', 'date_fin',
        'justifiee', 'motif', 'justificatif',
    ];

    protected $casts = [
        'date_debut' => 'date',
        'date_fin' => 'date',
        'justifiee' => 'boolean',
    ];

    public function employe(): BelongsTo { return $this->belongsTo(Employe::class); }
}
