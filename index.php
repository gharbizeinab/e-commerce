<?php
/**
 * Homepage - La Beauté Bio
 * Main landing page with beautiful frontend design and PHP backend integration
 */

require_once 'config/database.php';
require_once 'config/session.php';
require_once 'includes/functions.php';

$page_title = 'La Beauté Bio - Cosmétiques Naturels';

// Récupérer les produits en vedette (8 derniers produits)
$sql = "SELECT p.*, c.name as category_name FROM products p
        LEFT JOIN categories c ON p.category_id = c.id
        WHERE p.is_active = 1
        ORDER BY p.is_featured DESC, p.created_at DESC LIMIT 8";
$result = executeQuery($sql);

$featured_products = array();
while ($row = mysqli_fetch_assoc($result)) {
    $featured_products[] = $row;
}

// Récupérer les catégories pour la navigation
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
    <!-- Bootstrap for additional components -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <style>
        /* Additional styles for PHP integration */
        .savons-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 30px;
            max-width: 1200px;
            margin: 0 auto;
            padding: 40px 20px;
        }

        .savon {
            background-color: #fff;
            border-radius: 15px;
            box-shadow: 0 8px 16px rgba(0,0,0,0.08);
            padding: 20px;
            transition: transform 0.3s, box-shadow 0.3s;
            text-align: center;
        }

        .savon:hover {
            transform: translateY(-8px);
            box-shadow: 0 12px 24px rgba(0,0,0,0.12);
        }

        .savon img {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: 10px;
            margin-bottom: 15px;
        }

        .savon h3 {
            color: #5c7045;
            margin: 10px 0;
            font-size: 1.2em;
        }

        .price {
            font-size: 1.3em;
            font-weight: bold;
            color: #7c943f;
            margin: 10px 0;
        }

        .btn-add-cart {
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

        .btn-add-cart:hover {
            background-color: #5d722e;
            color: white;
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

<!-- Header with Navigation -->
<header>
    <nav>
        <a href="index.php" class="active">Accueil</a>
        <a href="a-propos.php">À propos</a>
        <a href="products.php">Nos produits</a>
        <a href="contact.php">Contact</a>
        <a href="panier.php"><i class="fas fa-shopping-cart"></i> Panier</a>
    </nav>
</header>

<!-- Banner Section -->
<section id="apropos" class="banner-container">
    <img src="assets/images/savons.jpg" alt="Savons Naturels" class="banner-image" />
    <div class="bio-circle">
        <img src="assets/images/brand.png" style="width: 300px;" alt="Logo La Beauté Bio"/>
    </div>
</section>

<!-- À propos Section -->
<section style="padding: 40px; text-align: center;">
    <h2>À propos de La Beauté Bio</h2>
    <p style="max-width: 800px; margin: auto; font-size: 1.1em; line-height: 1.6;">
        Chez <strong>La Beauté Bio</strong>, nous croyons en une beauté authentique, respectueuse de la nature et de votre peau.
        Tous nos produits sont fabriqués à partir d'ingrédients d'origine naturelle, choisis avec soin pour leur efficacité et leur douceur.
        Notre mission est de proposer des soins qui allient bien-être, élégance et engagement éthique.
        Chaque savon, parfum ou cosmétique est une invitation à prendre soin de soi, dans le respect de l'environnement.
    </p>
</section>

<!-- Produits Section -->
<section id="produits">
    <h2 style="text-align: center; color: #7c943f; font-size: 2.5em; margin-bottom: 30px;">Nos Produits</h2>
    <section class="savons-container">
        <?php foreach ($featured_products as $product): ?>
        <div class="savon">
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

            <h3><?php echo htmlspecialchars($product['name']); ?></h3>
            <p class="price"><?php echo formatPrice($product['price']); ?></p>

            <?php if ($product['stock_quantity'] > 0): ?>
                <small style="color: #28a745;">✓ En stock (<?php echo $product['stock_quantity']; ?>)</small><br>
            <?php else: ?>
                <small style="color: #dc3545;">✗ Rupture de stock</small><br>
            <?php endif; ?>

            <div style="margin-top: 15px;">
                <a href="product_detail.php?id=<?php echo $product['id']; ?>" class="btn-add-cart">
                    <i class="fas fa-eye"></i> Voir détails
                </a>

                <?php if (isClient() && $product['stock_quantity'] > 0): ?>
                    <button class="btn-add-cart" onclick="ajouterAuPanier('<?php echo htmlspecialchars($product['name']); ?>', <?php echo $product['price']; ?>, <?php echo $product['id']; ?>)">
                        <i class="fas fa-shopping-cart"></i> Ajouter au panier
                    </button>
                <?php elseif (!isLoggedIn()): ?>
                    <a href="client/login.php" class="btn-add-cart">
                        <i class="fas fa-sign-in-alt"></i> Se connecter
                    </a>
                <?php endif; ?>
            </div>
        </div>
        <?php endforeach; ?>
    </section>

    <div style="text-align: center; margin-top: 40px;">
        <a href="products.php" class="btn-add-cart" style="font-size: 1.2em; padding: 15px 30px;">
            <i class="fas fa-shopping-bag"></i> Voir tous nos produits
        </a>
    </div>
</section>

<!-- Contact Section -->
<section id="contact" style="padding: 40px; text-align: center;">
    <h2>Contactez-nous</h2>
    <p>Une question, une suggestion, un mot doux ?</p>
    <p style="margin-top: 30px;">
        <a href="contact.php" class="btn-add-cart" style="font-size: 1.2em; padding: 15px 30px;">
            <i class="fas fa-envelope"></i> Formulaire de contact complet
        </a>
    </p>
    <div style="margin-top: 30px;">
        <p><i class="fas fa-phone"></i> <strong>Téléphone :</strong> +216 71 123 456</p>
        <p><i class="fas fa-mobile-alt"></i> <strong>Mobile :</strong> +216 98 765 432</p>
        <p><i class="fas fa-envelope"></i> <strong>Email :</strong> contact@labeautebio.tn</p>
        <p><i class="fas fa-map-marker-alt"></i> <strong>Adresse :</strong> Avenue Habib Bourguiba, Centre Ville, 1001 Tunis, Tunisie</p>
    </div>
</section>

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
    <a href="#" style="color: white; text-decoration: none;">Mentions légales</a> |
    <a href="#" style="color: white; text-decoration: none;">Politique de cookies</a> |
    <a href="#" style="color: white; text-decoration: none;">Confidentialité</a> |
    <a href="#" style="color: white; text-decoration: none;">Conditions</a>
</div>

<!-- JavaScript -->
<script src="assets/js/cart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Smooth scrolling for anchor links
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
            target.scrollIntoView({
                behavior: 'smooth'
            });
        }
    });
});

// Show success message if any
<?php if (isset($_SESSION['success_message'])): ?>
    alert('<?php echo addslashes($_SESSION['success_message']); unset($_SESSION['success_message']); ?>');
<?php endif; ?>

// Show error message if any
<?php if (isset($_SESSION['error_message'])): ?>
    alert('<?php echo addslashes($_SESSION['error_message']); unset($_SESSION['error_message']); ?>');
<?php endif; ?>
</script>

</body>
</html>
