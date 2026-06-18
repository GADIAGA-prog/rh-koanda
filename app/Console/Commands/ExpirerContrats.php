<?php

namespace App\Console\Commands;

use App\Services\ContratService;
use Illuminate\Console\Command;

class ExpirerContrats extends Command
{
    protected $signature = 'rh:expirer-contrats';
    protected $description = 'Passe à « expiré » les contrats dont la date de fin est dépassée';

    public function handle(ContratService $service): int
    {
        $n = $service->expirerEcheances();
        $this->info("{$n} contrat(s) marqué(s) comme expiré(s).");
        return self::SUCCESS;
    }
}
