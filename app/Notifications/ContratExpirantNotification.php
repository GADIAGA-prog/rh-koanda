<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Collection;

class ContratExpirantNotification extends Notification
{
    use Queueable;

    public function __construct(public Collection $contrats) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $mail = (new MailMessage)
            ->subject('Contrats arrivant à échéance')
            ->line('Les contrats suivants arrivent à échéance :');

        foreach ($this->contrats as $c) {
            $mail->line("- {$c->employe->nom_complet} ({$c->filiale->nom}) : fin le " . $c->date_fin->format('d/m/Y'));
        }

        return $mail->action('Voir les contrats', url('/employes'));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'titre' => 'Contrats à renouveler',
            'nombre' => $this->contrats->count(),
            'message' => $this->contrats->count() . ' contrat(s) arrivent à échéance.',
        ];
    }
}
