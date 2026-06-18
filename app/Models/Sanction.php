<?php

namespace App\Models;

use App\Models\Concerns\BelongsToFiliale;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sanction extends Model
{
    use BelongsToFiliale, SoftDeletes;

    protected $fillable = [
        'filiale_id', 'employe_id', 'type', 'date_sanction',
        'motif', 'document', 'prononce_par',
    ];

    protected $casts = ['date_sanction' => 'date'];

    public function employe(): BelongsTo { return $this->belongsTo(Employe::class); }
}
