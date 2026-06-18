<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use Notifiable, HasRoles;

    protected $fillable = [
        'name', 'email', 'password', 'filiale_id', 'actif',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'actif' => 'boolean',
        ];
    }

    /** Rôles ayant une visibilité sur l'ensemble du groupe. */
    public const ROLES_GROUPE = ['super-admin', 'direction-generale', 'drh-groupe', 'auditeur-groupe'];

    public function peutVoirToutLeGroupe(): bool
    {
        return $this->hasAnyRole(self::ROLES_GROUPE);
    }

    /** Liste des IDs de filiales que l'utilisateur peut consulter. */
    public function filialesAccessibles(): array
    {
        return $this->filialesGerees->pluck('id')
            ->push($this->filiale_id)
            ->filter()
            ->unique()
            ->values()
            ->all();
    }

    public function filiale(): BelongsTo
    {
        return $this->belongsTo(Filiale::class);
    }

    public function filialesGerees(): BelongsToMany
    {
        return $this->belongsToMany(Filiale::class, 'filiale_user');
    }

    public function employe(): HasOne
    {
        return $this->hasOne(Employe::class);
    }
}
