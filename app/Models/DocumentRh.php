<?php

namespace App\Models;

use App\Models\Concerns\BelongsToFiliale;
use App\Models\Enums\Confidentialite;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class DocumentRh extends Model
{
    use BelongsToFiliale, SoftDeletes;

    protected $table = 'documents_rh';

    protected $fillable = [
        'filiale_id', 'employe_id', 'type_document', 'titre',
        'fichier', 'date_expiration', 'confidentialite', 'ajoute_par',
    ];

    protected $casts = [
        'date_expiration' => 'date',
        'confidentialite' => Confidentialite::class,
    ];

    public function employe(): BelongsTo { return $this->belongsTo(Employe::class); }

    public function scopeExpirantAvant($query, $date)
    {
        return $query->whereNotNull('date_expiration')->whereDate('date_expiration', '<=', $date);
    }
}
