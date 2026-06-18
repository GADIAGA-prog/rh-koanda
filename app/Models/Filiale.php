<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Filiale extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'code', 'nom', 'domaine', 'pays', 'ville', 'adresse',
        'telephone', 'email', 'responsable_id', 'statut',
    ];

    protected $casts = ['statut' => 'boolean'];

    public function sites(): HasMany { return $this->hasMany(Site::class); }
    public function departements(): HasMany { return $this->hasMany(Departement::class); }
    public function postes(): HasMany { return $this->hasMany(Poste::class); }
    public function employes(): HasMany { return $this->hasMany(Employe::class); }
    public function contrats(): HasMany { return $this->hasMany(Contrat::class); }

    public function responsable(): BelongsTo
    {
        return $this->belongsTo(Employe::class, 'responsable_id');
    }
}
