# Déploiement sur Laravel Cloud — RH Koanda Groupe

Guide pas-à-pas pour mettre l'application en ligne sur **Laravel Cloud**, de zéro
jusqu'à « chaque RH se connecte depuis son navigateur ».

> ⚠️ **À lire avant tout.** L'archive du projet contient les fichiers métier
> (modèles, migrations, services, vues…), **pas** un projet Laravel complet.
> L'Étape 0 consiste donc à assembler le projet entier. Le reste (Étapes 1 à 9)
> est le déploiement proprement dit.

---

## Étape 0 — Assembler le projet complet (en local / dans Claude Code)

Laravel Cloud déploie depuis un dépôt Git contenant **un projet Laravel complet**.
On le construit d'abord :

```bash
# 1. Projet Laravel neuf
composer create-project laravel/laravel rh-koanda-app
cd rh-koanda-app

# 2. Paquets requis
composer require spatie/laravel-permission spatie/laravel-activitylog
composer require laravel/breeze --dev
php artisan breeze:install blade

# 3. Migrations des paquets
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"
php artisan vendor:publish --provider="Spatie\Activitylog\ActivitylogServiceProvider" --tag="activitylog-migrations"

# 4. Table des notifications (canal "database" utilisé par les alertes contrats)
php artisan make:notifications-table

# 5. Copier le contenu de l'archive PAR-DESSUS le projet
#    (app/, database/, resources/views/, routes/, docs/, CLAUDE.md, README.md…)
#    en écrasant app/Models/User.php, routes/web.php et routes/console.php.

# 6. Enregistrer l'alias middleware dans bootstrap/app.php :
#    ->withMiddleware(function (Middleware $middleware) {
#        $middleware->alias(['filiale.active' => App\Http\Middleware\FilialeActive::class]);
#    })

# 7. Vérifier en local
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
npm install && npm run build
php artisan serve   # tester sur http://localhost:8000
```

Connecte-toi en local avec `admin@koandagroupe.bf` / `password`. Si le tableau de
bord s'affiche et que tu vois les 6 filiales, le projet est prêt à être déployé.

---

## Étape 1 — Envoyer le code sur GitHub

Laravel Cloud se connecte à GitHub, GitLab ou Bitbucket. Avec GitHub :

```bash
git init
git add .
git commit -m "Application RH Koanda Groupe"
# Crée un dépôt vide sur github.com (privé de préférence, ce sont des données RH),
# puis :
git remote add origin https://github.com/<ton-compte>/rh-koanda.git
git branch -M main
git push -u origin main
```

> Le `.gitignore` de Laravel exclut déjà `vendor/` et `.env` — aucun secret n'est
> poussé. **Garde le dépôt privé.**

---

## Étape 2 — Créer le compte Laravel Cloud

1. Va sur **cloud.laravel.com** et crée un compte.
2. Ajoute un moyen de paiement (carte bancaire). C'est exigé même pour l'offre
   gratuite, mais : **le premier mois Starter est offert**, tu as **5 $ de crédit
   mensuel**, et tu peux définir une **limite de dépense** ferme (voir Étape 8) pour
   ne jamais être surpris.

---

## Étape 3 — Créer l'application

1. Tableau de bord → **+ New application**.
2. **Continue with GitHub** (ou GitLab/Bitbucket) → autorise l'accès au dépôt
   `rh-koanda`.
3. Sélectionne le dépôt, nomme l'application, et choisis une **région** proche :
   **Europe (Francfort ou Londres)**.

---

## Étape 4 — Créer la base de données

Sur le plan de l'environnement (« infrastructure canvas ») → **Add resource** →
base de données :

- **Laravel MySQL** — recommandé : l'application est écrite pour MySQL, **aucune
  modification de code**. Petit forfait fixe pour la taille « dev ».
- *Alternative* : Serverless Postgres se met en veille à zéro (moins cher), mais il
  faut passer `DB_CONNECTION=pgsql` et vérifier deux ou trois migrations. Pour un
  essai sans souci, reste sur MySQL.

Laravel Cloud injecte automatiquement les variables `DB_*` dans l'application : tu
n'as pas à les recopier.

---

## Étape 5 — Variables d'environnement

Dans l'onglet **Environment** de l'application :

```env
APP_NAME="RH Koanda Groupe"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://<ton-app>.laravel.cloud   # adresse fournie par Cloud
APP_LOCALE=fr

# APP_KEY : utilise le bouton de génération de Cloud,
# ou colle la sortie de `php artisan key:generate --show`

SESSION_DRIVER=database
QUEUE_CONNECTION=database
CACHE_STORE=database

# E-mail (réinitialisation de mot de passe + alertes contrats)
MAIL_MAILER=smtp
MAIL_HOST=...
MAIL_PORT=587
MAIL_USERNAME=...
MAIL_PASSWORD=...
MAIL_FROM_ADDRESS="rh@koandagroupe.bf"
```

(Les `DB_*` sont gérés automatiquement par la ressource base de données.)

---

## Étape 6 — Commandes de build / déploiement

Dans les réglages de déploiement de l'environnement, assure-toi que la commande de
déploiement contient la migration (le `--force` est obligatoire en production) :

```bash
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

La compilation des assets (`composer install`, `npm run build`) est gérée par Cloud.

---

## Étape 7 — Premier déploiement + données initiales

1. Clique sur **Deploy**. Cloud récupère le code, installe, migre et publie.
2. **Une seule fois**, après le premier déploiement réussi, lance le peuplement via
   l'outil **Commands** de Cloud :
   ```bash
   php artisan db:seed --force
   ```
   Cela crée les 7 rôles, les 6 filiales et les comptes de démonstration. Les
   seeders sont idempotents (ré-exécutables sans doublon).

---

## Étape 8 — Planificateur, file d'attente et limite de dépense

- **Scheduler** : active-le (interrupteur). Il lance les alertes contrats
  (`rh:contrats-expirants`, `rh:expirer-contrats`).
- **Managed queue** : active une file gérée pour l'envoi des e-mails/notifications.
- **Mise en veille à zéro** : laisse-la activée (par défaut sur Starter) — l'appli
  dort quand personne ne l'utilise et se réveille en moins d'une seconde.
- **Spending limit** : fixe une limite mensuelle (ex. 10 $). Au plafond, le compute
  se met en pause ; tu ne peux pas être facturé au-delà.

---

## Étape 9 — Accès des RH

1. Pour l'essai, utilise l'adresse gratuite `https://<ton-app>.laravel.cloud`
   (HTTPS automatique). Un domaine personnalisé (`rh.koandagroupe.bf`) pourra être
   branché plus tard.
2. **Change les mots de passe de démonstration**, puis crée les vrais comptes RH
   (un par filiale).
3. Communique l'adresse + les identifiants à chaque RH. Ils se connectent depuis
   leur navigateur ; chacun ne voit que sa filiale.

---

## Déploiements suivants

À chaque `git push` sur la branche `main`, Laravel Cloud redéploie automatiquement
(migrations comprises). Tu développes dans Claude Code, tu pousses, c'est en ligne.

## Coût attendu pour l'essai

Premier mois Starter offert + 5 $ de crédit mensuel. Le compute se met en veille à
zéro (donc quasi nul pour une appli RH peu sollicitée) ; le principal poste est la
base MySQL gérée (petit forfait). Avec une limite de dépense, le budget reste maîtrisé.
