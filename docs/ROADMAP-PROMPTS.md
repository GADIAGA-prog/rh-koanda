# Feuille de route — Prompts pour Claude Code

Ce fichier regroupe les prompts prêts à coller dans **Claude Code** pour développer
les modules restants de la plateforme RH Koanda Groupe, dans l'ordre conseillé.

## Mode d'emploi

- Colle **un seul prompt à la fois**.
- Laisse Claude Code terminer, puis **teste en local** (`http://rh-koanda-app.test`
  ou `php artisan serve`) avant de passer au suivant.
- Quand un module fonctionne : `git push` → Laravel Cloud le met en ligne
  automatiquement. **Ne pousse jamais un module non testé.**

## Règles communes (déjà dans `CLAUDE.md`)

Inutile de les répéter — les prompts y renvoient. Pour mémoire :
logique dans un **Service**, validation en **Form Request**, autorisation en
**Policy**, **scope multi-filiales** via le trait `BelongsToFiliale`, **soft deletes**
sur les données sensibles, vues Blade avec `layouts.app`. Et pour **chaque** module :
ajouter ses **permissions** dans `RolePermissionSeeder` (+ les affecter aux rôles) et
le **lien dans la barre latérale**.

> ⚠️ Garde-fou central : après chaque module, demande à Claude Code de vérifier le
> **test d'isolation entre filiales** — un RH d'une filiale ne doit jamais voir les
> données d'une autre.

---

## 1. Gestion des accès (utilisateurs, rôles, permissions) — à faire en premier

```
En suivant le motif des modules Employés et Congés déjà présents et les règles de CLAUDE.md, développe un module « Administration des accès » réservé aux rôles super-admin et drh-groupe.

Trois écrans :
1) Utilisateurs : liste paginée et recherchable (nom, email, rôle, filiale, statut actif/inactif). Création d'un utilisateur (nom, email, mot de passe initial), affectation d'un rôle (parmi super-admin, direction-generale, drh-groupe, rh-filiale, manager, employe, auditeur-groupe), affectation d'une filiale principale et, pour les RH multi-filiales, de filiales gérées supplémentaires via la table pivot filiale_user. Possibilité d'activer/désactiver un compte (champ actif) et de réinitialiser le mot de passe. Lier optionnellement un utilisateur à une fiche employé existante.
2) Rôles & permissions : matrice affichant chaque rôle et ses permissions (Spatie), modifiable uniquement par super-admin.
3) Mon profil : tout utilisateur peut changer son propre mot de passe.

Crée un UtilisateurService (création, changement de rôle, (dés)activation, reset mot de passe), une UtilisateurPolicy (seuls super-admin et drh-groupe gèrent les comptes ; un drh-groupe ne peut pas modifier un super-admin), les Form Requests, et les vues Blade.

Ajoute dans RolePermissionSeeder les permissions utilisateur.view/create/update/delete et role.manage, affectées à super-admin et drh-groupe. Ajoute une section « Administration » dans la barre latérale, visible seulement si l'utilisateur a la permission utilisateur.view.

Important : un compte désactivé (actif = false) ne doit pas pouvoir se connecter — ajoute cette vérification à l'authentification. Teste en local avant que je pousse.
```

---

## 2. Contrats — le socle existe déjà (modèle, migration, ContratService)

```
Le modèle Contrat, sa migration et ContratService existent déjà. En suivant le motif d'Employés/Congés et CLAUDE.md, développe l'interface du module Contrats : liste filtrable (par filiale, type, statut, employé), création, édition, consultation, et action « renouveler » (qui clôture le contrat courant et en crée un nouveau à la suite). Gère les types CDI, CDD, stage, consultant, prestataire, les dates, le salaire de base et la devise.

Branche les alertes d'expiration déjà prévues (commande rh:contrats-expirants) à un encart « contrats à renouveler » sur la fiche employé et le tableau de bord. Crée ContratPolicy et les Form Requests. Ajoute les permissions manquantes au seeder et le lien dans la barre latérale. Respecte le scope multi-filiales. Teste en local.
```

---

## 3. Postes, départements et organigramme

```
En suivant CLAUDE.md et le motif existant, développe le module Organisation : CRUD pour les départements, les postes et les fonctions, chacun rattaché à une filiale (et un site pour les départements). Ajoute une vue « organigramme » par filiale montrant la hiérarchie via le champ manager_id des employés (arborescence responsable → subordonnés).

Les modèles Departement, Poste, Fonction et Site existent déjà. Crée les contrôleurs, Services si nécessaire, Policies, Form Requests et vues. Permissions au seeder + liens dans la barre latérale. Scope multi-filiales respecté. Teste en local.
```

---

## 4. Présences et absences

```
Les modèles Presence et Absence et leurs migrations existent déjà. En suivant CLAUDE.md, développe le module Présences & Absences : saisie/pointage manuel par jour (heure d'arrivée, départ, statut présent/retard/absent), vue calendrier ou tableau mensuel par filiale, et gestion des absences (justifiée/non justifiée, motif, pièce justificative). Ajoute un indicateur de taux d'absentéisme et de retards par filiale au tableau de bord.

Crée PresenceService et AbsenceService pour la logique (calcul des compteurs), les Policies, Form Requests et vues. Permissions au seeder + liens dans la barre latérale. Scope multi-filiales. Teste en local.
```

---

## 5. Missions et ordres de mission

```
Crée un nouveau module Missions (ordres de mission). Crée la migration missions scoppée par filiale (trait BelongsToFiliale) avec : employe_id, objet, destination, lieu_depart, date_depart, date_retour, nombre_jours, moyen_transport, indemnite_journaliere, autres_frais, montant_total, devise, statut (brouillon, soumise, validée, refusée, clôturée), validateur_id, valide_le, motif_refus, observations. Soft deletes.

Crée un enum StatutMission, le modèle Mission, un MissionService (calcul du montant total = nombre_jours × indemnité journalière + autres frais ; workflow soumettre → valider/refuser → clôturer), une MissionPolicy (création par RH/manager, validation par drh-groupe ou rh-filiale du périmètre), les Form Requests et les vues (liste filtrable, création, fiche détail avec état de frais, boutons valider/refuser).

Ajoute le module mission et ses permissions (dont mission.valider) au RolePermissionSeeder et aux rôles concernés, et le lien dans la barre latérale. Suis le motif de Congés pour le workflow de validation. Teste en local.
```

---

## 6. Paie et salaires — le plus délicat, à traiter avec rigueur

```
Crée un module Paie scoppé par filiale, en suivant CLAUDE.md. Trois entités :
- rubriques_paie : catalogue paramétrable des éléments de paie (code, libellé, type = gain/retenue/cotisation, mode de calcul = fixe/pourcentage, montant ou taux, base de calcul, imposable, ordre, actif). filiale_id nullable (null = commune au groupe).
- bulletins_paie : un bulletin par employé et par période (AAAA-MM), avec salaire de base, total gains, salaire brut, total cotisations, total retenues, net à payer, coût employeur, statut (brouillon/validé/payé). Unique (employe_id, periode). Soft deletes.
- lignes_bulletin : le détail (libellé, type, base, taux, montant) de chaque bulletin.

Crée les enums (TypeRubrique, ModeCalcul, StatutBulletin), les modèles, un PaieService qui génère un bulletin pour un employé sur une période : il part du salaire de base du contrat actif, applique les rubriques (gains, cotisations salariales type CNSS, impôt type IUTS, retenues), calcule brut → net et le coût employeur. Génération possible en masse pour toute une filiale sur une période.

IMPORTANT : implémente les taux (CNSS, IUTS, barèmes) comme des RUBRIQUES PARAMÉTRABLES, pas en dur dans le code, avec des valeurs par défaut clairement marquées « à vérifier » — la réglementation fiscale et sociale du Burkina Faso doit être validée par un comptable. Ajoute un RubriquePaieSeeder avec des rubriques de démonstration (salaire de base, prime de transport, indemnité de logement, CNSS salarié, IUTS).

Crée PaiePolicy (réservé rh-filiale de son périmètre, drh-groupe, super-admin), les Form Requests, et les vues : catalogue des rubriques, génération de paie d'une période, liste des bulletins, et un bulletin de paie imprimable (PDF). Ajoute les permissions paie au seeder + le lien dans la barre latérale. Affiche une masse salariale estimative par filiale sur le tableau de bord. Teste en local.
```

---

## 7. Performance — évaluations

```
Le modèle EvaluationPerformance et sa migration existent déjà. En suivant CLAUDE.md, développe le module Performance : campagnes d'évaluation par période, fiche d'évaluation (objectifs, note globale, commentaire, prime proposée), saisie par le manager pour son équipe, consultation par le RH et la DG. Crée le Service, la Policy (un manager n'évalue que ses subordonnés via manager_id), les Form Requests et les vues. Permissions au seeder + lien dans la barre latérale. Scope multi-filiales. Teste en local.
```

---

## 8. Formation — plans et participants

```
Les modèles Formation et Competence (et leurs pivots) existent déjà. En suivant CLAUDE.md, développe le module Formation : création d'une formation (intitulé, objectif, organisme, dates, coût, devise), inscription des participants, suivi de présence et résultat, et acquisition de compétences (niveau 1 à 5). Vue des coûts de formation par filiale. Crée le Service, la Policy, les Form Requests et les vues. Permissions au seeder + lien dans la barre latérale. Scope multi-filiales. Teste en local.
```

---

## 9. Documents RH — pièces justificatives avec confidentialité

```
Le modèle DocumentRh et sa migration existent déjà (avec champ confidentialite public/rh/direction). En suivant CLAUDE.md, développe le module Documents RH : téléversement de fichiers (contrats signés, diplômes, CNIB, attestations, fiches de poste, certificats) rattachés à un employé, avec type, titre, date d'expiration et niveau de confidentialité. Stockage via le système de fichiers Laravel (disque privé), téléchargement contrôlé par Policy selon la confidentialité et la filiale. Alerte sur les documents expirés/expirant. Crée le Service, la Policy, les Form Requests et les vues, et intègre les documents à la fiche employé. Permissions au seeder + lien dans la barre latérale. Teste en local.
```

---

## 10. Discipline et sanctions

```
Le modèle Sanction et sa migration existent déjà. En suivant CLAUDE.md, développe le module Discipline : enregistrement des avertissements, demandes d'explication et sanctions (type, date, motif, pièce jointe, agent prononçant), avec historique par employé et confidentialité renforcée. Crée le Service, la Policy (réservé rh-filiale de son périmètre et drh-groupe), les Form Requests et les vues, intégrées à la fiche employé. Permissions au seeder + lien dans la barre latérale. Scope multi-filiales. Teste en local.
```

---

## 11. Exports et rapports

```
En suivant CLAUDE.md, ajoute les exports et rapports : export Excel et PDF des listes (employés, contrats, congés, bulletins de paie) et des états consolidés par filiale, en respectant le périmètre de filiale de l'utilisateur. Utilise un package Laravel adapté (par ex. maatwebsite/excel pour Excel et un générateur PDF pour les états). Ajoute un écran « Rapports » avec les statistiques RH par filiale (effectifs, absentéisme, turnover, masse salariale, contrats à renouveler). Crée un RapportService réutilisant StatistiqueRhService. Permissions au seeder + lien dans la barre latérale. Teste en local.
```

---

## Après chaque module

1. Tester en local (connexion, création, lecture, modification).
2. Vérifier le **test d'isolation entre filiales**.
3. `git push` → déploiement automatique sur Laravel Cloud.
4. Passer au module suivant.
