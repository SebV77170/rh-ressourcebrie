# Ressource RH — demandes de congés

Cette application Laravel fournit un espace RH simple pour déposer des demandes de congés, les faire valider par les membres du conseil d'administration (CA) et générer les récapitulatifs mensuels destinés à la paie.

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
4. Dans `.env`, configurez la section base de données en remplaçant par vos valeurs alwaysdata :
   ```dotenv
   DB_CONNECTION=mysql
   DB_HOST=sql-0X.alwaysdata.net   # remplacez par l'hôte fourni
   DB_PORT=3306                    # ou le port indiqué
   DB_DATABASE=nom_de_votre_base
   DB_USERNAME=nom_utilisateur
   DB_PASSWORD=mot_de_passe
   ```

## Installer les dépendances
```bash
composer install
npm install
```

## Créer les tables avec les migrations
Une fois `.env` configuré, exécutez les migrations Laravel pour créer automatiquement la table des demandes de congés (`leave_requests`) :
```bash
php artisan migrate
```

La table générée contiendra notamment les colonnes suivantes :
- `employee_name`, `employee_email`, `start_date`, `end_date`, `reason` pour la demande
- `status`, `decision_notes`, `decision_made_at` pour le suivi des décisions du CA

Si vous ne pouvez pas lancer les migrations, vous pouvez créer la table manuellement avec l'extrait SQL équivalent (à adapter si besoin) :
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
```

## Lancer l'application
1. Démarrez le serveur de développement Laravel :
   ```bash
   php artisan serve
   ```
2. Accédez ensuite à `http://localhost:8000` pour déposer des demandes de congés ou valider/rejeter celles en attente.
