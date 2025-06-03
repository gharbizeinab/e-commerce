<?php
/**
 * Products Listing Page - La Beauté Bio
 * Display all products with beautiful design and filtering
 */

require_once 'config/database.php';
require_once 'config/session.php';
require_once 'includes/functions.php';

$page_title = 'Nos Produits - La Beauté Bio';

// La session est déjà démarrée dans config/session.php

// Récupérer les paramètres de filtre
$category_filter = isset($_GET['category']) ? (int)$_GET['category'] : null;
$search_query = isset($_GET['search']) ? sanitizeInput($_GET['search']) : '';

// Construire la requête SQL simple
$sql = "SELECT p.*, c.name as category_name FROM products p
        LEFT JOIN categories c ON p.category_id = c.id
        WHERE p.is_active = 1";

if ($category_filter) {
    $sql .= " AND p.category_id = '$category_filter'";
}

if ($search_query) {
    $sql .= " AND (p.name LIKE '%$search_query%' OR p.description LIKE '%$search_query%' OR p.characteristics LIKE '%$search_query%')";
}

$sql .= " ORDER BY p.is_featured DESC, p.name";

// Exécuter la requête
$result = executeQuery($sql);

// Récupérer tous les produits
$products = array();
while ($row = mysqli_fetch_assoc($result)) {
    $products[] = $row;
}

// Récupérer toutes les catégories pour le filtre
$categories = getCategories();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?></title>

    <!-- Frontend CSS -->
    <link rel="stylesheet" href="assets/css/frontend.css">
    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <style>
        .products-header {
            background: linear-gradient(135deg, #7c943f, #5c7045);
            color: white;
            padding: 60px 0;
            text-align: center;
        }

        .filter-section {
            background: white;
            padding: 30px;
            margin: 30px auto;
            max-width: 1200px;
            border-radius: 15px;
            box-shadow: 0 8px 16px rgba(0,0,0,0.08);
        }

        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 30px;
            max-width: 1200px;
            margin: 0 auto;
            padding: 40px 20px;
        }

        .product-card {
            background-color: #fff;
            border-radius: 15px;
            box-shadow: 0 8px 16px rgba(0,0,0,0.08);
            padding: 20px;
            transition: transform 0.3s, box-shadow 0.3s;
            text-align: center;
        }

        .product-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 12px 24px rgba(0,0,0,0.12);
        }

        .product-card img {
            width: 100%;
            height: 220px;
            object-fit: cover;
            border-radius: 10px;
            margin-bottom: 15px;
        }

        .product-card h3 {
            color: #5c7045;
            margin: 10px 0;
            font-size: 1.3em;
        }

        .product-price {
            font-size: 1.4em;
            font-weight: bold;
            color: #7c943f;
            margin: 15px 0;
        }

        .btn-product {
            background-color: #7c943f;
            color: white;
            border: none;
            padding: 10px 20px;
            font-weight: bold;
            border-radius: 8px;
            cursor: pointer;
            transition: background 0.3s;
            text-decoration: none;
            display: inline-block;
            margin: 5px;
        }

        .btn-product:hover {
            background-color: #5d722e;
            color: white;
        }

        .category-badge {
            background-color: #d8c3a5;
            color: #5c7045;
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 0.9em;
            margin-bottom: 10px;
            display: inline-block;
        }

        .user-menu {
            position: absolute;
            top: 20px;
            right: 20px;
            z-index: 1000;
        }

        .user-menu a {
            background-color: rgba(255,255,255,0.9);
            color: #7c943f;
            padding: 8px 15px;
            border-radius: 20px;
            text-decoration: none;
            margin: 0 5px;
            font-weight: bold;
            transition: all 0.3s;
        }

        .user-menu a:hover {
            background-color: #7c943f;
            color: white;
        }
    </style>
</head>
<body>

<!-- User Menu -->
<div class="user-menu">
    <?php if (isLoggedIn()): ?>
        <a href="client/profile.php"><i class="fas fa-user"></i> Mon Profil</a>
        <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Déconnexion</a>
    <?php else: ?>
        <a href="client/login.php"><i class="fas fa-sign-in-alt"></i> Connexion</a>
        <a href="client/register.php"><i class="fas fa-user-plus"></i> Inscription</a>
    <?php endif; ?>
</div>

<!-- Header -->
<header>
    <nav>
        <a href="index.php">Accueil</a>
        <a href="a-propos.php">À propos</a>
        <a href="products.php" class="active">Nos produits</a>
        <a href="contact.php">Contact</a>
        <a href="panier.php"><i class="fas fa-shopping-cart"></i> Panier</a>
    </nav>
</header>

<!-- Products Header -->
<div class="products-header">
    <h1><i class="fas fa-shopping-bag"></i> Nos Produits Cosmétiques</h1>
    <p>Découvrez notre collection de produits naturels et bio</p>
</div>

<!-- Search and Filters -->
<div class="filter-section">
    <form method="GET" class="row g-3 align-items-end">
        <div class="col-md-5">
            <label for="search" class="form-label" style="color: #7c943f; font-weight: bold;">
                <i class="fas fa-search"></i> Rechercher un produit
            </label>
            <input type="text" class="form-control" id="search" name="search"
                   placeholder="Nom, description, caractéristiques..."
                   value="<?php echo htmlspecialchars($search_query); ?>"
                   style="border: 2px solid #7c943f; border-radius: 10px;">
        </div>
        <div class="col-md-4">
            <label for="category" class="form-label" style="color: #7c943f; font-weight: bold;">
                <i class="fas fa-tags"></i> Catégorie
            </label>
            <select class="form-select" id="category" name="category"
                    style="border: 2px solid #7c943f; border-radius: 10px;">
                <option value="">Toutes les catégories</option>
                <?php foreach ($categories as $category): ?>
                    <option value="<?php echo $category['id']; ?>"
                            <?php echo $category_filter == $category['id'] ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($category['name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-3">
            <button type="submit" class="btn-product me-2">
                <i class="fas fa-search"></i> Rechercher
            </button>
            <a href="products.php" class="btn-product" style="background-color: #6c757d;">
                <i class="fas fa-times"></i> Effacer
            </a>
        </div>
    </form>

    <div style="margin-top: 20px; text-align: center;">
        <p style="color: #7c943f; font-weight: bold;">
            <i class="fas fa-info-circle"></i>
            <?php echo count($products); ?> produit(s) trouvé(s)
            <?php if ($category_filter): ?>
                dans la catégorie "<?php
                    $selected_category = array_filter($categories, function($cat) use ($category_filter) {
                        return $cat['id'] == $category_filter;
                    });
                    echo htmlspecialchars(reset($selected_category)['name']);
                ?>"
            <?php endif; ?>
            <?php if ($search_query): ?>
                pour la recherche "<?php echo htmlspecialchars($search_query); ?>"
            <?php endif; ?>
        </p>
    </div>
</div>

<!-- Products Grid -->
<?php if (empty($products)): ?>
    <div style="text-align: center; padding: 60px 20px;">
        <i class="fas fa-search" style="font-size: 4rem; color: #ccc; margin-bottom: 20px;"></i>
        <h3 style="color: #7c943f;">Aucun produit trouvé</h3>
        <p style="color: #666;">Essayez de modifier vos critères de recherche ou explorez toutes nos catégories.</p>
        <a href="products.php" class="btn-product" style="font-size: 1.2em; padding: 15px 30px;">
            <i class="fas fa-shopping-bag"></i> Voir tous les produits
        </a>
    </div>
<?php else: ?>
    <div class="products-grid">
        <?php foreach ($products as $product): ?>
            <div class="product-card" data-category="<?php echo $product['category_id']; ?>">
                <?php
                // Determine image path with fallback
                $image_path = '';
                if ($product['image'] && file_exists('assets/images/' . $product['image'])) {
                    $image_path = 'assets/images/' . htmlspecialchars($product['image']);
                } else {
                    // Fallback based on category
                    switch ($product['category_id']) {
                        case 1: $image_path = 'assets/images/savon1.jpg'; break;
                        case 2: $image_path = 'assets/images/OIP (1).jpg'; break;
                        case 3: $image_path = 'assets/images/OIP (3).jpg'; break;
                        case 4: $image_path = 'assets/images/OIP (4).jpg'; break;
                        case 5: $image_path = 'assets/images/OIP (6).jpg'; break;
                        default: $image_path = 'assets/images/savon1.jpg';
                    }
                }
                ?>
                <img src="<?php echo $image_path; ?>"
                     alt="<?php echo htmlspecialchars($product['name']); ?>"
                     onerror="this.src='assets/images/savon1.jpg'" />

                <div class="category-badge"><?php echo htmlspecialchars($product['category_name']); ?></div>

                <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                <p style="color: #666; font-size: 0.9em; margin: 10px 0;">
                    <?php echo htmlspecialchars(substr($product['description'], 0, 100)) . '...'; ?>
                </p>

                <div class="product-price"><?php echo formatPrice($product['price']); ?></div>

                <?php if ($product['stock_quantity'] > 0): ?>
                    <small style="color: #28a745;">✓ En stock (<?php echo $product['stock_quantity']; ?>)</small>
                <?php else: ?>
                    <small style="color: #dc3545;">✗ Rupture de stock</small>
                <?php endif; ?>

                <div style="margin-top: 15px;">
                    <a href="product_detail.php?id=<?php echo $product['id']; ?>" class="btn-product">
                        <i class="fas fa-eye"></i> Voir détails
                    </a>

                    <?php if (isClient() && $product['stock_quantity'] > 0): ?>
                        <button class="btn-product" onclick="ajouterAuPanier('<?php echo htmlspecialchars($product['name']); ?>', <?php echo $product['price']; ?>, <?php echo $product['id']; ?>)">
                            <i class="fas fa-shopping-cart"></i> Ajouter au panier
                        </button>
                    <?php elseif (!isLoggedIn()): ?>
                        <a href="client/login.php" class="btn-product">
                            <i class="fas fa-sign-in-alt"></i> Se connecter
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<!-- Footer -->
<footer class="footer">
    <div>
        <h3>Nous suivre</h3>
        <p>
            <a href="#" style="color: white; text-decoration: none;"><i class="fab fa-facebook"></i> Facebook</a> |
            <a href="#" style="color: white; text-decoration: none;"><i class="fab fa-twitter"></i> Twitter</a>
        </p>
    </div>
    <div>
        <h3>Nous parler</h3>
        <p><i class="fas fa-phone"></i> +33 1 23 45 67 89</p>
    </div>
    <div>
        <h3>Nous écrire</h3>
        <p><a href="mailto:contact@labeautebio.fr" style="color: white;"><i class="fas fa-envelope"></i> contact@labeautebio.fr</a></p>
    </div>
</footer>

<div class="bottom-bar">
    <a href="index.php" style="color: white; text-decoration: none;">Retour à l'accueil</a> |
    <a href="panier.php" style="color: white; text-decoration: none;">Mon panier</a>
</div>

<!-- JavaScript -->
<script src="assets/js/cart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Show success/error messages
<?php if (isset($_SESSION['success_message'])): ?>
    alert('<?php echo addslashes($_SESSION['success_message']); unset($_SESSION['success_message']); ?>');
<?php endif; ?>

<?php if (isset($_SESSION['error_message'])): ?>
    alert('<?php echo addslashes($_SESSION['error_message']); unset($_SESSION['error_message']); ?>');
<?php endif; ?>
</script>

</body>
</html>
