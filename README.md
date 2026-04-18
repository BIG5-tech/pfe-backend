# PFE Manager — Backend

> API PHP pour la gestion des projets de fin d'études (PFE)

---

## Tech Stack

- **Langage** : PHP 8.2
- **Serveur** : Apache (XAMPP)
- **Base de données** : MySQL (pfe_manager)
- **Sécurité** : password_hash (BCRYPT)
- **Architecture** : MVC (Model-View-Controller)

---

## Architecture
dashbord-backend/
├── config/
│   └── database.php          # Connexion MySQL (PDO)
├── controllers/
│   ├── DashboardController.php
│   ├── ProjectController.php
│   ├── NotificationController.php
│   ├── CompteRenduController.php
│   ├── PreferenceController.php
│   └── StudentController.php
├── .htaccess                 # Réécriture des URLs
└── index.php                 # Router principal

---

## Base de données

**Base :** `pfe_manager`

| Table | Description |
|-------|-------------|
| `users` | Comptes utilisateurs (étudiants, coordinateurs) |
| `students` | Informations spécifiques aux étudiants |
| `roles` | Rôles utilisateurs |
| `projects` | Projets des étudiants |
| `comptes_rendus` | Comptes rendus de réunions |
| `notifications` | Notifications des étudiants |
| `dashboard_preferences` | Préférences de personnalisation |

---

## Routes API

### US1 — Dashboard étudiant

| Méthode | Route | Contrôleur | Description |
|---------|-------|------------|-------------|
| GET | `/api/dashboard/{id}` | DashboardController | Récupérer toutes les données du dashboard |
| POST | `/api/projects/filter/{id}` | ProjectController | Filtrer les projets par statut/catégorie |
| GET | `/api/comptes-rendus/{id}` | CompteRenduController | Récupérer les comptes rendus |
| GET | `/api/notifications/{id}` | NotificationController | Récupérer les notifications |
| PATCH | `/api/notifications/{id}/read` | NotificationController | Marquer une notification comme lue |
| PUT | `/api/preferences/{id}` | PreferenceController | Sauvegarder les préférences |

### US2 — Création de compte étudiant

| Méthode | Route | Contrôleur | Description |
|---------|-------|------------|-------------|
| POST | `/api/students` | StudentController | Créer un compte étudiant |

---

## User Stories

### US1 — Espace étudiant personnalisé
> En tant qu'étudiant, je peux personnaliser mon espace afin de visualiser mes projets, comptes rendus et notifications.

**Sous-tâches réalisées :**
- Endpoint `getDashboard` — récupère projets, comptes rendus, notifications et préférences en une seule requête
- Endpoint `filterProjects` — filtre par statut (en_cours, en_attente, terminé) et catégorie
- Endpoint `getComptesRendus` — tri par date ou titre
- Endpoint `getNotifications` — filtre non lues uniquement
- Endpoint `markAsRead` — marquer notification comme lue
- Endpoint `savePreference` — sauvegarde ou mise à jour des préférences (UPSERT)

---

### US2 — Création de compte étudiant
> En tant que coordinateur, je peux créer un compte étudiant.

**Sous-tâches réalisées :**
- Vérification d'unicité de l'email
- Insertion dans `users` avec rôle `etudiant`
- Insertion dans `students` avec informations détaillées
- Génération automatique d'un mot de passe temporaire (8 caractères)
- Hashage BCRYPT du mot de passe
- Retour du mot de passe temporaire dans la réponse

---

## Configuration

### `config/database.php`

```php
private $host     = "localhost";
private $db_name  = "pfe_manager";
private $username = "root";
private $password = "";
```

---

## CORS

Le backend autorise les requêtes depuis `http://localhost:4200` :

```php
header("Access-Control-Allow-Origin: http://localhost:4200");
header("Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
```

---

## Démarrage

1. Ouvrir **XAMPP Control Panel**
2. Démarrer **Apache**
3. Accéder à `http://localhost/backend/dashbord-backend/api/dashboard/1`

---

## Sécurité

- Mots de passe hashés avec `PASSWORD_BCRYPT`
- Requêtes préparées PDO (protection injection SQL)
- Validation des données entrantes
- Headers CORS configurés

---

## Compte de test
Email    : ahmed@pfe.com
Password : password
Role     : etudiant