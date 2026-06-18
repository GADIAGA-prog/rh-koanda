<?php

namespace App\Services;

use App\Models\Employe;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

/**
 * Logique métier de l'administration des comptes :
 * création, changement de rôle, (dés)activation, réinitialisation du mot de
 * passe, gestion des filiales gérées et du lien optionnel vers une fiche employé.
 */
class UtilisateurService
{
    public function creer(array $donnees): User
    {
        return DB::transaction(function () use ($donnees) {
            $user = User::create([
                'name' => $donnees['name'],
                'email' => $donnees['email'],
                'password' => Hash::make($donnees['password']),
                'filiale_id' => $donnees['filiale_id'] ?? null,
                'actif' => $donnees['actif'] ?? true,
            ]);

            $user->syncRoles([$donnees['role']]);
            $this->synchroniserFiliales($user, $donnees['filiales_gerees'] ?? []);
            $this->lierEmploye($user, $donnees['employe_id'] ?? null);

            return $user;
        });
    }

    public function modifier(User $user, array $donnees): User
    {
        return DB::transaction(function () use ($user, $donnees) {
            $user->update([
                'name' => $donnees['name'],
                'email' => $donnees['email'],
                'filiale_id' => $donnees['filiale_id'] ?? null,
            ]);

            if (! empty($donnees['role'])) {
                $user->syncRoles([$donnees['role']]);
            }

            $this->synchroniserFiliales($user, $donnees['filiales_gerees'] ?? []);
            $this->lierEmploye($user, $donnees['employe_id'] ?? null);

            return $user->refresh();
        });
    }

    /** Active ou désactive le compte ; renvoie le nouvel état. */
    public function basculerActivation(User $user): bool
    {
        $user->actif = ! $user->actif;
        $user->save();

        return $user->actif;
    }

    public function reinitialiserMotDePasse(User $user, string $nouveauMotDePasse): void
    {
        $user->update(['password' => Hash::make($nouveauMotDePasse)]);
    }

    public function supprimer(User $user): void
    {
        DB::transaction(function () use ($user) {
            // On délie la fiche employé et les filiales avant suppression.
            Employe::sansFiltreFiliale()->where('user_id', $user->id)->update(['user_id' => null]);
            $user->filialesGerees()->detach();
            $user->delete();
        });
    }

    /** Filiales supplémentaires gérées (RH multi-filiales) via la table pivot. */
    protected function synchroniserFiliales(User $user, array $filialeIds): void
    {
        $user->filialesGerees()->sync($filialeIds);
    }

    /** Lien optionnel 1-1 vers une fiche employé (employes.user_id). */
    protected function lierEmploye(User $user, ?int $employeId): void
    {
        // On détache l'éventuel ancien lien (le scope filiale est désactivé : opération Groupe).
        Employe::sansFiltreFiliale()->where('user_id', $user->id)->update(['user_id' => null]);

        if ($employeId) {
            Employe::sansFiltreFiliale()->whereKey($employeId)->update(['user_id' => $user->id]);
        }
    }
}
