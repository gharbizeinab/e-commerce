<?php
/**
 * Admin Products Management
 * List, edit, and delete products
 */

require_once '../config/database.php';
require_once '../config/session.php';
require_once '../includes/functions.php';

// Require admin access
requireAdmin();

$page_title = 'Gestion des produits';

// Get all products with category information
$stmt = $pdo->query("SELECT p.*, c.name as category_name FROM products p
                    LEFT JOIN categories c ON p.category_id = c.id
                    ORDER BY p.name");
$products = $stmt->fetchAll();

// Get categories for filter
$categories = getCategories($pdo);
?>

<?php include '../includes/admin_header.php'; ?>

<div class="container-fluid">
    <div class="row">
        <?php include '../includes/admin_sidebar.php'; ?>

        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2"><i class="fas fa-shopping-bag"></i> Gestion des produits</h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <a href="add_product.php" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Ajouter un produit
                    </a>
                </div>
            </div>

        <!-- Filters and Search -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <input type="text" class="form-control" id="searchInput" 
                               placeholder="Rechercher un produit...">
                    </div>
                    <div class="col-md-4">
                        <select class="form-select" id="categoryFilter">
                            <option value="">Toutes les catégories</option>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?php echo $category['id']; ?>">
                                    <?php echo htmlspecialchars($category['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button class="btn btn-outline-secondary w-100" onclick="clearFilters()">
                            <i class="fas fa-times"></i> Effacer
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Products Table -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-list"></i> Liste des produits 
                    <span class="badge bg-primary"><?php echo count($products); ?></span>
                </h5>
            </div>
            <div class="card-body p-0">
                <?php if (empty($products)): ?>
                    <div class="text-center py-5">
                        <i class="fas fa-shopping-bag text-muted" style="font-size: 3rem;"></i>
                        <h4 class="mt-3 text-muted">Aucun produit</h4>
                        <p class="text-muted">Commencez par ajouter votre premier produit.</p>
                        <a href="add_product.php" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Ajouter un produit
                        </a>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0" id="productsTable">
                            <thead class="table-light">
                                <tr>
                                    <th>Image</th>
                                    <th>Nom</th>
                                    <th>Catégorie</th>
                                    <th>Prix</th>
                                    <th>Stock</th>
                                    <th>Statut</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($products as $product): ?>
                                    <tr data-category="<?php echo $product['category_id']; ?>">
                                        <td>
                                            <?php if ($product['image']): ?>
                                                <img src="../assets/images/<?php echo htmlspecialchars($product['image']); ?>" 
                                                     alt="<?php echo htmlspecialchars($product['name']); ?>" 
                                                     class="img-thumbnail" style="width: 50px; height: 50px; object-fit: cover;">
                                            <?php else: ?>
                                                <div class="bg-light d-flex align-items-center justify-content-center" 
                                                     style="width: 50px; height: 50px;">
                                                    <i class="fas fa-image text-muted"></i>
                                                </div>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <strong><?php echo htmlspecialchars($product['name']); ?></strong>
                                            <br>
                                            <small class="text-muted">
                                                <?php echo htmlspecialchars(substr($product['description'], 0, 50)) . '...'; ?>
                                            </small>
                                        </td>
                                        <td>
                                            <span class="badge bg-secondary">
                                                <?php echo htmlspecialchars($product['category_name'] ?? 'Sans catégorie'); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <strong class="text-primary"><?php echo formatPrice($product['price']); ?></strong>
                                        </td>
                                        <td>
                                            <span class="badge bg-<?php 
                                                if ($product['stock_quantity'] == 0) echo 'danger';
                                                elseif ($product['stock_quantity'] <= 5) echo 'warning';
                                                else echo 'success';
                                            ?>">
                                                <?php echo $product['stock_quantity']; ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php if ($product['stock_quantity'] == 0): ?>
                                                <span class="badge bg-danger">Rupture</span>
                                            <?php elseif ($product['stock_quantity'] <= 5): ?>
                                                <span class="badge bg-warning">Stock faible</span>
                                            <?php else: ?>
                                                <span class="badge bg-success">Disponible</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="../product_detail.php?id=<?php echo $product['id']; ?>" 
                                                   class="btn btn-sm btn-outline-info" 
                                                   data-bs-toggle="tooltip" title="Voir">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="edit_product.php?id=<?php echo $product['id']; ?>" 
                                                   class="btn btn-sm btn-outline-primary" 
                                                   data-bs-toggle="tooltip" title="Modifier">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <a href="delete_product.php?id=<?php echo $product['id']; ?>" 
                                                   class="btn btn-sm btn-outline-danger btn-delete" 
                                                   data-item-name="<?php echo htmlspecialchars($product['name']); ?>"
                                                   data-bs-toggle="tooltip" title="Supprimer">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        </main>
    </div>
</div>

<script>
// Search and filter functionality
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const categoryFilter = document.getElementById('categoryFilter');
    const table = document.getElementById('productsTable');
    const rows = table ? table.querySelectorAll('tbody tr') : [];

    function filterTable() {
        const searchTerm = searchInput.value.toLowerCase();
        const selectedCategory = categoryFilter.value;

        rows.forEach(function(row) {
            const productName = row.cells[1].textContent.toLowerCase();
            const categoryId = row.getAttribute('data-category');
            
            const matchesSearch = productName.includes(searchTerm);
            const matchesCategory = !selectedCategory || categoryId === selectedCategory;
            
            if (matchesSearch && matchesCategory) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }

    if (searchInput) {
        searchInput.addEventListener('input', filterTable);
    }
    
    if (categoryFilter) {
        categoryFilter.addEventListener('change', filterTable);
    }
});

function clearFilters() {
    document.getElementById('searchInput').value = '';
    document.getElementById('categoryFilter').value = '';
    
    // Show all rows
    const rows = document.querySelectorAll('#productsTable tbody tr');
    rows.forEach(function(row) {
        row.style.display = '';
    });
}
</script>

<?php include '../includes/admin_footer.php'; ?>
