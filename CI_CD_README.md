# CI/CD Pipeline Documentation

## 📋 Vue d'ensemble

Ce projet utilise GitHub Actions pour l'intégration et le déploiement continu (CI/CD). Le pipeline est configuré selon les meilleures pratiques de 2025.

## 🚀 Workflows disponibles

### 1. CI Pipeline (`.github/workflows/ci.yml`)

S'exécute automatiquement sur chaque push et pull request vers `main` ou `develop`.

**Jobs inclus :**

- ✅ **Tests** : Exécution des tests Pest sur PHP 8.2 et 8.3
- ✅ **Code Quality** : Vérification du style de code avec Laravel Pint
- ✅ **Security Audit** : Audit de sécurité des dépendances Composer
- ✅ **Build Assets** : Compilation des assets frontend (Vite)
- ✅ **Validate Dependencies** : Validation des fichiers `composer.json` et `package.json`
- ✅ **CI Summary** : Résumé de tous les jobs

**Fonctionnalités :**
- Cache des dépendances Composer et NPM pour accélérer les builds
- Tests parallèles avec Pest
- Base de données MySQL en service pour les tests
- Upload des artefacts (logs, résultats de tests)
- Timeout de sécurité pour éviter les jobs bloqués

### 2. Deploy Pipeline (`.github/workflows/deploy.yml`)

S'exécute automatiquement sur push vers `main` ou manuellement via `workflow_dispatch`.

**Étapes :**
1. Installation des dépendances
2. Build des assets
3. Création du package de déploiement
4. Déploiement (à configurer selon votre infrastructure)

**Environnements :**
- `staging` : Environnement de staging
- `production` : Environnement de production

**Secrets requis :**
- `DEPLOY_HOST` : Adresse du serveur de déploiement
- `DEPLOY_USER` : Utilisateur SSH
- `DEPLOY_KEY` : Clé privée SSH

### 3. Dependabot Auto-merge (`.github/workflows/dependabot.yml`)

Auto-fusion des PRs Dependabot après validation des tests.

## 🔧 Configuration

### Variables d'environnement

Les variables d'environnement sont configurées dans les secrets GitHub :
- Allez dans **Settings > Secrets and variables > Actions**
- Ajoutez les secrets nécessaires

### Environnements

Configurez les environnements dans **Settings > Environments** :
- `staging`
- `production`

## 📊 Bonnes pratiques implémentées (2025)

### ✅ Performance
- **Cache des dépendances** : Composer et NPM sont mis en cache
- **Tests parallèles** : Utilisation de `--parallel` avec Pest
- **Matrix testing** : Tests sur plusieurs versions PHP
- **Timeout** : Limite de temps pour éviter les jobs bloqués

### ✅ Sécurité
- **Audit des dépendances** : Vérification automatique avec `composer audit`
- **Secrets management** : Utilisation des secrets GitHub
- **Environnements protégés** : Déploiement uniquement après validation

### ✅ Qualité du code
- **Linting automatique** : Laravel Pint vérifie le style de code
- **Validation des dépendances** : Vérification de la validité des fichiers
- **Tests obligatoires** : Les tests doivent passer avant le merge

### ✅ Maintenabilité
- **Dependabot** : Mise à jour automatique des dépendances
- **Templates PR** : Standardisation des pull requests
- **Artifacts** : Conservation des logs et rapports

### ✅ Observabilité
- **Rapports détaillés** : Upload des résultats de tests
- **Notifications** : Statut clair des jobs
- **Résumé CI** : Vue d'ensemble de tous les checks

## 🛠️ Utilisation locale

### Exécuter les mêmes checks que le CI

```bash
# Tests
composer test

# Code quality
./vendor/bin/pint --test

# Security audit
composer audit

# Build assets
npm run build
```

## 📝 Personnalisation

### Ajouter un nouveau job

1. Ajoutez un nouveau job dans `.github/workflows/ci.yml`
2. Mettez à jour le job `ci-summary` pour inclure le nouveau job

### Modifier les versions PHP

Modifiez la matrice dans le job `tests` :

```yaml
matrix:
  php-version: ['8.2', '8.3', '8.4']  # Ajoutez les versions souhaitées
```

### Configurer le déploiement

Éditez `.github/workflows/deploy.yml` et adaptez la section "Deploy to server" selon votre infrastructure :
- **SSH** : Déploiement via SSH
- **Docker** : Build et push d'images Docker
- **Cloud** : Déploiement vers AWS, GCP, Azure, etc.
- **Serverless** : Déploiement serverless

## 🔍 Monitoring

### Voir les résultats

1. Allez dans l'onglet **Actions** de votre repository GitHub
2. Cliquez sur un workflow pour voir les détails
3. Téléchargez les artefacts si nécessaire

### Badge de statut

Ajoutez ce badge dans votre README :

```markdown
![CI](https://github.com/votre-org/votre-repo/workflows/CI%20Pipeline/badge.svg)
```

## 🐛 Dépannage

### Les tests échouent

1. Vérifiez les logs dans l'onglet Actions
2. Exécutez les tests localement : `composer test`
3. Vérifiez la configuration de la base de données

### Le cache ne fonctionne pas

1. Vérifiez que `composer.lock` et `package-lock.json` sont commités
2. Le cache est invalidé automatiquement si ces fichiers changent

### Le déploiement échoue

1. Vérifiez que les secrets sont correctement configurés
2. Testez la connexion SSH manuellement
3. Vérifiez les permissions sur le serveur

## 📚 Ressources

- [GitHub Actions Documentation](https://docs.github.com/en/actions)
- [Laravel Testing](https://laravel.com/docs/testing)
- [Pest PHP](https://pestphp.com/)
- [Laravel Pint](https://laravel.com/docs/pint)

