<?php

namespace App\Models;

use App\Models\Concerns\BelongsToFiliale;
use App\Models\Enums\TypeSanction;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Sanction extends Model
{
    use BelongsToFiliale, SoftDeletes, LogsActivity;

    protected $fillable = [
        'filiale_id', 'employe_id', 'type', 'date_sanction',
        'motif', 'document', 'prononce_par',
    ];

    protected $casts = [
        'date_sanction' => 'date',
        'type' => TypeSanction::class,
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['type', 'date_sanction', 'employe_id'])
            ->logOnlyDirty();
    }

    public function employe(): BelongsTo { return $this->belongsTo(Employe::class); }
    public function auteur(): BelongsTo { return $this->belongsTo(User::class, 'prononce_par'); }
}
