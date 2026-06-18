<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Departement extends Model
{
    protected $fillable = ['filiale_id', 'site_id', 'nom', 'code'];

    public function filiale(): BelongsTo { return $this->belongsTo(Filiale::class); }
    public function site(): BelongsTo { return $this->belongsTo(Site::class); }
    public function postes(): HasMany { return $this->hasMany(Poste::class); }
    public function employes(): HasMany { return $this->hasMany(Employe::class); }
}
