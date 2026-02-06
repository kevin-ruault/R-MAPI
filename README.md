# Rick & Morty API (Symfony)

Ce projet est une ré-implémentation partielle de la [Rick & Morty API](https://rickandmortyapi.com/), réalisée dans le cadre d’un test technique.
L’objectif est de reproduire une partie du comportement de l’API originale (pagination, filtres, structure JSON), en mettant l’accent sur :
- la qualité du code
- la testabilité
- la séparation des environnements (dev / test)

## Stack technique

- PHP 8.x
- Symfony 7.x
- Doctrine ORM
- SQLite (dev & test)
- PHPUnit
- Faker (fixtures)

## Fonctionnalités implémentées

- `GET /api/character`
  - pagination (20 éléments par page)
  - filtres :
    - `name` (recherche partielle, insensible à la casse)
    - `status` (recherche partielle, insensible à la casse)
    - `gender` (valeur exacte, insensible à la casse)
    - `origin` (recherche partielle, insensible à la casse)
    - `species` (recherche partielle, insensible à la casse)
- `GET /api/character/{id}`
  - 404 si la ressource n’existe pas
- Fixtures générant des personnages inspirés de l’univers Rick & Morty
- Tests d’intégration couvrant pagination, filtres et cas d’erreur

## Installation

### Prérequis
- PHP >= 8.1
- Composer
- SQLite
- Symfony CLI

### Setup du projet

```
git clone https://github.com/kevin-ruault/R-MAPI.git
cd R-MAPI
composer install
composer setup
```

La commande `composer setup` prépare les bases de données de développement et de test (migrations + fixtures) afin de rendre l’API immédiatement utilisable.

### Lancer l'application

```composer serve```

Puis accéder à : `http://127.0.0.1:8000/`

### Tests

Les tests utilisent une base SQLite dédiée (`app_test.db`), isolée de l’environnement de développement.
```composer test```

Le script :
-   recrée la base de test
-   applique les migrations
-   charge les fixtures
-   lance PHPUnit

### Base de données (dev & test)

Reset complet de la base de données de développement : ```composer db:reset```
Reset complet de la base de données de test : ```composer db:reset-test``` 

Cela :
-   supprime la base SQLite
-   rejoue les migrations
-   recharge les fixtures

## Choix techniques

-   **SQLite** : simplicité, rapidité, reproductibilité.
-   **Fixtures contrôlées** : données inspirées de Rick & Morty, mais non strictement identiques à l’API officielle.
-   **Séparation dev / test** : bases de données distinctes pour éviter toute interaction entre tests et environnement local.
-   **Tests d’intégration** : validation du contrat API (structure JSON, filtres, pagination).
-  **Pas de dépendance externe** : l’API est totalement autonome et ne dépend pas de l’API officielle pour fonctionner.


## Temps passé
Temps total estimé : **~6 heures**

-   Mise en place des differentes tâches : ~30-45min
-   Setup environnement & Symfony (WSL, Symfony, SQLite) : ~1h
-   Modélisation & migrations : ~1h
-   Endpoints & logique métier : ~1h30
-   Tests & stabilisation : ~2h
-   UI : ~20-30min

## Utilisation de la documentation et des outils

La documentation officielle de la Rick & Morty API a été utilisée comme référence
pour la structure des endpoints, la pagination et le format des réponses JSON.

La documentation Symfony et Doctrine a été consultée ponctuellement pour les
aspects liés à la configuration, aux migrations et aux tests.

Des assistants IA (GPT, Copilot) ont été utilisés comme outil de support (rappels de syntaxe,
vérification d’approches, aide au débogage), mais l’ensemble des choix techniques,
de l’architecture et du code final ont été conçus, adaptés et validés manuellement.

## Améliorations possibles

-   Ajout des endpoints `location`
-   Les `images` et `episodes` sont difficilement accessible au vu du fait que j'utilise Faker, ce qui créerait des incohérences scénaristiques
-   Documentation OpenAPI / Swagger
-   Validation plus stricte des paramètres d’entrée
