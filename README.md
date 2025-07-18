# IFran Presence - Système de Gestion des Présences

## 📋 Description

IFran Presence est un système complet de gestion des présences pour les établissements d'enseignement. Il permet de gérer les emplois du temps, les séances, les présences, les justifications et les statistiques avec un système de rôles avancé.

## ✨ Fonctionnalités Principales

### 🎯 Gestion des Rôles
- **Administrateur** : Accès complet au système
- **Coordinateur** : Gestion des classes et des emplois du temps
- **Enseignant** : Saisie des présences et consultation des statistiques
- **Étudiant** : Consultation de ses présences et justifications
- **Parent** : Suivi des présences de ses enfants

### 📅 Gestion des Emplois du Temps
- Création et modification d'emplois du temps
- Génération automatique des séances
- Gestion des types de cours (présentiel, e-learning, atelier)
- Planification hebdomadaire

### 📊 Système de Présences
- Saisie des présences par les enseignants
- Gestion des retards et absences
- Système de justifications
- Calcul automatique des taux de présence

### 📈 Statistiques et Rapports
- Statistiques détaillées par classe, module, étudiant
- Graphiques interactifs avec Chart.js
- Rapports exportables (PDF, Excel, CSV)
- Évolution des présences dans le temps

### 🔔 Système de Notifications
- Notifications en temps réel
- Alertes automatiques (absences, retards, faible taux)
- Notifications par email et interface
- Gestion des préférences de notification

### 🚨 Système d'Alertes
- Détection automatique des absences consécutives
- Alertes de faible taux de présence
- Notifications de retards fréquents
- Tableau de bord des alertes

## 🛠️ Technologies Utilisées

- **Backend** : Laravel 10.x
- **Frontend** : Blade, Vue.js, Tailwind CSS
- **Base de données** : SQLite/MySQL
- **Graphiques** : Chart.js
- **Notifications** : Système personnalisé
- **Export** : DomPDF, Maatwebsite Excel

## 📦 Installation

### Prérequis
- PHP 8.1+
- Composer
- Node.js et npm
- Base de données SQLite ou MySQL

### Étapes d'installation

1. **Cloner le repository**
```bash
git clone [url-du-repo]
cd ifran-presence
```

2. **Installer les dépendances PHP**
```bash
composer install
```

3. **Installer les dépendances Node.js**
```bash
npm install
```

4. **Configurer l'environnement**
```bash
cp .env.example .env
php artisan key:generate
```

5. **Configurer la base de données**
```bash
# Pour SQLite
touch database/database.sqlite
# Modifier .env pour utiliser SQLite

# Pour MySQL
# Modifier .env avec les informations de connexion
```

6. **Exécuter les migrations**
```bash
php artisan migrate
```

7. **Générer les données de test**
```bash
php artisan db:seed
```

8. **Compiler les assets**
```bash
npm run build
```

9. **Démarrer le serveur**
```bash
php artisan serve
```

## 🗄️ Structure de la Base de Données

### Tables Principales
- `users` : Utilisateurs avec rôles
- `classes` : Classes d'étudiants
- `modules` : Modules d'enseignement
- `emploi_du_temps` : Emplois du temps
- `seances` : Séances générées
- `presences` : Présences des étudiants
- `justifications` : Justifications d'absences
- `notifications` : Notifications système

### Relations
- Un coordinateur gère une classe
- Un enseignant peut enseigner plusieurs modules
- Un étudiant appartient à une classe
- Une séance est liée à un emploi du temps
- Une présence est liée à une séance et un étudiant

## 🔧 Configuration

### Fichiers de Configuration
- `config/presence.php` : Paramètres de présence
- `config/notifications.php` : Configuration des notifications
- `config/export.php` : Paramètres d'export
- `config/backup.php` : Configuration des sauvegardes
- `config/interface.php` : Paramètres d'interface
- `config/security.php` : Paramètres de sécurité

### Variables d'Environnement Importantes
```env
APP_NAME="IFran Presence"
APP_ENV=production
APP_DEBUG=false
APP_URL=http://localhost

DB_CONNECTION=sqlite
DB_DATABASE=database/database.sqlite

MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="noreply@ifran-presence.com"
MAIL_FROM_NAME="${APP_NAME}"
```

## 🚀 Utilisation

### Création d'un Compte Administrateur
```bash
php artisan tinker
```
```php
User::create([
    'nom' => 'Admin',
    'prenom' => 'System',
    'email' => 'admin@ifran-presence.com',
    'password' => Hash::make('password'),
    'role' => 'admin'
]);
```

### Génération des Séances
```bash
php artisan seances:generer
```

### Nettoyage du Cache
```bash
php artisan cache:clear
php artisan config:clear
php artisan view:clear
```

## 📊 Fonctionnalités Avancées

### Services Implémentés
- **StatistiqueService** : Calculs statistiques avancés
- **AlerteService** : Gestion des alertes automatiques
- **SeanceGenerationService** : Génération automatique des séances
- **NotificationService** : Système de notifications

### Middleware de Sécurité
- **PermissionMiddleware** : Gestion des permissions avancées
- **RoleMiddleware** : Vérification des rôles
- **AuditMiddleware** : Audit des actions utilisateurs

### Composants Vue.js
- **NotificationDropdown** : Menu des notifications
- **StatPresence** : Graphiques de présence
- **DataTable** : Tableaux de données avancés

## 🔒 Sécurité

### Fonctionnalités de Sécurité
- Authentification multi-rôles
- Protection CSRF
- Validation des données
- Limitation de débit
- Audit des actions
- Chiffrement des données sensibles

### Bonnes Pratiques
- Validation stricte des entrées
- Échappement des sorties
- Protection contre les injections SQL
- Gestion sécurisée des sessions
- Logs de sécurité

## 📈 Performance

### Optimisations
- Cache des requêtes fréquentes
- Pagination des résultats
- Lazy loading des relations
- Compression des assets
- Optimisation des images

### Monitoring
- Logs d'erreurs
- Métriques de performance
- Surveillance des ressources
- Alertes automatiques

## 🧪 Tests

### Exécution des Tests
```bash
php artisan test
```

### Tests Disponibles
- Tests unitaires des modèles
- Tests d'intégration des contrôleurs
- Tests des services
- Tests des middlewares

## 📝 API Documentation

### Endpoints Principaux
- `GET /api/presences` : Liste des présences
- `POST /api/presences` : Créer une présence
- `GET /api/statistiques` : Statistiques
- `GET /api/notifications` : Notifications

### Authentification
L'API utilise l'authentification Laravel Sanctum.

## 🔄 Maintenance

### Tâches Planifiées
```bash
# Génération automatique des séances
php artisan schedule:work

# Nettoyage des logs
php artisan log:clear

# Sauvegarde automatique
php artisan backup:run
```

### Commandes Utiles
```bash
# Vérifier l'état du système
php artisan system:check

# Optimiser la base de données
php artisan db:optimize

# Nettoyer les anciens exports
php artisan export:clean
```

## 🤝 Contribution

### Guide de Contribution
1. Fork le projet
2. Créer une branche feature
3. Commiter les changements
4. Pousser vers la branche
5. Créer une Pull Request

### Standards de Code
- PSR-12 pour PHP
- ESLint pour JavaScript
- Tests obligatoires
- Documentation mise à jour

## 📄 Licence

Ce projet est sous licence MIT. Voir le fichier `LICENSE` pour plus de détails.

## 📞 Support

### Contact
- Email : support@ifran-presence.com
- Documentation : [Lien vers la documentation]
- Issues : [Lien vers GitHub Issues]

### Ressources
- [Documentation Laravel](https://laravel.com/docs)
- [Guide Tailwind CSS](https://tailwindcss.com/docs)
- [Documentation Vue.js](https://vuejs.org/guide/)

## 🎯 Roadmap

### Versions Futures
- [ ] Application mobile
- [ ] Intégration calendrier
- [ ] Système de badges
- [ ] IA pour prédiction des absences
- [ ] Intégration avec d'autres systèmes
- [ ] API publique
- [ ] Multi-tenant
- [ ] Offline mode

---

**Développé avec ❤️ pour IFran**
