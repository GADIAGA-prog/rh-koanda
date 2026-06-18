<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Competence extends Model
{
    protected $fillable = ['libelle', 'domaine'];

    public function employes(): BelongsToMany
    {
        return $this->belongsToMany(Employe::class, 'competence_employe')
            ->withPivot('niveau')->withTimestamps();
    }
}
