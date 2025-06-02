<?php
/**
 * Client Order Detail Page - La Beauté Bio
 * View detailed information about a specific order
 */

require_once '../config/database.php';
require_once '../config/session.php';
require_once '../includes/functions.php';

// Require client login
if (!isClient()) {
    $_SESSION['error_message'] = 'Veuillez vous connecter pour voir vos commandes.';
    header('Location: login.php');
    exit();
}

$page_title = 'Détail de la Commande - La Beauté Bio';
$current_user = getCurrentUser();

// Get order ID
$order_id = intval($_GET['id'] ?? 0);
if (!$order_id) {
    $_SESSION['error_message'] = 'Commande non trouvée.';
    header('Location: orders.php');
    exit();
}

// Get order data (only for current user)
$stmt = $connection->query("SELECT * FROM orders WHERE id = ? AND user_id = ?");
$stmt->execute([$order_id, $current_user['id']]);
$order = mysqli_fetch_assoc($result);

if (!$order) {
    $_SESSION['error_message'] = 'Commande non trouvée.';
    header('Location: orders.php');
    exit();
}

// Get order items
$stmt = $connection->query("SELECT oi.*, p.image 
                      FROM order_items oi 
                      LEFT JOIN products p ON oi.product_id = p.id
                      WHERE oi.order_id = '$order_id'");
$order_items = array();
while ($row = mysqli_fetch_assoc($result)) {
    $order_items[] = $row;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?></title>
    
    <!-- Frontend CSS -->
    <link rel="stylesheet" href="../assets/css/frontend.css">
    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <style>
        .order-detail-container {
            max-width: 1000px;
            margin: 0 auto;
            padding: 40px 20px;
        }
        
        .order-detail-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 8px 16px rgba(0,0,0,0.08);
            padding: 30px;
            margin-bottom: 30px;
        }
        
        .order-header {
            text-align: center;
            margin-bottom: 40px;
            padding-bottom: 20px;
            border-bottom: 2px solid #f8f9fa;
        }
        
        .order-number {
            font-size: 2rem;
            font-weight: bold;
            color: #7c943f;
            margin-bottom: 10px;
        }
        
        .order-status {
            padding: 10px 20px;
            border-radius: 25px;
            font-weight: bold;
            font-size: 1.1em;
            display: inline-block;
        }
        
        .status-pending { background: #fff3cd; color: #856404; }
        .status-confirmed { background: #d1e7dd; color: #0f5132; }
        .status-declined { background: #f8d7da; color: #721c24; }
        .status-shipped { background: #cff4fc; color: #055160; }
        .status-delivered { background: #d1e7dd; color: #0f5132; }
        
        .order-info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 30px;
            margin-bottom: 40px;
        }
        
        .info-section {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 15px;
        }
        
        .info-section h5 {
            color: #7c943f;
            margin-bottom: 15px;
            border-bottom: 2px solid #7c943f;
            padding-bottom: 8px;
        }
        
        .order-items {
            margin-bottom: 30px;
        }
        
        .item-card {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 20px;
        }
        
        .item-image {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 10px;
            flex-shrink: 0;
        }
        
        .item-details {
            flex-grow: 1;
        }
        
        .item-name {
            font-weight: bold;
            color: #333;
            margin-bottom: 5px;
        }
        
        .item-price {
            color: #7c943f;
            font-weight: bold;
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
        
        .btn-order {
            background: linear-gradient(135deg, #7c943f, #5c7045);
            color: white;
            border: none;
            padding: 12px 25px;
            font-weight: bold;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-block;
            margin: 5px;
        }
        
        .btn-order:hover {
            background: linear-gradient(135deg, #5c7045, #4a5a37);
            color: white;
            transform: translateY(-1px);
        }
    </style>
</head>
<body>

<!-- User Menu -->
<div class="user-menu">
    <a href="profile.php"><i class="fas fa-user"></i> Mon Profil</a>
    <a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Déconnexion</a>
</div>

<!-- Header -->
<header>
    <nav>
        <a href="../index.php">Accueil</a>
        <a href="../a-propos.php">À propos</a>
        <a href="../products.php">Nos produits</a>
        <a href="../contact.php">Contact</a>
        <a href="../panier.php">Panier</a>
    </nav>
</header>

<!-- Order Detail Content -->
<div class="order-detail-container">
    <div class="order-detail-card">
        <div class="order-header">
            <div class="order-number">Commande #<?php echo htmlspecialchars($order['order_number']); ?></div>
            <p style="color: #666; margin-bottom: 20px;">
                Passée le <?php echo date('d/m/Y à H:i', strtotime($order['created_at'])); ?>
            </p>
            <?php
            $status_classes = [
                'pending' => 'status-pending',
                'confirmed' => 'status-confirmed',
                'declined' => 'status-declined',
                'shipped' => 'status-shipped',
                'delivered' => 'status-delivered'
            ];
            $status_labels = [
                'pending' => 'En attente de confirmation',
                'confirmed' => 'Confirmée',
                'declined' => 'Refusée',
                'shipped' => 'Expédiée',
                'delivered' => 'Livrée'
            ];
            $class = $status_classes[$order['status']] ?? 'status-pending';
            $label = $status_labels[$order['status']] ?? $order['status'];
            ?>
            <div class="order-status <?php echo $class; ?>"><?php echo $label; ?></div>
        </div>
        
        <!-- Order Information -->
        <div class="order-info-grid">
            <div class="info-section">
                <h5><i class="fas fa-info-circle"></i> Informations générales</h5>
                <p><strong>Montant total :</strong> <?php echo formatPrice($order['total_amount']); ?></p>
                <p><strong>Mode de paiement :</strong> 
                    <?php
                    $payment_labels = [
                        'card' => 'Carte bancaire',
                        'paypal' => 'PayPal',
                        'transfer' => 'Virement bancaire'
                    ];
                    echo $payment_labels[$order['payment_method']] ?? $order['payment_method'];
                    ?>
                </p>
                <p><strong>Dernière mise à jour :</strong> <?php echo date('d/m/Y à H:i', strtotime($order['updated_at'])); ?></p>
            </div>
            
            <div class="info-section">
                <h5><i class="fas fa-truck"></i> Livraison</h5>
                <p><?php echo nl2br(htmlspecialchars($order['shipping_address'])); ?></p>
            </div>
            
            <div class="info-section">
                <h5><i class="fas fa-file-invoice"></i> Facturation</h5>
                <p><?php echo nl2br(htmlspecialchars($order['billing_address'])); ?></p>
            </div>
        </div>
        
        <?php if ($order['notes']): ?>
            <div class="info-section" style="margin-bottom: 30px;">
                <h5><i class="fas fa-sticky-note"></i> Vos notes</h5>
                <p><?php echo nl2br(htmlspecialchars($order['notes'])); ?></p>
            </div>
        <?php endif; ?>
        
        <!-- Order Items -->
        <div class="order-items">
            <h3 style="color: #7c943f; margin-bottom: 25px;">
                <i class="fas fa-box"></i> Articles commandés
            </h3>
            
            <?php foreach ($order_items as $item): ?>
                <div class="item-card">
                    <?php if ($item['image'] && file_exists('../assets/images/' . $item['image'])): ?>
                        <img src="../assets/images/<?php echo htmlspecialchars($item['image']); ?>" 
                             alt="<?php echo htmlspecialchars($item['product_name']); ?>"
                             class="item-image">
                    <?php else: ?>
                        <div class="item-image" style="background: #e9ecef; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-image" style="color: #6c757d;"></i>
                        </div>
                    <?php endif; ?>
                    
                    <div class="item-details">
                        <div class="item-name"><?php echo htmlspecialchars($item['product_name']); ?></div>
                        <div style="color: #666;">
                            Quantité : <?php echo $item['quantity']; ?> × <?php echo formatPrice($item['unit_price']); ?>
                        </div>
                        <div class="item-price"><?php echo formatPrice($item['total_price']); ?></div>
                    </div>
                </div>
            <?php endforeach; ?>
            
            <div style="text-align: right; margin-top: 20px; padding-top: 20px; border-top: 2px solid #7c943f;">
                <h4 style="color: #7c943f;">
                    Total : <?php echo formatPrice($order['total_amount']); ?>
                </h4>
            </div>
        </div>
        
        <!-- Actions -->
        <div style="text-align: center; margin-top: 40px;">
            <a href="orders.php" class="btn-order">
                <i class="fas fa-arrow-left"></i> Retour à mes commandes
            </a>
            <a href="../products.php" class="btn-order">
                <i class="fas fa-shopping-cart"></i> Continuer mes achats
            </a>
        </div>
    </div>
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
        <p><i class="fas fa-phone"></i> +33 1 23 45 67 89</p>
    </div>
    <div>
        <h3>Nous écrire</h3>
        <p><a href="mailto:contact@labeautebio.fr" style="color: white;"><i class="fas fa-envelope"></i> contact@labeautebio.fr</a></p>
    </div>
</footer>

<div class="bottom-bar">
    <a href="../index.php" style="color: white; text-decoration: none;">Retour à l'accueil</a> |
    <a href="orders.php" style="color: white; text-decoration: none;">Mes commandes</a>
</div>

<!-- JavaScript -->
<script src="../assets/js/cart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
