# GestUniv

Système de Gestion Universitaire pour l'EMIT (École de Management et d'Innovation Technologique).

## Fonctionnalités

- **Étudiants** : Inscription, réinscription, consultation des promotions et fiches individuelles.
- **Corps Enseignant** : Gestion des professeurs, grades académiques et spécialités.
- **Matières & Examens** : Gestion des crédits, coefficients et programmation des sessions.
- **Notes & Résultats** : Saisie des contrôles, calcul des moyennes pondérées, délibération (admis, ajourné, rattrapage).
- **Emploi du Temps** : Planning hebdomadaire des séances de cours et affectation des salles.
- **Assiduité** : Feuilles de présence et suivi des absences.
- **Documents Officiels** : Impression des relevés de notes et bulletins académiques.
- **Analytique** : Graphiques Chart.js et compteurs statistiques en temps réel.
- **Sécurité** : Requêtes préparées PDO, protection CSRF, hachage bcrypt, journal d'audit de sécurité.

## Installation

### 1. Prérequis
- Serveur Web (Apache ou Nginx)
- PHP 8.0 ou supérieur (avec extension pdo_mysql)
- MySQL 5.7+ ou MariaDB 10.3+

### 2. Base de données
Créer et initialiser la base de données :
```bash
mysql -u root -p < database/schema.sql
mysql -u root -p gestion_universite < database/seed.sql
```

### 3. Configuration
Les paramètres de connexion par défaut se trouvent dans `inclus/config.php` et peuvent être ajustés ou surchargés par variables d'environnement (`DB_HOST`, `DB_USER`, `DB_PASS`, `DB_NAME`).

### 4. Compte initial
- Identifiant : `admin`
- Mot de passe : `admin123`
