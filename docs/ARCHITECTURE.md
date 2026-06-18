# Architecture applicative — Application RH Koanda Groupe

> Document de référence destiné à la racine du projet (`/docs/ARCHITECTURE.md`).
> À lire avant tout développement. Sert de cadre pour Claude Code et l'équipe.

**Projet :** Application RH privée, multi-sociétés et multi-filiales
**Filiales :** GCM Industries · GCM Immobilier · Faso Energy · Eco Oil · Eco Food · AMKO Trading SA
**Stack :** Laravel · Blade + Tailwind · MySQL/MariaDB · Spatie Permission

---

## 1. Principes directeurs

L'architecture repose sur cinq principes non négociables :

1. **Centralisation + autonomie contrôlée.** Le Groupe voit tout ; chaque filiale ne voit et ne gère que ses propres données. Cette frontière est garantie *par le code*, pas seulement par la discipline des utilisateurs.
2. **Logique métier hors des contrôleurs.** Toute règle RH (calcul de solde de congés, renouvellement de contrat, mutation) vit dans une couche **Service**. Les contrôleurs orchestrent, ils ne décident pas.
3. **Sécurité par défaut.** Une requête non scoppée doit retourner *les données de la filiale de l'utilisateur*, jamais tout le groupe. L'ouverture (voir tout) est l'exception explicite, pas l'inverse.
4. **Traçabilité des actions sensibles.** Création/modification/suppression sur les données RH critiques sont journalisées automatiquement.
5. **Réversibilité.** Les données sensibles utilisent le *soft delete* — rien n'est jamais perdu, tout reste auditable.

---

## 2. Architecture applicative en couches

```
┌─────────────────────────────────────────────────────────┐
│  PRÉSENTATION   Blade + Tailwind · Chart.js / ApexCharts │
├─────────────────────────────────────────────────────────┤
│  HTTP           Controllers (mince) · Form Requests       │
│                 Policies (autorisation) · Middleware       │
├─────────────────────────────────────────────────────────┤
│  MÉTIER         Services (EmployeService, CongeService…)   │
│                 Actions · Events / Listeners               │
├─────────────────────────────────────────────────────────┤
│  DONNÉES        Models Eloquent + Global Scopes            │
│                 Traits (BelongsToFiliale) · Observers      │
├─────────────────────────────────────────────────────────┤
│  PERSISTANCE    MySQL · Migrations · Soft deletes          │
└─────────────────────────────────────────────────────────┘
```

**Règle de flux :** une requête HTTP → Middleware (auth + filiale active) → Form Request (validation) → Controller (orchestration) → Policy (autorisation) → Service (logique) → Model (données scoppées) → Vue.

---

## 3. Stratégie multi-filiales — *le cœur du système*

C'est la décision architecturale la plus importante. Une erreur ici = fuite de données RH entre filiales.

### 3.1 Modèle retenu : base unique, données partagées, scoping automatique

On reste sur **une seule base de données**. Toutes les tables « métier sensible » portent une colonne `filiale_id`. Un **Global Scope Eloquent** filtre automatiquement chaque requête selon l'utilisateur connecté. C'est plus simple à maintenir et à consolider (tableaux de bord Groupe) qu'une base par filiale, tout en restant sûr.

> Alternative écartée : une base par filiale (vraie multi-tenancy). Plus lourde, complique fortement les rapports consolidés que demande la Direction Générale. Inutile à votre échelle.

### 3.2 Qui voit quoi

| Rôle | Périmètre de données |
|------|----------------------|
| Super Admin Groupe | Toutes les filiales (scope désactivé) |
| Direction Générale | Toutes les filiales (lecture) |
| DRH Groupe | Toutes les filiales |
| RH Filiale | Sa/ses filiale(s) uniquement |
| Manager | Son équipe dans sa filiale |
| Employé | Son propre dossier |
| Auditeur | Lecture seule, périmètre autorisé |

### 3.3 Le trait `BelongsToFiliale`

Appliqué à tous les modèles scoppés (`Employe`, `Contrat`, `Presence`, `Absence`, `Conge`, `DocumentRh`, `EvaluationPerformance`, `Sanction`…) :

```php
// app/Models/Concerns/BelongsToFiliale.php
namespace App\Models\Concerns;

use App\Models\Filiale;
use App\Models\Scopes\FilialeScope;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait BelongsToFiliale
{
    protected static function bootBelongsToFiliale(): void
    {
        // 1. Filtrage automatique en lecture
        static::addGlobalScope(new FilialeScope);

        // 2. Affectation automatique de la filiale à la création
        static::creating(function ($model) {
            if (empty($model->filiale_id) && auth()->check()) {
                $model->filiale_id = auth()->user()->filiale_id;
            }
        });
    }

    public function filiale(): BelongsTo
    {
        return $this->belongsTo(Filiale::class);
    }
}
```

### 3.4 Le Global Scope

```php
// app/Models/Scopes/FilialeScope.php
namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class FilialeScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        $user = auth()->user();

        // Pas d'utilisateur (console, seeders, jobs) → pas de filtrage
        if (! $user) {
            return;
        }

        // Rôles Groupe → accès total, scope désactivé
        if ($user->peutVoirToutLeGroupe()) {
            return;
        }

        // Sinon : restreint aux filiales accessibles de l'utilisateur
        $builder->whereIn(
            $model->getTable().'.filiale_id',
            $user->filialesAccessibles()
        );
    }
}
```

### 3.5 Le modèle `User`

```php
// app/Models/User.php (extraits)

public function filiale(): BelongsTo
{
    return $this->belongsTo(Filiale::class);
}

// Multi-filiales : un RH peut en gérer plusieurs (pivot filiale_user)
public function filialesGerees(): BelongsToMany
{
    return $this->belongsToMany(Filiale::class, 'filiale_user');
}

public function peutVoirToutLeGroupe(): bool
{
    return $this->hasAnyRole([
        'super-admin', 'direction-generale', 'drh-groupe', 'auditeur-groupe',
    ]);
}

public function filialesAccessibles(): array
{
    // Filiale principale + filiales gérées via le pivot
    return $this->filialesGerees->pluck('id')
        ->push($this->filiale_id)
        ->filter()->unique()->values()->all();
}
```

### 3.6 Désactiver le scope quand c'est légitime

```php
// Tableau de bord Groupe, rapports consolidés, jobs planifiés :
Employe::withoutGlobalScope(FilialeScope::class)->count();
```

> **Règle d'or :** scoping (quelles *lignes*) ≠ autorisation (quelles *actions*). Le scope cache les données ; les Policies décident des droits. Les deux sont nécessaires et complémentaires.

---

## 4. Modèle de données

### 4.1 Domaines

```
ORGANISATION      filiales · sites · departements · postes · fonctions
PERSONNES         employes · contrats · affectations · organigrammes
TEMPS             presences · absences · conges · soldes_conges
DOCUMENTS         documents_rh · categories_documents · validations_documents
DÉVELOPPEMENT     formations · competences · evaluations_performance
DISCIPLINE        sanctions · incidents_rh · notes_rh
SYSTÈME           users · roles · permissions · audit_logs · notifications
```

### 4.2 Relations principales

```
Filiale 1───* Site 1───* Departement 1───* Poste
Filiale 1───* Employe
Employe  1───* Contrat        (historique de contrats)
Employe  1───* Affectation    (mutations entre filiales/postes)
Employe  *───1 Employe        (manager_id, auto-référence)
Employe  1───* Presence / Absence / Conge / DocumentRh
Employe  1───1 SoldeConge     (par type et par année)
```

### 4.3 Conventions de schéma

- Clé primaire `id` en `bigIncrements`, clés étrangères en `foreignId()->constrained()`.
- `filiale_id` **obligatoire et indexé** sur toute table scoppée.
- Index composés sur les colonnes filtrées ensemble : `(filiale_id, statut)`, `(employe_id, date_presence)`.
- `softDeletes()` sur : `employes`, `contrats`, `documents_rh`, `sanctions`.
- `timestamps()` partout. Montants en `decimal(15,2)` + colonne `devise` (XOF par défaut).
- Énumérations (`type_contrat`, `statut`, `confidentialite`) via colonnes `string` + classes PHP Enum, jamais d'ENUM SQL (rigide à migrer).

---

## 5. Sécurité et autorisations

### 5.1 Deux couches distinctes

1. **Spatie Permission** — rôles et permissions (`employe.create`, `conge.valider`, `contrat.update`…).
2. **Policies Laravel** — combinent permission + appartenance à la filiale + relation hiérarchique.

```php
// app/Policies/CongePolicy.php
public function valider(User $user, Conge $conge): bool
{
    // Permission ET (RH de la filiale OU manager de l'employé)
    return $user->can('conge.valider')
        && (
            in_array($conge->filiale_id, $user->filialesAccessibles())
            || $conge->employe->manager_id === $user->employe?->id
        );
}
```

### 5.2 Middleware « filiale active »

Pour les RH multi-filiales, un sélecteur de filiale active stocké en session, vérifié par middleware, évite les manipulations involontaires inter-filiales.

### 5.3 Confidentialité documentaire

Le champ `confidentialite` (`public` / `rh` / `direction`) sur `documents_rh` filtre l'accès aux pièces sensibles (sanctions, contrats, médical) même au sein d'une filiale.

---

## 6. Audit et traçabilité

Recommandation : **`spatie/laravel-activitylog`** plutôt qu'une table maison. Il alimente votre table d'audit automatiquement.

```php
class Employe extends Model
{
    use LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['nom', 'prenom', 'poste_id', 'statut', 'filiale_id'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
```

Capture automatiquement *qui*, *quoi*, *ancienne/nouvelle valeur*, *quand* — exactement les champs prévus dans `audit_logs`. À activer en priorité sur : `Employe`, `Contrat`, `Conge`, `Sanction`, `DocumentRh`.

---

## 7. Alertes et tâches planifiées

Les alertes RH (contrats à renouveler, documents expirés, congés en attente) ne sont **pas** du code dans les vues — ce sont des commandes planifiées qui génèrent des notifications.

```php
// app/Console/Kernel.php
$schedule->command('rh:contrats-expirants')->dailyAt('07:00');
$schedule->command('rh:documents-expires')->dailyAt('07:15');
$schedule->command('rh:conges-en-attente')->weekdays()->at('08:00');
```

Chaque commande tourne **sans le scope filiale** (`withoutGlobalScope`) pour balayer tout le groupe, puis notifie le bon RH selon la filiale concernée via les `Notifications` Laravel (base + e-mail).

---

## 8. Structure des dossiers

```
app/
├── Console/Commands/        rh:contrats-expirants, etc.
├── Http/
│   ├── Controllers/         Groupe/, Filiale/, Employe/…
│   ├── Requests/            StoreEmployeRequest, ValiderCongeRequest…
│   └── Middleware/          FilialeActive
├── Models/
│   ├── Concerns/            BelongsToFiliale
│   ├── Scopes/              FilialeScope
│   └── Enums/               TypeContrat, StatutConge…
├── Policies/                EmployePolicy, CongePolicy…
├── Services/                EmployeService, CongeService,
│                            ContratService, StatistiqueRhService
├── Notifications/           ContratExpirantNotification…
└── Support/                 helpers, calculs de dates ouvrées

database/
├── migrations/
├── seeders/                 FilialeSeeder, RolePermissionSeeder
└── factories/

resources/views/
├── layouts/                 app, sidebar par rôle
├── groupe/dashboard/        tableaux consolidés
├── filiale/dashboard/
└── employes/ contrats/ conges/ presences/…
```

---

## 9. Conventions de code

- **Form Requests** pour toute validation. Pas de validation dans les contrôleurs.
- **Services** pour la logique : un contrôleur ne dépasse pas ~15 lignes par méthode.
- **Policies** pour toute autorisation ; jamais de `if ($user->role === …)` dans les vues.
- **Enums PHP** pour les valeurs fixes.
- **Nommage FR** pour le métier (tables, modèles : `Filiale`, `Conge`, `Affectation`) — cohérent avec le cahier des charges.
- **Tests** au minimum sur le scoping multi-filiales : un RH de la filiale A ne doit jamais accéder à un employé de la filiale B. C'est le test de sécurité le plus important du projet.

---

## 10. Feuille de route par phases

| Phase | Contenu | Livrable de fin de phase |
|-------|---------|--------------------------|
| **0 — Socle** | Projet Laravel, MySQL, Spatie, `BelongsToFiliale`, `FilialeScope`, rôles/permissions, seeders des 6 filiales | Auth fonctionnelle + scoping testé |
| **1 — Organisation** | Filiales, Sites, Départements, Postes, Fonctions | CRUD organisation |
| **2 — Employés** | Dossier individuel complet, recherche, EmployeService | Module Employés opérationnel |
| **3 — Contrats & Documents** | Contrats + historique, documents RH + confidentialité, audit log activé | Gestion contractuelle |
| **4 — Temps** | Présences, absences, congés + soldes + validation | Workflow congés complet |
| **5 — Pilotage** | Tableau de bord Groupe + par filiale, StatistiqueRhService, Chart.js | Indicateurs consolidés |
| **6 — Exports & Alertes** | Excel/PDF, commandes planifiées, notifications | Rapports + alertes |
| **7 — RH avancé** | Performance, formation, discipline | Modules complémentaires |

**Point de vigilance de la Phase 0 :** ne pas avancer tant que le test « isolation inter-filiales » n'est pas vert. Tout le reste s'appuie dessus.

---

*Document d'architecture — à versionner avec le projet et à faire évoluer au fil des phases.*
