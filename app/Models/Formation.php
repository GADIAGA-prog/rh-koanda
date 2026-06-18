<?php

namespace App\Models;

use App\Models\Concerns\BelongsToFiliale;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Formation extends Model
{
    use BelongsToFiliale;

    protected $fillable = [
        'filiale_id', 'intitule', 'objectif', 'organisme',
        'date_debut', 'date_fin', 'cout', 'devise', 'statut',
    ];

    protected $casts = [
        'date_debut' => 'date',
        'date_fin' => 'date',
        'cout' => 'decimal:2',
    ];

    public function participants(): BelongsToMany
    {
        return $this->belongsToMany(Employe::class, 'formation_employe')
            ->withPivot('present', 'resultat')->withTimestamps();
    }
}
