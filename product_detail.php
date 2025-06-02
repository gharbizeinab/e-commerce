<?php
/**
 * Product Detail Page - La Beauté Bio
 * Beautiful product detail page with enhanced styling
 */

require_once 'config/database.php';
require_once 'config/session.php';
require_once 'includes/functions.php';

// Get product ID
$product_id = intval($_GET['id'] ?? 0);
if (!$product_id) {
    $_SESSION['error_message'] = 'Produit non trouvé.';
    header('Location: products.php');
    exit();
}

// Récupérer les données du produit
$product = getProductById($product_id);
if (!$product) {
    $_SESSION['error_message'] = 'Produit non trouvé.';
    header('Location: products.php');
    exit();
}

$page_title = htmlspecialchars($product['name']) . ' - La Beauté Bio';

// Récupérer les produits similaires de la même catégorie
$category_id = cleanInput($product['category_id']);
$current_product_id = cleanInput($product_id);
$sql = "SELECT p.*, c.name as category_name FROM products p
        LEFT JOIN categories c ON p.category_id = c.id
        WHERE p.category_id = '$category_id' AND p.id != '$current_product_id' AND p.is_active = 1
        ORDER BY RAND() LIMIT 4";
$result = executeQuery($sql);

$related_products = array();
while ($row = mysqli_fetch_assoc($result)) {
    $related_products[] = $row;
}
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
        .product-detail-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 40px 20px;
        }

        .product-detail-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            overflow: hidden;
            margin-bottom: 40px;
        }

        .product-image-section {
            position: relative;
            overflow: hidden;
        }

        .product-image-section img {
            width: 100%;
            height: 500px;
            object-fit: cover;
            transition: transform 0.3s ease;
        }

        .product-image-section:hover img {
            transform: scale(1.05);
        }

        .product-info-section {
            padding: 40px;
        }

        .product-title {
            color: #7c943f;
            font-size: 2.5rem;
            font-weight: bold;
            margin-bottom: 15px;
        }

        .product-category {
            background: linear-gradient(135deg, #7c943f, #5c7045);
            color: white;
            padding: 8px 20px;
            border-radius: 25px;
            font-size: 0.9rem;
            display: inline-block;
            margin-bottom: 20px;
        }

        .product-price {
            font-size: 2.5rem;
            font-weight: bold;
            color: #7c943f;
            margin: 20px 0;
        }

        .product-description {
            font-size: 1.1rem;
            line-height: 1.8;
            color: #666;
            margin-bottom: 25px;
        }

        .product-characteristics {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 15px;
            margin: 20px 0;
        }

        .product-characteristics h6 {
            color: #7c943f;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .stock-info {
            background: linear-gradient(135deg, #28a745, #20c997);
            color: white;
            padding: 15px 25px;
            border-radius: 15px;
            margin: 20px 0;
            text-align: center;
            font-weight: bold;
        }

        .stock-info.out-of-stock {
            background: linear-gradient(135deg, #dc3545, #c82333);
        }

        .action-buttons {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-top: 30px;
        }

        .btn-product-action {
            background: linear-gradient(135deg, #7c943f, #5c7045);
            color: white;
            border: none;
            padding: 15px 25px;
            font-weight: bold;
            border-radius: 15px;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
        }

        .btn-product-action:hover {
            background: linear-gradient(135deg, #5c7045, #4a5a37);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.2);
        }

        .btn-secondary-action {
            background: linear-gradient(135deg, #6c757d, #5a6268);
        }

        .btn-secondary-action:hover {
            background: linear-gradient(135deg, #5a6268, #495057);
        }

        .related-products {
            margin-top: 60px;
        }

        .related-products h3 {
            color: #7c943f;
            text-align: center;
            margin-bottom: 40px;
            font-size: 2rem;
        }

        .related-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 30px;
        }

        .related-product {
            background: white;
            border-radius: 15px;
            box-shadow: 0 8px 16px rgba(0,0,0,0.08);
            padding: 20px;
            transition: transform 0.3s;
            text-align: center;
        }

        .related-product:hover {
            transform: translateY(-8px);
        }

        .related-product img {
            width: 100%;
            height: 180px;
            object-fit: cover;
            border-radius: 10px;
            margin-bottom: 15px;
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

        @media (max-width: 768px) {
            .action-buttons {
                grid-template-columns: 1fr;
            }

            .product-title {
                font-size: 2rem;
            }

            .product-price {
                font-size: 2rem;
            }
        }
    </style>
</head>
<body>

<!-- User Menu -->
<div class="user-menu">
    <?php if (isLoggedIn()): ?>
        <?php if (isAdmin()): ?>
            <a href="admin/index.php"><i class="fas fa-cog"></i> Admin</a>
        <?php else: ?>
            <a href="client/profile.php"><i class="fas fa-user"></i> Mon Profil</a>
        <?php endif; ?>
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
        <a href="products.php">Nos produits</a>
        <a href="contact.php">Contact</a>
        <a href="panier.php"><i class="fas fa-shopping-cart"></i> Panier</a>
    </nav>
</header>

<!-- Product Detail Content -->
<div class="product-detail-container">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" style="margin-bottom: 30px;">
        <ol class="breadcrumb" style="background: rgba(255,255,255,0.8); padding: 15px; border-radius: 10px;">
            <li class="breadcrumb-item"><a href="index.php" style="color: #7c943f;">Accueil</a></li>
            <li class="breadcrumb-item"><a href="products.php" style="color: #7c943f;">Produits</a></li>
            <?php if ($product['category_name']): ?>
                <li class="breadcrumb-item">
                    <a href="products.php?category=<?php echo $product['category_id']; ?>" style="color: #7c943f;">
                        <?php echo htmlspecialchars($product['category_name']); ?>
                    </a>
                </li>
            <?php endif; ?>
            <li class="breadcrumb-item active"><?php echo htmlspecialchars($product['name']); ?></li>
        </ol>
    </nav>

    <!-- Main Product Card -->
    <div class="product-detail-card">
        <div class="row g-0">
            <div class="col-lg-6">
                <!-- Product Image -->
                <div class="product-image-section">
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
                         onerror="this.src='assets/images/savon1.jpg'">
                </div>
            </div>

            <div class="col-lg-6">
                <!-- Product Information -->
                <div class="product-info-section">
                    <?php if ($product['category_name']): ?>
                        <div class="product-category">
                            <i class="fas fa-tag"></i> <?php echo htmlspecialchars($product['category_name']); ?>
                        </div>
                    <?php endif; ?>

                    <h1 class="product-title"><?php echo htmlspecialchars($product['name']); ?></h1>

                    <div class="product-price"><?php echo formatPrice($product['price']); ?></div>

                    <div class="product-description">
                        <?php echo nl2br(htmlspecialchars($product['description'])); ?>
                    </div>

                    <?php if ($product['characteristics']): ?>
                        <div class="product-characteristics">
                            <h6><i class="fas fa-list-ul"></i> Caractéristiques</h6>
                            <p><?php echo nl2br(htmlspecialchars($product['characteristics'])); ?></p>
                        </div>
                    <?php endif; ?>

                    <!-- Stock Status -->
                    <?php if ($product['stock_quantity'] > 0): ?>
                        <div class="stock-info">
                            <i class="fas fa-check-circle"></i> En stock - <?php echo $product['stock_quantity']; ?> disponible(s)
                        </div>
                    <?php else: ?>
                        <div class="stock-info out-of-stock">
                            <i class="fas fa-times-circle"></i> Rupture de stock
                        </div>
                    <?php endif; ?>

                    <!-- Action Buttons -->
                    <div class="action-buttons">
                        <?php if (isClient()): ?>
                            <?php if ($product['stock_quantity'] > 0): ?>
                                <button class="btn-product-action" onclick="ajouterAuPanier('<?php echo htmlspecialchars($product['name']); ?>', <?php echo $product['price']; ?>, <?php echo $product['id']; ?>)">
                                    <i class="fas fa-shopping-cart"></i> Ajouter au panier
                                </button>
                                <button class="btn-product-action btn-secondary-action" onclick="addToWishlist(<?php echo $product['id']; ?>)">
                                    <i class="fas fa-heart"></i> Favoris
                                </button>
                            <?php else: ?>
                                <button class="btn-product-action" disabled style="opacity: 0.6;">
                                    <i class="fas fa-times"></i> Indisponible
                                </button>
                                <a href="products.php" class="btn-product-action btn-secondary-action">
                                    <i class="fas fa-arrow-left"></i> Autres produits
                                </a>
                            <?php endif; ?>
                        <?php else: ?>
                            <a href="client/login.php" class="btn-product-action">
                                <i class="fas fa-sign-in-alt"></i> Se connecter
                            </a>
                            <a href="client/register.php" class="btn-product-action btn-secondary-action">
                                <i class="fas fa-user-plus"></i> Créer un compte
                            </a>
                        <?php endif; ?>
                    </div>

                    <?php if (isAdmin()): ?>
                        <div style="margin-top: 30px; padding-top: 20px; border-top: 2px solid #e9ecef;">
                            <h6 style="color: #dc3545; margin-bottom: 15px;">
                                <i class="fas fa-cog"></i> Actions administrateur
                            </h6>
                            <div class="action-buttons">
                                <a href="admin/edit_product.php?id=<?php echo $product['id']; ?>" class="btn-product-action btn-secondary-action">
                                    <i class="fas fa-edit"></i> Modifier
                                </a>
                                <a href="admin/delete_product.php?id=<?php echo $product['id']; ?>"
                                   class="btn-product-action" style="background: linear-gradient(135deg, #dc3545, #c82333);"
                                   onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce produit ?')">
                                    <i class="fas fa-trash"></i> Supprimer
                                </a>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Related Products -->
    <?php if (!empty($related_products)): ?>
        <div class="related-products">
            <h3><i class="fas fa-heart"></i> Produits similaires</h3>
            <div class="related-grid">
                <?php foreach ($related_products as $related_product): ?>
                    <div class="related-product">
                        <?php
                        // Determine image path with fallback for related products
                        $related_image_path = '';
                        if ($related_product['image'] && file_exists('assets/images/' . $related_product['image'])) {
                            $related_image_path = 'assets/images/' . htmlspecialchars($related_product['image']);
                        } else {
                            // Fallback based on category
                            switch ($related_product['category_id']) {
                                case 1: $related_image_path = 'assets/images/savon1.jpg'; break;
                                case 2: $related_image_path = 'assets/images/OIP (1).jpg'; break;
                                case 3: $related_image_path = 'assets/images/OIP (3).jpg'; break;
                                case 4: $related_image_path = 'assets/images/OIP (4).jpg'; break;
                                case 5: $related_image_path = 'assets/images/OIP (6).jpg'; break;
                                default: $related_image_path = 'assets/images/savon1.jpg';
                            }
                        }
                        ?>
                        <img src="<?php echo $related_image_path; ?>"
                             alt="<?php echo htmlspecialchars($related_product['name']); ?>"
                             onerror="this.src='assets/images/savon1.jpg'">

                        <h5 style="color: #7c943f; margin: 15px 0 10px 0;">
                            <?php echo htmlspecialchars($related_product['name']); ?>
                        </h5>
                        <p style="color: #666; font-size: 0.9em; margin-bottom: 10px;">
                            <?php echo htmlspecialchars(substr($related_product['description'], 0, 80)) . '...'; ?>
                        </p>
                        <p style="font-size: 1.2em; font-weight: bold; color: #7c943f; margin: 10px 0;">
                            <?php echo formatPrice($related_product['price']); ?>
                        </p>

                        <a href="product_detail.php?id=<?php echo $related_product['id']; ?>" class="btn-product-action" style="width: 100%; margin-top: 10px;">
                            <i class="fas fa-eye"></i> Voir détails
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>
</div>

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
        <p><i class="fas fa-phone"></i> +216 71 123 456</p>
        <p><i class="fas fa-mobile-alt"></i> +216 98 765 432</p>
    </div>
    <div>
        <h3>Nous écrire</h3>
        <p><a href="mailto:contact@labeautebio.tn" style="color: white;"><i class="fas fa-envelope"></i> contact@labeautebio.tn</a></p>
    </div>
</footer>

<div class="bottom-bar">
    <a href="index.php" style="color: white; text-decoration: none;">Retour à l'accueil</a> |
    <a href="products.php" style="color: white; text-decoration: none;">Tous nos produits</a> |
    <a href="panier.php" style="color: white; text-decoration: none;">Mon panier</a>
</div>

<!-- JavaScript -->
<script src="assets/js/cart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Add to wishlist functionality (placeholder)
function addToWishlist(productId) {
    alert('Produit ajouté aux favoris !');
}

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
