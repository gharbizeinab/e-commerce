<?php
/**
 * Admin Sidebar Navigation
 */
$current_page = basename($_SERVER['PHP_SELF']);
?>

<nav id="sidebarMenu" class="col-md-3 col-lg-2 d-md-block bg-light sidebar collapse">
    <div class="position-sticky pt-3">
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link <?php echo $current_page === 'index.php' ? 'active' : ''; ?>" href="index.php">
                    <i class="fas fa-tachometer-alt"></i> Tableau de bord
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo in_array($current_page, ['products.php', 'add_product.php', 'edit_product.php']) ? 'active' : ''; ?>" href="products.php">
                    <i class="fas fa-box"></i> Produits
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo $current_page === 'orders.php' ? 'active' : ''; ?>" href="orders.php">
                    <i class="fas fa-shopping-cart"></i> Commandes
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo $current_page === 'categories.php' ? 'active' : ''; ?>" href="categories.php">
                    <i class="fas fa-tags"></i> Catégories
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo $current_page === 'users.php' ? 'active' : ''; ?>" href="users.php">
                    <i class="fas fa-users"></i> Utilisateurs
                </a>
            </li>
        </ul>

        <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted">
            <span>Actions rapides</span>
        </h6>
        <ul class="nav flex-column mb-2">
            <li class="nav-item">
                <a class="nav-link" href="add_product.php">
                    <i class="fas fa-plus"></i> Ajouter un produit
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="../index.php" target="_blank">
                    <i class="fas fa-external-link-alt"></i> Voir le site
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="../logout.php">
                    <i class="fas fa-sign-out-alt"></i> Déconnexion
                </a>
            </li>
        </ul>
    </div>
</nav>
