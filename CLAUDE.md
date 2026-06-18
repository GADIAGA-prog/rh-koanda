# Règles du projet — RH Koanda Groupe

Application RH **multi-filiales** pour Koanda Groupe (6 filiales : GCMI, GCMIMMO,
FASOEN, ECOOIL, ECOFOOD, AMKO). Stack : Laravel 11 · Blade + Tailwind · MySQL ·
Spatie Permission · Spatie Activitylog.

## Principe central : isolation des données par filiale

C'est l'invariant le plus important du projet. **Ne jamais le contourner sans raison explicite.**

- Tout modèle « métier » qui appartient à une filiale utilise le trait
  `App\Models\Concerns\BelongsToFiliale`. Il ajoute le `FilialeScope` (filtrage
  automatique en lecture) et affecte `filiale_id` à la création.
- Le scope **cache des lignes** ; il ne remplace pas l'autorisation. Les **Policies**
  décident des actions permises. Les deux sont nécessaires.
- Les rôles Groupe (`super-admin`, `direction-generale`, `drh-groupe`,
  `auditeur-groupe`) voient tout le groupe ; les autres sont limités à leurs filiales.
- Pour un besoin consolidé (tableau de bord, rapport, job), désactiver le scope
  **explicitement** : `Model::sansFiltreFiliale()` ou `withoutGlobalScope(FilialeScope::class)`.

## Conventions de code

1. **Logique métier dans les Services** (`app/Services`), pas dans les contrôleurs.
   Un contrôleur orchestre et reste mince (≈ 15 lignes/méthode max).
2. **Validation dans les Form Requests** (`app/Http/Requests`), jamais inline.
3. **Autorisation dans les Policies** (`app/Policies`). Pas de `if ($user->role === …)`
   dans les vues ni les contrôleurs ; utiliser `can()` / `@can`.
4. **Enums PHP** (`app/Models/Enums`) pour toute valeur fixe (statuts, types).
5. **Soft deletes** sur les données sensibles : employés, contrats, documents, sanctions.
6. **Audit** via Activitylog (`LogsActivity`) sur Employe, Contrat, Conge, Sanction.
7. **Nommage métier en français** (Filiale, Conge, Affectation…).
8. **Vues** : Tailwind, responsive, sobres. Layout `layouts.app` + sidebar.

## Règle de sécurité à ne pas casser

> Un RH de la filiale A ne doit **jamais** accéder à un employé de la filiale B.

Tout nouveau modèle scoppé doit :
- porter une colonne `filiale_id` indexée ;
- utiliser le trait `BelongsToFiliale` ;
- être couvert par une Policy si des actions y sont rattachées.

Avant de livrer une fonctionnalité, vérifier que ce test reste vrai.

## Tâches planifiées

Définies dans `routes/console.php` :
- `rh:contrats-expirants` (quotidien 07:00) — notifie les RH.
- `rh:expirer-contrats` (quotidien 00:30) — passe les CDD échus à « expiré ».

## À faire après installation (voir README)

- Enregistrer l'alias middleware `filiale.active` dans `bootstrap/app.php`.
- Installer Breeze pour l'authentification (`routes/auth.php`, vues de login, logout).
- Ajuster le droit de congé annuel (30 j par défaut) à la convention applicable.
