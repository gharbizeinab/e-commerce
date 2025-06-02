<?php
/**
 * Admin Dashboard
 * Main administration panel with statistics and quick actions
 */

require_once '../config/database.php';
require_once '../config/session.php';
require_once '../includes/functions.php';

// Require admin access
requireAdmin();

$page_title = 'Administration';

// Get statistics
$stats = [];

// Total products
$stmt = $connection->query("SELECT COUNT(*) as total FROM products");
$stats['total_products'] = $stmt->fetch()['total'];

// Total categories
$stmt = $connection->query("SELECT COUNT(*) as total FROM categories");
$stats['total_categories'] = $stmt->fetch()['total'];

// Total users (clients)
$stmt = $connection->query("SELECT COUNT(*) as total FROM users WHERE role = 'client'");
$stats['total_clients'] = $stmt->fetch()['total'];

// Total orders
$stmt = $connection->query("SELECT COUNT(*) as total FROM orders");
$stats['total_orders'] = $stmt->fetch()['total'];

// Pending orders
$stmt = $connection->query("SELECT COUNT(*) as total FROM orders WHERE status = 'pending'");
$stats['pending_orders'] = $stmt->fetch()['total'];

// Low stock products (stock <= 5)
$stmt = $connection->query("SELECT COUNT(*) as total FROM products WHERE stock_quantity <= 5");
$stats['low_stock_products'] = $stmt->fetch()['total'];

// Recent products
$stmt = $connection->query("SELECT p.*, c.name as category_name FROM products p 
                    LEFT JOIN categories c ON p.category_id = c.id 
                    ORDER BY p.created_at DESC LIMIT 5");
$recent_products = array();
while ($row = mysqli_fetch_assoc($result)) {
    $recent_products[] = $row;
}

// Low stock products
$stmt = $connection->query("SELECT p.*, c.name as category_name FROM products p 
                    LEFT JOIN categories c ON p.category_id = c.id 
                    WHERE p.stock_quantity <= 5 
                    ORDER BY p.stock_quantity ASC LIMIT 10");
$low_stock_products = array();
while ($row = mysqli_fetch_assoc($result)) {
    $low_stock_products[] = $row;
}
?>

<?php include '../includes/admin_header.php'; ?>

<div class="container-fluid">
    <div class="row">
        <?php include '../includes/admin_sidebar.php'; ?>

        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2"><i class="fas fa-tachometer-alt"></i> Tableau de bord</h1>
                <div class="text-muted">
                    Bienvenue, <?php echo htmlspecialchars($_SESSION['full_name']); ?>
                </div>
            </div>

        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-md-6 col-lg-3 mb-3">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h4 class="mb-0"><?php echo $stats['total_products']; ?></h4>
                                <p class="mb-0">Produits</p>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-shopping-bag fa-2x"></i>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <a href="products.php" class="text-white text-decoration-none">
                            <small>Gérer les produits <i class="fas fa-arrow-right"></i></small>
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-lg-3 mb-3">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h4 class="mb-0"><?php echo $stats['total_orders']; ?></h4>
                                <p class="mb-0">Commandes</p>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-shopping-cart fa-2x"></i>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <a href="orders.php" class="text-white text-decoration-none">
                            <small>Gérer les commandes <i class="fas fa-arrow-right"></i></small>
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-lg-3 mb-3">
                <div class="card bg-warning text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h4 class="mb-0"><?php echo $stats['pending_orders']; ?></h4>
                                <p class="mb-0">En attente</p>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-clock fa-2x"></i>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <a href="orders.php?status=pending" class="text-white text-decoration-none">
                            <small>Commandes à traiter <i class="fas fa-arrow-right"></i></small>
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-lg-3 mb-3">
                <div class="card bg-danger text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h4 class="mb-0"><?php echo $stats['low_stock_products']; ?></h4>
                                <p class="mb-0">Stock faible</p>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-exclamation-triangle fa-2x"></i>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <small>Produits à réapprovisionner</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-bolt"></i> Actions rapides</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3 mb-2">
                                <a href="add_product.php" class="btn btn-primary w-100">
                                    <i class="fas fa-plus"></i> Ajouter un produit
                                </a>
                            </div>
                            <div class="col-md-3 mb-2">
                                <a href="products.php" class="btn btn-outline-primary w-100">
                                    <i class="fas fa-edit"></i> Modifier les produits
                                </a>
                            </div>
                            <div class="col-md-3 mb-2">
                                <a href="../products.php" class="btn btn-outline-secondary w-100">
                                    <i class="fas fa-eye"></i> Voir le catalogue
                                </a>
                            </div>
                            <div class="col-md-3 mb-2">
                                <a href="../index.php" class="btn btn-outline-info w-100">
                                    <i class="fas fa-home"></i> Voir le site
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Recent Products -->
            <div class="col-lg-6 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-clock"></i> Produits récents</h5>
                    </div>
                    <div class="card-body">
                        <?php if (empty($recent_products)): ?>
                            <p class="text-muted">Aucun produit trouvé.</p>
                        <?php else: ?>
                            <div class="list-group list-group-flush">
                                <?php foreach ($recent_products as $product): ?>
                                    <div class="list-group-item d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="mb-1"><?php echo htmlspecialchars($product['name']); ?></h6>
                                            <small class="text-muted"><?php echo htmlspecialchars($product['category_name']); ?></small>
                                        </div>
                                        <div class="text-end">
                                            <span class="badge bg-primary"><?php echo formatPrice($product['price']); ?></span>
                                            <br>
                                            <small class="text-muted">Stock: <?php echo $product['stock_quantity']; ?></small>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                        <div class="card-footer bg-transparent">
                            <a href="products.php" class="btn btn-sm btn-outline-primary">
                                Voir tous les produits <i class="fas fa-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Low Stock Alert -->
            <div class="col-lg-6 mb-4">
                <div class="card">
                    <div class="card-header bg-warning text-dark">
                        <h5 class="mb-0"><i class="fas fa-exclamation-triangle"></i> Alertes stock</h5>
                    </div>
                    <div class="card-body">
                        <?php if (empty($low_stock_products)): ?>
                            <p class="text-success"><i class="fas fa-check"></i> Tous les produits sont bien approvisionnés.</p>
                        <?php else: ?>
                            <div class="list-group list-group-flush">
                                <?php foreach ($low_stock_products as $product): ?>
                                    <div class="list-group-item d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="mb-1"><?php echo htmlspecialchars($product['name']); ?></h6>
                                            <small class="text-muted"><?php echo htmlspecialchars($product['category_name']); ?></small>
                                        </div>
                                        <div class="text-end">
                                            <span class="badge bg-<?php echo $product['stock_quantity'] == 0 ? 'danger' : 'warning'; ?>">
                                                <?php echo $product['stock_quantity']; ?> restant(s)
                                            </span>
                                            <br>
                                            <a href="edit_product.php?id=<?php echo $product['id']; ?>" class="btn btn-sm btn-outline-primary">
                                                Modifier
                                            </a>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        </main>
    </div>
</div>

<?php include '../includes/admin_footer.php'; ?>
