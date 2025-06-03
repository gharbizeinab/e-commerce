# 🔐 Accès Administration - La Beauté Bio

## 👨‍💼 Compte Administrateur

### 🔑 Identifiants Admin
```
Email : admin@cosmetics.com
Mot de passe : admin123
```

### 🛠️ Accès à l'Administration

#### **Méthode 1 - Connexion Standard :**
1. Aller sur : `http://localhost/Projet/client/login.php`
2. Se connecter avec les identifiants ci-dessus
3. Redirection automatique vers le dashboard admin

#### **Méthode 2 - Accès Direct :**
1. Se connecter d'abord avec le compte admin
2. Aller directement sur : `http://localhost/Projet/admin/index.php`

### 📋 Pages d'Administration

#### **URLs Directes :**
- **Dashboard :** `http://localhost/Projet/admin/index.php`
- **Produits :** `http://localhost/Projet/admin/products.php`
- **Commandes :** `http://localhost/Projet/admin/orders.php`
- **Ajouter Produit :** `http://localhost/Projet/admin/add_product.php`
- **Modifier Produit :** `http://localhost/Projet/admin/edit_product.php?id=X`

### 🔒 Sécurité

#### **✅ Mesures de Sécurité Appliquées :**
- Identifiants admin **non visibles** sur la page de connexion publique
- Vérification automatique des permissions sur chaque page admin
- Redirection si utilisateur non autorisé
- Sessions sécurisées avec destruction complète à la déconnexion

#### **⚠️ Important :**
- Les identifiants admin ne sont **jamais affichés** publiquement
- Accès admin uniquement par connexion + URL directe
- Aucun lien admin visible dans l'interface client

### 🎯 Fonctionnalités Admin

#### **📊 Dashboard :**
- Statistiques générales (produits, commandes, clients)
- Produits récents
- Alertes stock faible
- Vue d'ensemble des ventes

#### **📦 Gestion Produits :**
- Liste complète des produits
- Ajouter nouveaux produits
- Modifier produits existants
- Supprimer produits (avec vérification commandes)
- Gestion des images et caractéristiques

#### **🛒 Gestion Commandes :**
- Liste de toutes les commandes
- Filtrage par statut
- Actions : confirmer, expédier, livrer
- Détails complets des commandes

### 🧪 Tests

#### **✅ Workflow de Test :**
1. **Connexion admin** → Vérifier redirection dashboard
2. **Navigation admin** → Tester toutes les pages
3. **Gestion produits** → Ajouter/modifier/supprimer
4. **Gestion commandes** → Traiter les commandes
5. **Déconnexion** → Vérifier retour à l'accueil

### 📝 Notes pour Développeurs

#### **🔧 Modification des Identifiants :**
Pour changer les identifiants admin, modifier dans la base de données :
```sql
UPDATE users SET 
    email = 'nouvel@email.com',
    password = 'nouveau_hash_password'
WHERE role = 'admin';
```

#### **🛡️ Sécurité Renforcée :**
- Jamais d'affichage public des identifiants
- Logs des connexions admin (à implémenter)
- Limitation des tentatives de connexion (à implémenter)
- Changement régulier des mots de passe (recommandé)

---

**⚠️ CONFIDENTIEL - Ne pas partager publiquement**
**Usage :** Développement et tests uniquement
