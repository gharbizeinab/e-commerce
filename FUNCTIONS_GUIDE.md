# Guide des Fonctions - La Beauté Bio

## 📋 Architecture des Fonctions

### 🔧 config/database.php
**Fonctions de base de données et sécurité :**
- `cleanInput($data)` - Nettoie et sécurise les données
- `executeQuery($sql)` - Exécute une requête SQL

### 🔐 config/session.php  
**Fonctions de session et authentification :**
- `isLoggedIn()` - Vérifie si l'utilisateur est connecté
- `isAdmin()` - Vérifie si l'utilisateur est admin
- `isClient()` - Vérifie si l'utilisateur est client
- `logoutUser()` - Déconnecte l'utilisateur

### 🛠️ includes/functions.php
**Fonctions utilitaires :**
- `isValidEmail($email)` - Valide un email
- `hashPassword($password)` - Hash un mot de passe
- `verifyPassword($password, $hash)` - Vérifie un mot de passe
- `formatPrice($price)` - Formate les prix en TND
- `getUserByEmail($email)` - Récupère un utilisateur par email
- `createUser($data)` - Crée un nouvel utilisateur

## ⚠️ Règles Importantes

### ❌ NE PAS Dupliquer
- **cleanInput()** → Existe dans database.php
- **executeQuery()** → Existe dans database.php  
- **isLoggedIn()** → Existe dans session.php
- **isAdmin()** → Existe dans session.php
- **logoutUser()** → Existe dans session.php

### ✅ Utilisation Correcte
```php
// Inclure les fichiers dans l'ordre
require_once 'config/database.php';    // cleanInput, executeQuery
require_once 'config/session.php';     // isLoggedIn, isAdmin, logoutUser
require_once 'includes/functions.php'; // formatPrice, getUserByEmail, etc.
```

## 🎯 Fonctions par Catégorie

### 🔒 Sécurité
- `cleanInput()` - Nettoie les données utilisateur
- `hashPassword()` - Hash sécurisé des mots de passe
- `verifyPassword()` - Vérification des mots de passe

### 🗄️ Base de Données
- `executeQuery()` - Exécution sécurisée des requêtes
- `getUserByEmail()` - Récupération d'utilisateur
- `createUser()` - Création d'utilisateur

### 👤 Session
- `isLoggedIn()` - État de connexion
- `isAdmin()` - Vérification du rôle admin
- `logoutUser()` - Déconnexion propre

### 🎨 Affichage
- `formatPrice()` - Formatage des prix en TND
- `isValidEmail()` - Validation d'email

## 🚀 Bonnes Pratiques

### ✅ Avant d'Ajouter une Fonction
1. **Vérifier** si elle existe déjà
2. **Choisir** le bon fichier selon la catégorie
3. **Tester** qu'il n'y a pas de conflit

### ✅ Ordre d'Inclusion
```php
// Toujours dans cet ordre
require_once 'config/database.php';
require_once 'config/session.php';
require_once 'includes/functions.php';
```

### ✅ Nommage
- **Fonctions de base** → database.php
- **Fonctions de session** → session.php
- **Fonctions métier** → functions.php

## 🔧 Maintenance

### 📝 Avant Modification
- Vérifier les dépendances
- Tester sur toutes les pages
- Documenter les changements

### 🧪 Tests Essentiels
- Page d'accueil
- Connexion/Déconnexion
- Profil utilisateur
- Administration
- Navigation entre pages

---

**Dernière mise à jour :** Fonctions organisées sans duplication
**Status :** ✅ Toutes les pages fonctionnelles
