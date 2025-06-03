<?php
require_once '../config/database.php';
require_once '../config/session.php';
require_once '../includes/functions.php';

// Require admin access
if (!isLoggedIn() || !isAdmin()) {
    header('Location: ../client/login.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administration - La Beauté Bio</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            min-height: 100vh;
        }
        
        .admin-header {
            background: linear-gradient(135deg, #7c943f, #5c7045);
            color: white;
            padding: 20px 0;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .admin-card {
            background: white;
            border-radius: 15px;
            padding: 30px;
            margin: 20px 0;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            transition: transform 0.3s;
        }
        
        .admin-card:hover {
            transform: translateY(-5px);
        }
        
        .stat-card {
            background: linear-gradient(135deg, #7c943f, #5c7045);
            color: white;
            border-radius: 15px;
            padding: 25px;
            text-align: center;
            margin-bottom: 20px;
        }
        
        .stat-number {
            font-size: 2.5rem;
            font-weight: bold;
            margin-bottom: 10px;
        }
        
        .btn-admin {
            background: linear-gradient(135deg, #7c943f, #5c7045);
            color: white;
            border: none;
            padding: 15px 30px;
            border-radius: 10px;
            font-weight: bold;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            transition: all 0.3s;
            margin: 10px;
        }
        
        .btn-admin:hover {
            background: linear-gradient(135deg, #5c7045, #4a5a37);
            color: white;
            text-decoration: none;
            transform: translateY(-2px);
        }
    </style>
</head>
<body>
    <div class="admin-header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h1><i class="fas fa-tachometer-alt"></i> Administration</h1>
                    <p class="mb-0">Tableau de bord - La Beauté Bio</p>
                </div>
                <div class="col-md-6 text-end">
                    <span class="me-3">Bienvenue, <?php echo htmlspecialchars($_SESSION['full_name'] ?? 'Admin'); ?></span>
                    <a href="../logout.php" class="btn btn-outline-light">
                        <i class="fas fa-sign-out-alt"></i> Déconnexion
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="container mt-4">
        <!-- Statistiques -->
        <div class="row">
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-number">
                        <?php 
                        $result = executeQuery("SELECT COUNT(*) as total FROM products");
                        $row = mysqli_fetch_assoc($result);
                        echo $row['total'] ?? 0;
                        ?>
                    </div>
                    <div>Produits</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-number">
                        <?php 
                        $result = executeQuery("SELECT COUNT(*) as total FROM users WHERE role = 'client'");
                        $row = mysqli_fetch_assoc($result);
                        echo $row['total'] ?? 0;
                        ?>
                    </div>
                    <div>Clients</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-number">
                        <?php 
                        $result = executeQuery("SELECT COUNT(*) as total FROM orders");
                        $row = mysqli_fetch_assoc($result);
                        echo $row['total'] ?? 0;
                        ?>
                    </div>
                    <div>Commandes</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-number">
                        <?php 
                        $result = executeQuery("SELECT COUNT(*) as total FROM products WHERE stock_quantity <= 5");
                        $row = mysqli_fetch_assoc($result);
                        echo $row['total'] ?? 0;
                        ?>
                    </div>
                    <div>Stock Faible</div>
                </div>
            </div>
        </div>

        <!-- Actions Rapides -->
        <div class="admin-card">
            <h3><i class="fas fa-bolt"></i> Actions Rapides</h3>
            <div class="row mt-3">
                <div class="col-md-3">
                    <a href="products.php" class="btn-admin w-100">
                        <i class="fas fa-shopping-bag"></i> Gérer les Produits
                    </a>
                </div>
                <div class="col-md-3">
                    <a href="add_product.php" class="btn-admin w-100">
                        <i class="fas fa-plus"></i> Ajouter un Produit
                    </a>
                </div>
                <div class="col-md-3">
                    <a href="orders.php" class="btn-admin w-100">
                        <i class="fas fa-shopping-cart"></i> Gérer les Commandes
                    </a>
                </div>
                <div class="col-md-3">
                    <a href="../index.php" class="btn-admin w-100">
                        <i class="fas fa-eye"></i> Voir le Site
                    </a>
                </div>
            </div>
        </div>

        <!-- Produits Récents -->
        <div class="admin-card">
            <h3><i class="fas fa-clock"></i> Produits Récents</h3>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Nom</th>
                            <th>Prix</th>
                            <th>Stock</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $result = executeQuery("SELECT * FROM products ORDER BY created_at DESC LIMIT 5");
                        while ($product = mysqli_fetch_assoc($result)):
                        ?>
                        <tr>
                            <td><?php echo htmlspecialchars($product['name']); ?></td>
                            <td><?php echo formatPrice($product['price']); ?></td>
                            <td>
                                <span class="badge <?php echo $product['stock_quantity'] <= 5 ? 'bg-danger' : 'bg-success'; ?>">
                                    <?php echo $product['stock_quantity']; ?>
                                </span>
                            </td>
                            <td>
                                <a href="edit_product.php?id=<?php echo $product['id']; ?>" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-edit"></i> Modifier
                                </a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Navigation -->
        <div class="admin-card">
            <h3><i class="fas fa-sitemap"></i> Navigation</h3>
            <div class="row">
                <div class="col-md-6">
                    <h5>Gestion</h5>
                    <ul class="list-unstyled">
                        <li><a href="products.php" class="text-decoration-none"><i class="fas fa-shopping-bag"></i> Tous les produits</a></li>
                        <li><a href="add_product.php" class="text-decoration-none"><i class="fas fa-plus"></i> Ajouter un produit</a></li>
                        <li><a href="orders.php" class="text-decoration-none"><i class="fas fa-shopping-cart"></i> Toutes les commandes</a></li>
                    </ul>
                </div>
                <div class="col-md-6">
                    <h5>Site Public</h5>
                    <ul class="list-unstyled">
                        <li><a href="../index.php" class="text-decoration-none"><i class="fas fa-home"></i> Page d'accueil</a></li>
                        <li><a href="../products.php" class="text-decoration-none"><i class="fas fa-eye"></i> Catalogue public</a></li>
                        <li><a href="../client/login.php" class="text-decoration-none"><i class="fas fa-sign-in-alt"></i> Page de connexion</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
