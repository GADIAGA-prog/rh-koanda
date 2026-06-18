<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Notifications\ContratExpirantNotification;
use App\Services\ContratService;
use Illuminate\Console\Command;

class AlerteContratsExpirants extends Command
{
    protected $signature = 'rh:contrats-expirants {--jours=30}';
    protected $description = 'Notifie les RH des contrats arrivant à échéance';

    public function handle(ContratService $service): int
    {
        $jours = (int) $this->option('jours');
        $contrats = $service->expirantDansJours($jours);

        if ($contrats->isEmpty()) {
            $this->info('Aucun contrat à échéance dans les ' . $jours . ' jours.');
            return self::SUCCESS;
        }

        // Notifie le DRH Groupe et le RH de chaque filiale concernée.
        foreach ($contrats->groupBy('filiale_id') as $filialeId => $lot) {
            $destinataires = User::where('filiale_id', $filialeId)
                ->role(['rh-filiale', 'drh-groupe'])
                ->get();

            foreach ($destinataires as $user) {
                $user->notify(new ContratExpirantNotification($lot));
            }
        }

        $this->info($contrats->count() . ' contrat(s) signalé(s).');
        return self::SUCCESS;
    }
}
