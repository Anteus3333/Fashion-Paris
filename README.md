# Fashion Paris

Site e-commerce **Fashion Paris** — **Dossier Projet** Studi (Développeur web et web mobile).  
Architecture calquée sur [Vite et Gourmand (ECF)](../) : **MVC maison**, `public/` + Router, Models / Repositories, Docker, MySQL, MongoDB (stats), CSRF, AuthGuard, JS vanilla.

| | |
|---|---|
| **Stack** | PHP 8+ · MySQL · MongoDB Atlas (stats) · Apache · Docker |
| **Front** | HTML / CSS / JS vanilla (mobile first) |
| **Accès Docker** | [http://localhost:8081](http://localhost:8081) |

---

## Lancer avec Docker

Arrêter Laragon si conflit sur les ports. Fashion utilise **8081** (web) et **3307** (MySQL) pour cohabiter avec l’ECF (8080 / 3306).

```bash
cd C:\Docker_App\Fashion
docker compose up -d --build
```

| Service | Accès |
|---------|--------|
| Web | http://localhost:8081 |
| MySQL | `127.0.0.1:3307` — user `fashion` / `fashion` (root : `root`) |

Arrêt : `docker compose down`  
Reset BDD : `docker compose down -v` puis `docker compose up -d --build`

## Comptes de test

| Rôle | E-mail | Mot de passe |
|------|--------|--------------|
| Admin | `admin@fashionparis.fr` | `Password1!` |
| Client | `client@fashionparis.fr` | `Password1!` |

## Structure (alignée ECF)

```
Fashion/
├── public/          # DocumentRoot (index.php, css, js, images)
├── src/
│   ├── Router.php
│   ├── Controllers/
│   ├── Models/
│   ├── Repositories/
│   ├── Services/    # SqlDatabase, MongoDatabase, Csrf, AuthGuard…
│   └── Views/
├── config/          # database.php, mongodb.php (+ *.local.php)
├── sql/             # schema.sql + donnees_test.sql
├── docker/          # entrypoint + MySQL utf8mb4
├── scripts/         # sync_stats_mongo.php (stub)
└── docker-compose.yml
```

## Routes principales

| URL | Rôle |
|-----|------|
| `/` | Accueil + vedettes + newsletter |
| `/catalogue` · `/catalogue/{cat}` · `/catalogue/recherche` | Catalogue |
| `/produit/{slug}` | Fiche produit + avis |
| `/panier` · `/panier/ajouter|modifier|retirer` | Panier session |
| `/commande` | Checkout (AuthGuard + décrément stock) |
| `/login` · `/inscription` | Auth (CSRF + hash bcrypt) |
| `/mon-compte` · `/mon-compte/profil` · `/mon-compte/commande/{n}` | Espace client + suivi |
| `/admin` | Dashboard stats MySQL |
| `/admin/produits` · `/admin/produit/...` | CRUD produits / stocks |
| `/admin/commandes` · `/admin/commande/{n}` | Gestion & suivi commandes |
| `/admin/clients` · `/admin/avis` · `/admin/stats` | Clients, modération, Mongo |

## Sécurité (contre-exemple du sujet)

L’extrait PHP du PDF sujet est un **anti-modèle** : SQL concaténé, mots de passe en clair, XSS, pas de CSRF.  
Ici : **PDO préparé**, `password_hash` / `password_verify`, `htmlspecialchars`, `Csrf`, `AuthGuard`, transaction stock à la commande.

## MongoDB (stats)

1. Copier `config/mongodb.local.example.php` → `mongodb.local.php` (URI Atlas)  
2. Sync : `docker compose exec web php scripts/sync_stats_mongo.php`  
   ou bouton dans `/admin/stats`

## Suite possible

Blog, upload images produits, e-mails (confirmation), limitation tentatives de connexion, maquettes + rédaction du dossier projet.
