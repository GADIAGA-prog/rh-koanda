# RH Koanda Groupe

Système de gestion des ressources humaines **multi-filiales** pour Koanda Groupe.
Contrôle centralisé Groupe + autonomie contrôlée par filiale.

> ⚠️ Ce dossier contient le **code source applicatif** (modèles, migrations, services,
> contrôleurs, vues, seeders). Il se greffe sur un projet Laravel 11 neuf. Les
> dépendances (`composer install`) s'installent de votre côté — voir ci-dessous.

## 1. Pile technique

- Laravel 11 · PHP 8.2+
- Blade + Tailwind CSS
- MySQL / MariaDB
- spatie/laravel-permission (rôles & permissions)
- spatie/laravel-activitylog (audit)
- laravel/breeze (authentification)

## 2. Installation

```bash
# a) Créer un projet Laravel neuf
composer create-project laravel/laravel rh-koanda-app
cd rh-koanda-app

# b) Installer les paquets
composer require spatie/laravel-permission spatie/laravel-activitylog
composer require laravel/breeze --dev
php artisan breeze:install blade

# c) Publier les migrations des paquets
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"
php artisan vendor:publish --provider="Spatie\Activitylog\ActivitylogServiceProvider" --tag="activitylog-migrations"

# d) Copier le contenu de CE dossier par-dessus le projet
#    (app/, database/, resources/views/, routes/, CLAUDE.md…)
#    en écrasant app/Models/User.php et routes/web.php / console.php.

# e) Configurer la base
cp .env.example .env        # adapter DB_DATABASE / DB_USERNAME / DB_PASSWORD
php artisan key:generate

# f) Migrer + données de démonstration
php artisan migrate --seed

# g) Compiler les assets et lancer
npm install && npm run dev
php artisan serve
```

## 3. Réglages à faire dans le projet Laravel

### a) Enregistrer l'alias middleware (`bootstrap/app.php`)

```php
->withMiddleware(function (Middleware $middleware) {
    $middleware->alias([
        'filiale.active' => App\Http\Middleware\FilialeActive::class,
    ]);
})
```

### b) Les Policies sont auto-découvertes par Laravel 11

`EmployePolicy` et `CongePolicy` se relient automatiquement à leurs modèles
(convention `App\Policies\{Model}Policy`). Rien à déclarer.

### c) `routes/web.php` requiert `routes/auth.php`

Fourni par Breeze (`php artisan breeze:install`). Il fournit aussi la route `logout`
utilisée dans le layout.

## 4. Comptes de démonstration

Tous avec le mot de passe : **`password`**

| Rôle | Email | Périmètre |
|------|-------|-----------|
| Super Admin | `admin@koandagroupe.bf` | Tout le groupe |
| DRH Groupe | `drh@koandagroupe.bf` | Tout le groupe |
| RH Filiale | `rh.gcmi@koandagroupe.bf` (et `rh.gcmimmo@…`, `rh.fasoen@…`, `rh.ecooil@…`, `rh.ecofood@…`, `rh.amko@…`) | Une filiale |

> ⚠️ **Changer ces mots de passe avant toute mise en production.**

## 5. Ce qui est livré

- ✅ Socle multi-filiales (trait `BelongsToFiliale` + `FilialeScope`)
- ✅ Schéma complet (19 migrations : organisation, employés, contrats, congés, présences, documents, formation, performance, discipline)
- ✅ 18 modèles Eloquent avec relations
- ✅ Rôles & permissions (7 rôles) + seeders des 6 filiales + données démo
- ✅ Modules câblés : **Tableau de bord** (consolidé Groupe + filiale), **Employés** (CRUD complet), **Congés** (demande + validation), **Filiales**
- ✅ Services métier, Policies, Form Requests, alertes planifiées, notifications

## 6. Pistes pour la suite (à finir dans Claude Code)

- Modules Contrats / Présences / Documents avec interfaces complètes
- Modules Formation, Performance, Discipline
- Exports Excel / PDF
- Tests d'isolation inter-filiales (priorité — voir CLAUDE.md)
- Upload de fichiers (photos, documents, contrats signés)

Voir `docs/ARCHITECTURE.md` pour la conception détaillée et `CLAUDE.md` pour les
règles de développement.

## 7. Mise en ligne

Pour rendre le site accessible à chaque RH depuis son ordinateur (hébergement,
domaine, HTTPS, comptes), suivre **`docs/DEPLOIEMENT.md`**.

### Déploiement Laravel Cloud

Procédure complète pas-à-pas : **`docs/LARAVEL-CLOUD.md`**.
