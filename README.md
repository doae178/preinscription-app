# Système de Préinscription

## ⚙️ Installation

1. **Cloner le projet**
   ```bash
   git clone [url-du-projet]
   cd preinscription
Installer les dépendances

bash
Copier le code
composer install
Configurer la base de données

Option 1 : Utiliser le fichier .env (Recommandé)

Copier le fichier env.example vers .env :

bash
Copier le code
cp env.example .env
Modifier le fichier .env avec vos informations de base de données :

env
Copier le code
DB_HOST=localhost
DB_NAME=preinscription_db
DB_USER=postgres
DB_PASS=votre_mot_de_passe
DB_PORT=5432
Option 2 : Configuration manuelle

Modifier directement Includes/config.php avec vos paramètres.

🗄️ Importer la base PostgreSQL
Créer la base de données (si elle n’existe pas encore) :

bash
Copier le code
createdb -U postgres preinscription_db
Importer le fichier SQL :

bash
Copier le code
psql -U postgres -d preinscription_db -f DB/base.sql
⚠️ Remplacez postgres par votre utilisateur PostgreSQL et entrez le mot de passe quand demandé.

🔒 Sécurité
Le fichier .env contient des informations sensibles et ne doit JAMAIS être versionné.

.env est déjà ignoré via .gitignore.

Utilisez toujours env.example comme modèle.

Changez les mots de passe par défaut en production.

📂 Structure des fichiers
bash
Copier le code
preinscription/
├── .env                  # Configuration (à créer)
├── env.example           # Exemple de config
├── .gitignore            # Fichiers exclus
├── Includes/
│   ├── db.php            # Connexion DB
│   ├── config.php        # Config alternative
│   └── .htaccess
├── Etudiant/             # Interface étudiant
├── Admin/                # Interface admin
├── Public/               # Frontend
├── DB/
│   └── base.sql          # Dump PostgreSQL
📦 Dépendances
PHP 7.4+

PostgreSQL

Composer

vlucas/phpdotenv

