<?php

namespace App\Models;

use App\Models\Concerns\BelongsToFiliale;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Poste extends Model
{
    use BelongsToFiliale;

    protected $fillable = ['filiale_id', 'departement_id', 'intitule', 'categorie'];

    public function departement(): BelongsTo { return $this->belongsTo(Departement::class); }
    public function employes(): HasMany { return $this->hasMany(Employe::class); }
}
