# TP Conteneurisation & CI — squelette **PHP / Slim 4 + php-fpm**

Point de départ du TP. Consultez `SUJET.md` et `ANNEXE-PHP.md` pour les consignes et le barème.

## Particularité de ce parcours

**php-fpm ne parle pas HTTP, il parle FastCGI sur le port 9000.** Votre reverse proxy nginx
utilisera donc `fastcgi_pass` et non `proxy_pass`. C'est l'architecture de production standard
de PHP — et le principal point d'attention de ce parcours.

```
navigateur ──HTTP──▶ nginx (proxy) ──FastCGI:9000──▶ php-fpm (api x3)
```

## Ce qui vous est fourni

```
├── api/                      application Slim complète (ne pas modifier)
│   ├── composer.json
│   ├── composer.lock         ← commité : garantit des builds reproductibles
│   ├── public/index.php      point d'entrée de l'API
│   ├── src/                  code métier
│   ├── tests/                le test unitaire utilisé par la CI
│   ├── phpunit.xml
│   └── docker/zz-ping.conf   config php-fpm pour la sonde de santé
├── db/init.sql               schéma + jeu de données initial
├── web/                      front statique servi par nginx
├── .env.example
└── .gitignore
```

## Ce que vous devez écrire

| Fichier | Partie du sujet |
|---|---|
| `api/Dockerfile` | Partie 1 |
| `api/.dockerignore` | Partie 1 |
| `compose.yml` | Partie 2 |
| `nginx-proxy.conf` | Partie 3 |
| `.github/workflows/ci.yml` | Partie 4 |
| `RAPPORT.md` | livrables |

Plus un `.env` local (copié depuis `.env.example`), **jamais commité**.

## Démarrage

```bash
cp .env.example .env

# 1) Vérifiez d'abord que les tests passent, hors Docker :
cd api
composer install
vendor/bin/phpunit

# 2) Puis écrivez le Dockerfile, le compose, etc.
docker compose up -d --build
```

L'application est ensuite accessible sur <http://localhost:8080>.

## Contrat d'API

| Méthode | Route | Rôle |
|---|---|---|
| `GET` | `/api/taches` | liste les tâches |
| `POST` | `/api/taches` | crée une tâche — `{"titre":"…","faite":false}` |
| `GET` | `/api/qui` | hostname du conteneur (sert à vérifier le load-balancing) |

## Variables d'environnement attendues par l'API

L'application ne contient **aucune** valeur de connexion en dur. Votre `compose.yml` doit lui
fournir :

- `DB_HOST` — ex. `db`
- `DB_NAME`
- `DB_USER`
- `DB_PASSWORD`

## Points à ne pas rater dans le Dockerfile

- L'extension **`pdo_pgsql`** n'est pas présente dans `php:8.4-fpm-alpine` : il faut la compiler,
  donc installer `postgresql-dev` et `$PHPIZE_DEPS`… puis les **supprimer dans le même `RUN`**,
  sinon l'image n'est allégée d'aucun octet.
- Le paquet **`fcgi`** (commande `cgi-fcgi`) est nécessaire au `HEALTHCHECK`.
- `composer install` doit se faire avec **`--no-dev`** dans l'image : PHPUnit n'a rien à faire
  en production. La CI, elle, installe **avec** les dépendances de développement.

## Note sur la version de PHP

`composer.json` exige `php >= 8.2` pour rester installable sur un poste un peu ancien, mais
l'image cible du TP est **`php:8.4-fpm-alpine`**. Le code n'utilise aucune syntaxe postérieure
à 8.2 : les deux fonctionnent.
