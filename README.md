# Prise de rendez-vous en ligne

**Démo en ligne : [rdv-bby.infinityfreeapp.com](https://rdv-bby.infinityfreeapp.com/)**
(espace admin : `admin` / `admin1234` — identifiants de démo, à ne pas
utiliser tel quel en production)

Application web PHP + MySQL pour prendre rendez-vous (coiffeur, coach,
médecin...) : choix d'un service, d'une date, affichage des créneaux
réellement disponibles, et un espace admin pour gérer les rendez-vous et
les services proposés.

## Fonctionnalités

**Côté client**
- Choisir un service et une date
- Voir les créneaux libres, calculés en tenant compte des horaires
  d'ouverture, de la pause déjeuner, et des rendez-vous déjà pris
- Réserver (nom + email), avec revérification du créneau au moment de
  valider (pour éviter qu'un créneau affiché comme libre soit déjà pris
  par quelqu'un d'autre entre-temps)

**Espace admin** (authentifié, mot de passe haché avec `password_hash`)
- Voir la liste des rendez-vous à venir, les annuler
- Gérer les services (ajouter, modifier, supprimer, avec durée et prix)

## Stack technique

- **PHP** — sessions pour l'authentification admin
- **MySQL (InnoDB)** — clés étrangères réelles (`ON DELETE CASCADE` sur les
  rendez-vous liés à un service supprimé)
- **PDO** avec requêtes préparées
- **password_hash / password_verify** — jamais de mot de passe en clair
  ni de comparaison directe

## Le calcul des créneaux (le cœur du projet)

`functions.php` génère tous les créneaux possibles entre l'ouverture et
la fermeture (pas de 15 minutes), retire ceux qui chevauchent la pause
déjeuner, puis retire ceux qui chevauchent un rendez-vous déjà enregistré
ce jour-là — peu importe le service, puisqu'un seul rendez-vous à la fois
est possible.

## Installation (WampServer)

1. Copie le dossier dans `www/` (ex : `C:\wamp64\www\reservation-rdv`)
2. Importe le schéma (crée aussi la base, des services de départ et un
   compte admin `admin` / `admin1234` — **à changer après la première
   connexion**) :
```
mysql -u root < sql/schema.sql
```
3. Démarre WampServer, ouvre `http://localhost/reservation-rdv/`

## Déploiement

La démo tourne sur [InfinityFree](https://infinityfree.net) (hébergement PHP
+ MySQL gratuit) : fichiers envoyés en FTP, schéma importé via phpMyAdmin
(sans `CREATE DATABASE`/`USE`), `config.php` de production adapté aux
identifiants fournis par l'hébergeur — ce fichier n'est pas versionné ici,
seul le `config.php` de dev local (`root` sans mot de passe) est dans ce
dépôt.

## Structure du projet

```
reservation-rdv/
├── config.php            # connexion PDO + horaires d'ouverture
├── functions.php           # calcul des créneaux disponibles
├── index.php                # choix du service et de la date
├── creneaux.php               # créneaux libres pour ce choix
├── reserver.php                 # confirmation + enregistrement
├── confirmation.php               # page de succès
├── style.css
├── admin/
│   ├── auth.php                     # garde d'accès (session)
│   ├── login.php                      # connexion admin
│   ├── logout.php
│   ├── index.php                        # liste des rendez-vous, annulation
│   └── services.php                       # CRUD des services
└── sql/schema.sql                           # tables + données de départ
```
