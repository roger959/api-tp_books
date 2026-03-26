# TP Books

Application web PHP de recherche de livres avec authentification utilisateur, gestion de favoris et interface modernisee.

Le projet permet de :

- creer un compte utilisateur
- se connecter et se deconnecter
- rechercher des livres via l'API Google Books
- ajouter ou retirer des livres des favoris
- modifier les informations de profil

## Apercu

L'application repose sur une architecture PHP simple, sans framework, avec :

- PHP natif
- MySQL
- HTML / CSS
- sessions PHP pour l'authentification
- API Google Books pour la recherche

## Fonctionnalites

- inscription utilisateur
- connexion securisee avec mot de passe hashé
- recherche de livres en ligne
- sauvegarde des favoris en base de donnees
- mise a jour du profil utilisateur
- interface responsive avec fond video

## Structure du projet

```text
api-tp_books/
├── config.php
├── connexion.php
├── index.php
├── inscription.php
├── logout.php
├── profil.php
├── setup.sql
├── styles.css
├── video.php
└── images/
```

## Prerequis

Avant de lancer le projet, assurez-vous d'avoir :

- PHP 8.0 ou plus recent
- MySQL ou MariaDB
- l'extension PHP mysqli activee
- l'extension PHP curl activee

Sous Windows, un environnement comme XAMPP, WAMP ou Laragon fonctionne tres bien.

## Installation

### 1. Cloner ou recuperer le projet

Placez le projet dans un dossier local, par exemple :

```powershell
c:\dev\PROJECT-CLUB-ET-COMPETITIONS\api-tp_books
```

### 2. Creer la base de donnees

Créez une base de donnees nommee :

```sql
tp_librairie
```

Puis importez le script SQL contenu dans le fichier `setup.sql`.

Exemple depuis un client MySQL :

```sql
CREATE DATABASE tp_librairie;
USE tp_librairie;
SOURCE setup.sql;
```

### 3. Verifier la configuration de la base

Le fichier `config.php` utilise par defaut :

```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'tp_librairie');
```

Adaptez ces valeurs si votre configuration MySQL est differente.

## Lancer le projet

Depuis le dossier du projet, lancez le serveur PHP embarque :

```powershell
cd c:\dev\PROJECT-CLUB-ET-COMPETITIONS\api-tp_books
php -S localhost:8000
```

Ensuite, ouvrez votre navigateur sur :

```text
http://localhost:8000/connexion.php
```

Vous pouvez aussi ouvrir :

```text
http://localhost:8000/
```

mais la page d'accueil redirigera vers la connexion si aucun utilisateur n'est authentifie.

## Parcours utilisateur

### Inscription

La page `inscription.php` permet de creer un compte avec :

- nom
- email
- mot de passe

### Connexion

La page `connexion.php` verifie les identifiants de l'utilisateur et cree la session.

### Accueil

La page `index.php` permet :

- de rechercher des livres avec Google Books
- d'afficher les resultats
- d'ajouter un livre aux favoris
- d'afficher la liste des favoris utilisateur

### Profil

La page `profil.php` permet de modifier :

- le nom
- l'email
- l'avatar

### Deconnexion

La page `logout.php` detruit la session et redirige l'utilisateur.

## Base de donnees

Le script `setup.sql` cree deux tables principales :

### Table `users`

- `id`
- `email`
- `password`
- `name`
- `avatar`

### Table `favorites`

- `id`
- `user_id`
- `book_id`
- `title`
- `authors`
- `thumbnail`

## API utilisee

La recherche de livres repose sur l'API Google Books, appelee dans `index.php` via cURL.

## Style et interface

L'interface est centralisee dans `styles.css`.

Les principaux choix visuels incluent :

- theme moderne sombre
- panneaux translucides
- fond video plein ecran
- mise en page responsive

## Points d'attention

- le projet depend d'une base MySQL active
- l'extension cURL doit etre disponible pour la recherche de livres
- l'extension mysqli doit etre activee pour l'acces a la base
- l'API Google Books est utilisee en ligne, donc une connexion Internet est necessaire pour la recherche

## Ameliorations possibles

- ajouter une validation serveur plus stricte sur les formulaires
- gerer proprement les erreurs SQL
- limiter les doublons dans les favoris
- ajouter un systeme de pagination pour les resultats
- proteger et externaliser la cle API Google Books

## Auteur

Projet realise dans le cadre d'un TP PHP autour d'une application de bibliotheque en ligne.
