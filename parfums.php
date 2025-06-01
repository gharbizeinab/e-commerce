<?php
/**
 * Parfums Category Page
 * Display all perfume products
 */

require_once 'config/database.php';
require_once 'config/session.php';
require_once 'includes/functions.php';

$page_title = 'Nos Parfums - La Beauté Bio';

// Get parfums category ID
$stmt = $pdo->prepare("SELECT id FROM categories WHERE name = 'Parfums' LIMIT 1");
$stmt->execute();
$parfums_category = $stmt->fetch();
$category_id = $parfums_category ? $parfums_category['id'] : 2;

// Get all parfum products
$stmt = $pdo->prepare("SELECT p.*, c.name as category_name FROM products p 
                      LEFT JOIN categories c ON p.category_id = c.id 
                      WHERE p.category_id = ? AND p.is_active = 1
                      ORDER BY p.is_featured DESC, p.name");
$stmt->execute([$category_id]);
$parfums = $stmt->fetchAll();
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
            background: linear-gradient(135deg, #8b5a8c, #6d4c7d);
            color: white;
            padding: 80px 0;
            text-align: center;
        }
        
        .category-header h1 {
            font-size: 3rem;
            margin-bottom: 20px;
        }
        
        .parfums-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
            max-width: 1200px;
            margin: 0 auto;
            padding: 40px 20px;
        }
        
        .parfum {
            background: linear-gradient(145deg, #ffffff, #f8f9fa);
            border-radius: 20px;
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
            padding: 25px;
            transition: transform 0.3s, box-shadow 0.3s;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        
        .parfum::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #8b5a8c, #6d4c7d);
        }
        
        .parfum:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.15);
        }
        
        .parfum img {
            width: 100%;
            height: 250px;
            object-fit: cover;
            border-radius: 15px;
            margin-bottom: 20px;
        }
        
        .parfum h3 {
            color: #6d4c7d;
            margin: 15px 0;
            font-size: 1.4em;
            font-weight: 600;
        }
        
        .parfum-notes {
            background: #f8f9fa;
            padding: 10px;
            border-radius: 10px;
            margin: 15px 0;
            font-size: 0.9em;
            color: #666;
        }
        
        .price {
            font-size: 1.5em;
            font-weight: bold;
            color: #8b5a8c;
            margin: 20px 0;
        }
        
        .btn-parfum {
            background: linear-gradient(135deg, #8b5a8c, #6d4c7d);
            color: white;
            border: none;
            padding: 12px 25px;
            font-weight: bold;
            border-radius: 25px;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-block;
            margin: 5px;
        }
        
        .btn-parfum:hover {
            background: linear-gradient(135deg, #6d4c7d, #5a3e5b);
            color: white;
            transform: translateY(-2px);
        }
        
        .user-menu {
            position: absolute;
            top: 20px;
            right: 20px;
            z-index: 1000;
        }
        
        .user-menu a {
            background-color: rgba(255,255,255,0.9);
            color: #8b5a8c;
            padding: 8px 15px;
            border-radius: 20px;
            text-decoration: none;
            margin: 0 5px;
            font-weight: bold;
            transition: all 0.3s;
        }
        
        .user-menu a:hover {
            background-color: #8b5a8c;
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
        <a href="parfums.php" class="active">Parfums</a>
        <a href="contact.php">Contact</a>
        <a href="panier.php"><i class="fas fa-shopping-cart"></i> Panier</a>
    </nav>
</header>

<!-- Category Header -->
<div class="category-header">
    <h1><i class="fas fa-spray-can"></i> Nos Parfums d'Exception</h1>
    <p>Des fragrances uniques pour révéler votre personnalité</p>
</div>

<!-- Category Description -->
<div style="max-width: 800px; margin: 40px auto; padding: 30px; background: white; border-radius: 15px; box-shadow: 0 8px 16px rgba(0,0,0,0.08); text-align: center;">
    <h2 style="color: #8b5a8c; margin-bottom: 20px;">L'Art de la Parfumerie Naturelle</h2>
    <p style="color: #666; line-height: 1.8;">
        Nos parfums sont créés à partir d'essences naturelles et d'huiles essentielles pures, 
        composées par des maîtres parfumeurs pour vous offrir des fragrances authentiques et raffinées. 
        Chaque parfum raconte une histoire, évoque une émotion, révèle une facette de votre personnalité.
    </p>
    <p style="color: #666; line-height: 1.8;">
        Sans alcool éthylique, nos parfums respectent votre peau tout en offrant une tenue exceptionnelle 
        et un sillage délicat qui vous accompagne tout au long de la journée.
    </p>
</div>

<!-- Parfums Grid -->
<?php if (empty($parfums)): ?>
    <div style="text-align: center; padding: 60px 20px;">
        <i class="fas fa-spray-can" style="font-size: 4rem; color: #ccc; margin-bottom: 20px;"></i>
        <h3 style="color: #8b5a8c;">Aucun parfum disponible</h3>
        <p style="color: #666;">Nos parfums seront bientôt disponibles. Revenez nous voir !</p>
        <a href="products.php" class="btn-parfum" style="font-size: 1.2em; padding: 15px 30px;">
            <i class="fas fa-shopping-bag"></i> Voir tous nos produits
        </a>
    </div>
<?php else: ?>
    <section class="parfums-container">
        <?php foreach ($parfums as $parfum): ?>
        <div class="parfum">
            <?php
            // Determine image path with fallback for parfums
            $image_path = '';
            if ($parfum['image'] && file_exists('assets/images/' . $parfum['image'])) {
                $image_path = 'assets/images/' . htmlspecialchars($parfum['image']);
            } else {
                // Use OIP images as fallback for parfums
                $parfum_images = ['OIP (1).jpg', 'OIP (2).jpg', 'OIP (3).jpg', 'OIP (4).jpg', 'OIP (5).jpg', 'OIP (6).jpg'];
                $image_path = 'assets/images/' . $parfum_images[($parfum['id'] - 1) % count($parfum_images)];
            }
            ?>
            <img src="<?php echo $image_path; ?>"
                 alt="<?php echo htmlspecialchars($parfum['name']); ?>"
                 onerror="this.src='assets/images/OIP (1).jpg'" />
            
            <h3><?php echo htmlspecialchars($parfum['name']); ?></h3>
            <p style="color: #666; font-size: 0.95em; margin: 15px 0;">
                <?php echo htmlspecialchars(substr($parfum['description'], 0, 120)) . '...'; ?>
            </p>
            
            <?php if ($parfum['characteristics']): ?>
                <div class="parfum-notes">
                    <strong style="color: #8b5a8c;">Notes olfactives :</strong><br>
                    <?php echo htmlspecialchars(substr($parfum['characteristics'], 0, 80)) . '...'; ?>
                </div>
            <?php endif; ?>
            
            <div class="price"><?php echo formatPrice($parfum['price']); ?></div>
            
            <?php if ($parfum['stock_quantity'] > 0): ?>
                <small style="color: #28a745; font-weight: bold;">✓ En stock (<?php echo $parfum['stock_quantity']; ?>)</small>
            <?php else: ?>
                <small style="color: #dc3545; font-weight: bold;">✗ Rupture de stock</small>
            <?php endif; ?>
            
            <div style="margin-top: 20px;">
                <a href="product_detail.php?id=<?php echo $parfum['id']; ?>" class="btn-parfum">
                    <i class="fas fa-eye"></i> Découvrir
                </a>
                
                <?php if (isClient() && $parfum['stock_quantity'] > 0): ?>
                    <button class="btn-parfum" onclick="ajouterAuPanier('<?php echo htmlspecialchars($parfum['name']); ?>', <?php echo $parfum['price']; ?>)">
                        <i class="fas fa-shopping-cart"></i> Ajouter au panier
                    </button>
                <?php elseif (!isLoggedIn()): ?>
                    <a href="client/login.php" class="btn-parfum">
                        <i class="fas fa-sign-in-alt"></i> Se connecter
                    </a>
                <?php endif; ?>
            </div>
        </div>
        <?php endforeach; ?>
    </section>
    
    <div style="text-align: center; margin: 40px 0;">
        <a href="products.php" class="btn-parfum" style="font-size: 1.1em; padding: 12px 25px;">
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
        <p><i class="fas fa-phone"></i> +33 1 23 45 67 89</p>
    </div>
    <div>
        <h3>Nous écrire</h3>
        <p><a href="mailto:contact@labeautebio.fr" style="color: white;"><i class="fas fa-envelope"></i> contact@labeautebio.fr</a></p>
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
