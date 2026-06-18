<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Site extends Model
{
    protected $fillable = ['filiale_id', 'nom', 'ville', 'adresse', 'statut'];
    protected $casts = ['statut' => 'boolean'];

    public function filiale(): BelongsTo { return $this->belongsTo(Filiale::class); }
    public function departements(): HasMany { return $this->hasMany(Departement::class); }
}
