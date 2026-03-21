# Ressource RH — demandes de congés

Cette application Laravel fournit un espace RH simple pour déposer des demandes de congés, les faire valider par les membres du conseil d'administration (CA) et générer les récapitulatifs mensuels destinés à la paie. L'authentification est désormais mutualisée avec la base `objets`, tandis que les données métier de l'application restent stockées dans la base `rh`.

## Prérequis
- PHP 8.1+ avec les extensions habituelles de Laravel.
- Composer pour installer les dépendances PHP.
- Un serveur MySQL/MariaDB (par exemple l'offre MySQL d'alwaysdata).

## Configuration de la base MySQL sur alwaysdata
1. Connectez-vous à votre console alwaysdata et créez une base de données MySQL (nom et mot de passe de votre choix).
2. Créez un utilisateur MySQL autorisé sur cette base (ou réutilisez l'utilisateur fourni par alwaysdata) et notez l'hôte, le port et les identifiants.
3. Copiez le fichier d'environnement et renseignez les variables :
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
4. Dans `.env`, configurez la base applicative `rh` puis la base d'authentification mutualisée `objets` :
   ```dotenv
   DB_CONNECTION=mysql
   DB_HOST=sql-0X.alwaysdata.net   # remplacez par l'hôte fourni
   DB_PORT=3306                    # ou le port indiqué
   DB_DATABASE=rh
   DB_USERNAME=nom_utilisateur
   DB_PASSWORD=mot_de_passe

   DB_AUTH_CONNECTION=mysql_auth   # à conserver pour pointer vers la base mutualisée objets
   DB_AUTH_HOST=sql-0X.alwaysdata.net
   DB_AUTH_PORT=3306
   DB_AUTH_DATABASE=objets
   DB_AUTH_USERNAME=nom_utilisateur
   DB_AUTH_PASSWORD=mot_de_passe
   ```

## Installer les dépendances
```bash
composer install
npm install
```

## Créer les tables avec les migrations
Une fois `.env` configuré, exécutez les migrations Laravel pour créer automatiquement les tables `leave_requests` et `payroll_manager` dans la base `rh` :
```bash
php artisan migrate
```

La migration génère notamment :
- la table `leave_requests` avec `employee_name`, `employee_email`, `start_date`, `end_date`, `reason`, `status`, `decision_notes` et `decision_made_at`
- la table `payroll_manager` avec un `uuid_user` unique pointant vers l'utilisateur mutualisé autorisé à accéder au récapitulatif paie

Si vous ne pouvez pas lancer les migrations, vous pouvez créer les tables manuellement avec les extraits SQL équivalents (à adapter si besoin) :
```sql
CREATE TABLE leave_requests (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    employee_name VARCHAR(255) NOT NULL,
    employee_email VARCHAR(255) NOT NULL,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    reason TEXT NULL,
    status VARCHAR(255) NOT NULL DEFAULT 'pending',
    decision_notes TEXT NULL,
    decision_made_at TIMESTAMP NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE payroll_manager (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    uuid_user BIGINT UNSIGNED NOT NULL UNIQUE,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

## Lancer l'application
1. Démarrez le serveur de développement Laravel :
   ```bash
   php artisan serve
   ```
2. Accédez ensuite à `http://localhost:8000` pour déposer des demandes de congés ou valider/rejeter celles en attente.


## Règles d'authentification
- La connexion lit les utilisateurs dans `objets.users` via la colonne `pseudo`; si la base d’authentification dédiée n’est pas configurée, l’application retombe automatiquement sur la colonne `email` de la table locale `users`.
- Le rôle `admin` est attribué quand `objets.users.admin = 2`.
- Le rôle `payroll_manager` est attribué quand l'utilisateur existe dans `rh.payroll_manager`.
- Le rôle `employee` est attribué quand l'utilisateur existe dans `objets.employes` via `uuid_user`.
- Les listes d'employés proposées aux administrateurs proviennent de `objets.employes` joint à `objets.users`.
