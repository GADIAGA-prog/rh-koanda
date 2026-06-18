# Mise en ligne — RH Koanda Groupe

Objectif : rendre l'application accessible sur Internet à une adresse du type
`https://rh.koandagroupe.bf`, où chaque RH se connecte depuis son ordinateur avec
son propre identifiant. **L'application gère déjà les comptes par filiale** ; il
reste à l'héberger correctement, avec un nom de domaine et le HTTPS.

---

## 1. Ce dont vous avez besoin

1. **Un nom de domaine** (ex. `koandagroupe.bf` ou un `.com`). Les `.bf` se prennent
   auprès d'un registraire agréé au Burkina Faso ; un `.com` chez n'importe quel
   registraire international.
2. **Un hébergement compatible Laravel** (PHP 8.2+, MySQL, HTTPS) — voir §2.
3. **Le HTTPS** (certificat SSL) : obligatoire, c'est ce qui chiffre les mots de
   passe et les données RH en transit. Gratuit dans les deux options ci-dessous.

---

## 2. Choisir l'hébergement

Deux bonnes options selon que vous préférez la **simplicité** ou le **contrôle/prix**.

### Option A — Laravel Cloud *(recommandé pour démarrer simplement)*

Plateforme officielle de Laravel, entièrement gérée : vous connectez votre dépôt
Git, et chaque « push » déploie le site. SSL automatique, base MySQL gérée,
planificateur et files d'attente activables en un clic — pas d'administration serveur.

- Tarif : offre **Starter à 5 $/mois** (crédits inclus, montée en charge à zéro,
  domaine personnalisé et SSL compris). Palier supérieur **Growth à 20 $/mois**.
- Régions les plus proches du Burkina Faso : **Europe (Francfort, Londres, Irlande)**
  ou **Moyen-Orient (Émirats)**.
- Idéal ici car l'application a des **tâches planifiées** (alertes contrats) et des
  **e-mails/notifications** : tout est intégré, rien à configurer côté serveur.

### Option B — VPS Hostinger *(le plus économique, support en français)*

Un serveur privé que vous contrôlez. Hostinger propose un **modèle « Ubuntu +
Laravel »** préinstallé, un panneau en français et un support 24/7 francophone.

- Tarif : à partir de **~5 à 6,50 $/mois** (formule KVM 1 : 1 vCPU, 4 Go RAM,
  50 Go NVMe) — largement suffisant pour quelques dizaines d'utilisateurs RH.
- Centres de données en **France, Royaume-Uni, Allemagne** (latence correcte vers
  l'Afrique de l'Ouest).
- Demande un peu plus de mise en main (SSH, cron, certificat Let's Encrypt), mais
  l'assistant intégré et le support français facilitent la prise en main.

> **Conseil :** pour un premier déploiement sans expérience d'administration système,
> commencez par **Laravel Cloud**. Si le coût récurrent ou le contrôle total priment,
> partez sur le **VPS Hostinger**. Dans les deux cas, le coût mensuel reste modeste.

---

## 3. Réglages de production (les deux options)

Dans le fichier `.env` du serveur, basculer en mode production :

```env
APP_ENV=production
APP_DEBUG=false          # ne jamais laisser true en ligne (fuite d'infos)
APP_URL=https://rh.koandagroupe.bf
APP_KEY=                 # généré par : php artisan key:generate

DB_CONNECTION=mysql
DB_DATABASE=...          # fournis par l'hébergeur
DB_USERNAME=...
DB_PASSWORD=...          # mot de passe fort

SESSION_DRIVER=database
QUEUE_CONNECTION=database

# E-mail (réinitialisation de mot de passe + alertes) : utiliser un vrai SMTP
MAIL_MAILER=smtp
MAIL_HOST=...
MAIL_PORT=587
MAIL_USERNAME=...
MAIL_PASSWORD=...
MAIL_FROM_ADDRESS="rh@koandagroupe.bf"
```

Commandes à lancer après le premier déploiement :

```bash
php artisan key:generate
php artisan migrate --seed --force      # --force car en production
php artisan config:cache route:cache view:cache
npm run build                            # compile Tailwind/JS pour la prod
```

### Le planificateur (alertes contrats)

L'application programme des tâches (`rh:contrats-expirants`, `rh:expirer-contrats`).

- **Laravel Cloud** : activer le scheduler dans l'interface (un interrupteur).
- **VPS** : ajouter une ligne cron :
  ```
  * * * * * cd /chemin/du/projet && php artisan schedule:run >> /dev/null 2>&1
  ```

### La file d'attente (e-mails/notifications)

- **Laravel Cloud** : activer une « managed queue ».
- **VPS** : faire tourner `php artisan queue:work` en service (via Supervisor).

---

## 4. Comment chaque RH se connecte

1. Vous communiquez à chaque RH l'adresse du site (ex. `https://rh.koandagroupe.bf`)
   et son identifiant (e-mail + mot de passe).
2. Chaque RH ouvre l'adresse dans son navigateur, se connecte, et **ne voit que les
   données de sa filiale** — c'est garanti par le scope multi-filiales déjà en place.
3. Le Super Admin et le DRH Groupe voient l'ensemble du groupe.

> Aucun logiciel à installer sur les postes : un simple navigateur (Chrome, Edge,
> Firefox) suffit. Le site fonctionne aussi sur téléphone.

---

## 5. Sécurité des données RH — à ne pas négliger

- [ ] **HTTPS partout** (forcer la redirection http → https).
- [ ] **`APP_DEBUG=false`** en production.
- [ ] **Changer les mots de passe de démonstration** avant l'ouverture.
- [ ] **Mots de passe forts** pour la base et les comptes.
- [ ] **Sauvegardes automatiques** de la base (quotidiennes) — vérifier qu'elles
      tournent et tester une restauration.
- [ ] Limiter la création de comptes aux rôles Groupe (déjà via permissions).
- [ ] **Conformité locale** : les données RH sont des données personnelles. Au
      Burkina Faso, leur traitement relève de la réglementation sur la protection des
      données personnelles (CIL). Se renseigner sur l'obligation de déclaration. Je ne
      suis pas juriste — à confirmer auprès d'un conseil ou de l'autorité compétente.

---

## 6. Récapitulatif du chemin le plus simple

1. Acheter un domaine.
2. Créer un compte Laravel Cloud, connecter le dépôt Git du projet.
3. Créer la base MySQL gérée, renseigner le `.env` de production.
4. Déployer, lancer `migrate --seed --force`, activer scheduler + queue.
5. Brancher le domaine (SSL automatique).
6. Changer les mots de passe de démo, créer les vrais comptes RH.
7. Communiquer l'adresse et les identifiants à chaque RH.
