<?php
/**
 * Savons Category Page
 * Display all soap products
 */

require_once 'config/database.php';
require_once 'config/session.php';
require_once 'includes/functions.php';

$page_title = 'Nos Savons - La Beauté Bio';

// Récupérer l'ID de la catégorie Savons
$sql = "SELECT id FROM categories WHERE name = 'Savons' LIMIT 1";
$result = executeQuery($sql);
$savons_category = mysqli_fetch_assoc($result);
$category_id = $savons_category ? $savons_category['id'] : 1;

// Récupérer tous les produits savons
$category_id_clean = cleanInput($category_id);
$sql = "SELECT p.*, c.name as category_name FROM products p
        LEFT JOIN categories c ON p.category_id = c.id
        WHERE p.category_id = '$category_id_clean' AND p.is_active = 1
        ORDER BY p.is_featured DESC, p.name";
$result = executeQuery($sql);

$savons = array();
while ($row = mysqli_fetch_assoc($result)) {
    $savons[] = $row;
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
        .category-header {
            background: linear-gradient(135deg, #7c943f, #5c7045);
            color: white;
            padding: 80px 0;
            text-align: center;
        }
        
        .category-header h1 {
            font-size: 3rem;
            margin-bottom: 20px;
        }
        
        .category-description {
            max-width: 800px;
            margin: 40px auto;
            padding: 30px;
            background: white;
            border-radius: 15px;
            box-shadow: 0 8px 16px rgba(0,0,0,0.08);
            text-align: center;
        }
        
        .savons-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
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
            height: 220px;
            object-fit: cover;
            border-radius: 10px;
            margin-bottom: 15px;
        }
        
        .savon h3 {
            color: #5c7045;
            margin: 10px 0;
            font-size: 1.3em;
        }
        
        .price {
            font-size: 1.4em;
            font-weight: bold;
            color: #7c943f;
            margin: 15px 0;
        }
        
        .btn-savon {
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
        
        .btn-savon:hover {
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

<!-- Header -->
<header>
    <nav>
        <a href="index.php">Accueil</a>
        <a href="a-propos.php">À propos</a>
        <a href="products.php">Nos produits</a>
        <a href="savons.php" class="active">Savons</a>
        <a href="contact.php">Contact</a>
        <a href="panier.php"><i class="fas fa-shopping-cart"></i> Panier</a>
    </nav>
</header>

<!-- Category Header -->
<div class="category-header">
    <h1><i class="fas fa-soap"></i> Nos Savons Artisanaux</h1>
    <p>Découvrez notre collection de savons naturels fabriqués avec amour</p>
</div>

<!-- Category Description -->
<div class="category-description">
    <h2 style="color: #7c943f; margin-bottom: 20px;">L'Art du Savon Naturel</h2>
    <p style="color: #666; line-height: 1.8;">
        Nos savons sont fabriqués selon des méthodes artisanales traditionnelles, 
        en utilisant uniquement des ingrédients naturels et biologiques. 
        Chaque savon est saponifié à froid pour préserver toutes les propriétés 
        bénéfiques des huiles végétales et des beurres naturels.
    </p>
    <p style="color: #666; line-height: 1.8;">
        Sans sulfates, sans parabènes, sans colorants artificiels - 
        juste la pureté de la nature pour prendre soin de votre peau en douceur.
    </p>
</div>

<!-- Savons Grid -->
<?php if (empty($savons)): ?>
    <div style="text-align: center; padding: 60px 20px;">
        <i class="fas fa-soap" style="font-size: 4rem; color: #ccc; margin-bottom: 20px;"></i>
        <h3 style="color: #7c943f;">Aucun savon disponible</h3>
        <p style="color: #666;">Nos savons seront bientôt disponibles. Revenez nous voir !</p>
        <a href="products.php" class="btn-savon" style="font-size: 1.2em; padding: 15px 30px;">
            <i class="fas fa-shopping-bag"></i> Voir tous nos produits
        </a>
    </div>
<?php else: ?>
    <section class="savons-container">
        <?php foreach ($savons as $savon): ?>
        <div class="savon">
            <?php
            // Determine image path with fallback for savons
            $image_path = '';
            if ($savon['image'] && file_exists('assets/images/' . $savon['image'])) {
                $image_path = 'assets/images/' . htmlspecialchars($savon['image']);
            } else {
                // Use savon images as fallback
                $savon_images = ['savon1.jpg', 'savon2.jpg', 'savon3.jpg', 'savon4.jpg'];
                $image_path = 'assets/images/' . $savon_images[($savon['id'] - 1) % count($savon_images)];
            }
            ?>
            <img src="<?php echo $image_path; ?>"
                 alt="<?php echo htmlspecialchars($savon['name']); ?>"
                 onerror="this.src='assets/images/savon1.jpg'" />
            
            <h3><?php echo htmlspecialchars($savon['name']); ?></h3>
            <p style="color: #666; font-size: 0.9em; margin: 10px 0;">
                <?php echo htmlspecialchars(substr($savon['description'], 0, 100)) . '...'; ?>
            </p>
            
            <?php if ($savon['characteristics']): ?>
                <p style="color: #7c943f; font-size: 0.8em; font-style: italic;">
                    <?php echo htmlspecialchars(substr($savon['characteristics'], 0, 60)) . '...'; ?>
                </p>
            <?php endif; ?>
            
            <div class="price"><?php echo formatPrice($savon['price']); ?></div>
            
            <?php if ($savon['stock_quantity'] > 0): ?>
                <small style="color: #28a745;">✓ En stock (<?php echo $savon['stock_quantity']; ?>)</small>
            <?php else: ?>
                <small style="color: #dc3545;">✗ Rupture de stock</small>
            <?php endif; ?>
            
            <div style="margin-top: 15px;">
                <a href="product_detail.php?id=<?php echo $savon['id']; ?>" class="btn-savon">
                    <i class="fas fa-eye"></i> Voir détails
                </a>
                
                <?php if (isClient() && $savon['stock_quantity'] > 0): ?>
                    <button class="btn-savon" onclick="ajouterAuPanier('<?php echo htmlspecialchars($savon['name']); ?>', <?php echo $savon['price']; ?>)">
                        <i class="fas fa-shopping-cart"></i> Ajouter au panier
                    </button>
                <?php elseif (!isLoggedIn()): ?>
                    <a href="client/login.php" class="btn-savon">
                        <i class="fas fa-sign-in-alt"></i> Se connecter
                    </a>
                <?php endif; ?>
            </div>
        </div>
        <?php endforeach; ?>
    </section>
    
    <div style="text-align: center; margin: 40px 0;">
        <a href="products.php" class="btn-savon" style="font-size: 1.1em; padding: 12px 25px;">
            <i class="fas fa-arrow-left"></i> Voir tous nos produits
        </a>
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
    <a href="products.php" style="color: white; text-decoration: none;">Tous nos produits</a>
</div>

<!-- JavaScript -->
<script src="assets/js/cart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
