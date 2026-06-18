<?php

namespace App\Models;

use App\Models\Concerns\BelongsToFiliale;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EvaluationPerformance extends Model
{
    use BelongsToFiliale;

    protected $table = 'evaluations_performance';

    protected $fillable = [
        'filiale_id', 'employe_id', 'evaluateur_id', 'periode',
        'objectifs', 'note_globale', 'commentaire', 'prime_proposee',
    ];

    protected $casts = [
        'note_globale' => 'decimal:2',
        'prime_proposee' => 'decimal:2',
    ];

    public function employe(): BelongsTo { return $this->belongsTo(Employe::class); }
    public function evaluateur(): BelongsTo { return $this->belongsTo(User::class, 'evaluateur_id'); }
}
