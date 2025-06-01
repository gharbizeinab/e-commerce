<?php
/**
 * Admin Delete Product
 * Handle product deletion
 */

require_once '../config/database.php';
require_once '../config/session.php';
require_once '../includes/functions.php';

// Require admin access
requireAdmin();

// Get product ID
$product_id = intval($_GET['id'] ?? 0);
if (!$product_id) {
    $_SESSION['error_message'] = 'Produit non trouvé.';
    header('Location: products.php');
    exit();
}

// Get product data
$product = getProductById($pdo, $product_id);
if (!$product) {
    $_SESSION['error_message'] = 'Produit non trouvé.';
    header('Location: products.php');
    exit();
}

// Handle deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate CSRF token
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $_SESSION['error_message'] = 'Token de sécurité invalide.';
        header('Location: products.php');
        exit();
    }

    // Check if product is referenced in orders
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM order_items WHERE product_id = ?");
    $stmt->execute([$product_id]);
    $order_count = $stmt->fetch()['count'];

    if ($order_count > 0) {
        $_SESSION['error_message'] = 'Impossible de supprimer ce produit car il est référencé dans des commandes.';
        header('Location: products.php');
        exit();
    }

    // Delete product image if exists
    if ($product['image'] && file_exists('../assets/images/' . $product['image'])) {
        unlink('../assets/images/' . $product['image']);
    }

    // Delete product
    if (deleteProduct($pdo, $product_id)) {
        $_SESSION['success_message'] = 'Produit "' . htmlspecialchars($product['name']) . '" supprimé avec succès.';
    } else {
        $_SESSION['error_message'] = 'Erreur lors de la suppression du produit.';
    }

    header('Location: products.php');
    exit();
}

$page_title = 'Supprimer un produit';
$admin_path = '';
$home_path = '../';
$client_path = '../client/';
$css_path = '../assets/css/';
$js_path = '../assets/js/';
$logout_path = '../';
?>

<?php include '../includes/header.php'; ?>

<div class="row">
    <div class="col-md-3">
        <div class="admin-sidebar">
            <h5 class="mb-3"><i class="fas fa-cog"></i> Administration</h5>
            <nav class="nav flex-column">
                <a class="nav-link" href="index.php">
                    <i class="fas fa-tachometer-alt"></i> Tableau de bord
                </a>
                <a class="nav-link active" href="products.php">
                    <i class="fas fa-shopping-bag"></i> Gestion des produits
                </a>
                <a class="nav-link" href="add_product.php">
                    <i class="fas fa-plus"></i> Ajouter un produit
                </a>
                <a class="nav-link" href="../index.php">
                    <i class="fas fa-eye"></i> Voir le site
                </a>
            </nav>
        </div>
    </div>
    
    <div class="col-md-9">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1><i class="fas fa-trash text-danger"></i> Supprimer un produit</h1>
            <a href="products.php" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Retour à la liste
            </a>
        </div>

        <div class="card border-danger">
            <div class="card-header bg-danger text-white">
                <h5 class="mb-0"><i class="fas fa-exclamation-triangle"></i> Confirmation de suppression</h5>
            </div>
            <div class="card-body">
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle"></i>
                    <strong>Attention !</strong> Cette action est irréversible. Le produit sera définitivement supprimé.
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <?php if ($product['image']): ?>
                            <img src="../assets/images/<?php echo htmlspecialchars($product['image']); ?>" 
                                 alt="<?php echo htmlspecialchars($product['name']); ?>" 
                                 class="img-fluid rounded">
                        <?php else: ?>
                            <div class="bg-light d-flex align-items-center justify-content-center rounded" 
                                 style="height: 200px;">
                                <i class="fas fa-image text-muted fa-3x"></i>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="col-md-8">
                        <h4><?php echo htmlspecialchars($product['name']); ?></h4>
                        <p class="text-muted"><?php echo htmlspecialchars($product['description']); ?></p>
                        
                        <table class="table table-sm">
                            <tr>
                                <th>Catégorie:</th>
                                <td><?php echo htmlspecialchars($product['category_name'] ?? 'Sans catégorie'); ?></td>
                            </tr>
                            <tr>
                                <th>Prix:</th>
                                <td><?php echo formatPrice($product['price']); ?></td>
                            </tr>
                            <tr>
                                <th>Stock:</th>
                                <td><?php echo $product['stock_quantity']; ?> unité(s)</td>
                            </tr>
                            <tr>
                                <th>Créé le:</th>
                                <td><?php echo date('d/m/Y à H:i', strtotime($product['created_at'])); ?></td>
                            </tr>
                        </table>
                    </div>
                </div>

                <hr>

                <p class="mb-4">
                    <strong>Êtes-vous sûr de vouloir supprimer le produit "<?php echo htmlspecialchars($product['name']); ?>" ?</strong>
                </p>

                <form method="POST" class="d-flex justify-content-between">
                    <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                    
                    <a href="products.php" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Annuler
                    </a>
                    
                    <div>
                        <a href="edit_product.php?id=<?php echo $product['id']; ?>" class="btn btn-outline-primary me-2">
                            <i class="fas fa-edit"></i> Modifier plutôt
                        </a>
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-trash"></i> Confirmer la suppression
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
