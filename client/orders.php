<?php
/**
 * Client Orders Page - La Beauté Bio
 * View client's order history
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

$page_title = 'Mes Commandes - La Beauté Bio';
$current_user = getCurrentUser();

// Get client's orders
$stmt = $pdo->prepare("SELECT o.*, COUNT(oi.id) as item_count
                      FROM orders o 
                      LEFT JOIN order_items oi ON o.id = oi.order_id
                      WHERE o.user_id = ?
                      GROUP BY o.id
                      ORDER BY o.created_at DESC");
$stmt->execute([$current_user['id']]);
$orders = $stmt->fetchAll();
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
        .orders-container {
            max-width: 1000px;
            margin: 0 auto;
            padding: 40px 20px;
        }
        
        .order-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 8px 16px rgba(0,0,0,0.08);
            padding: 25px;
            margin-bottom: 25px;
            transition: transform 0.3s;
        }
        
        .order-card:hover {
            transform: translateY(-2px);
        }
        
        .order-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #f8f9fa;
        }
        
        .order-number {
            font-size: 1.2em;
            font-weight: bold;
            color: #7c943f;
        }
        
        .order-status {
            padding: 8px 16px;
            border-radius: 20px;
            font-weight: bold;
            font-size: 0.9em;
        }
        
        .status-pending { background: #fff3cd; color: #856404; }
        .status-confirmed { background: #d1e7dd; color: #0f5132; }
        .status-declined { background: #f8d7da; color: #721c24; }
        .status-shipped { background: #cff4fc; color: #055160; }
        .status-delivered { background: #d1e7dd; color: #0f5132; }
        
        .order-details {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 20px;
            margin-bottom: 20px;
        }
        
        .detail-item {
            text-align: center;
        }
        
        .detail-label {
            font-size: 0.9em;
            color: #666;
            margin-bottom: 5px;
        }
        
        .detail-value {
            font-weight: bold;
            color: #333;
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
            padding: 10px 20px;
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
        
        @media (max-width: 768px) {
            .order-details {
                grid-template-columns: 1fr;
                gap: 15px;
            }
            
            .order-header {
                flex-direction: column;
                gap: 15px;
                text-align: center;
            }
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

<!-- Orders Content -->
<div class="orders-container">
    <h1 style="text-align: center; color: #7c943f; margin-bottom: 40px;">
        <i class="fas fa-shopping-bag"></i> Mes Commandes
    </h1>

    <?php if (empty($orders)): ?>
        <div class="order-card" style="text-align: center; padding: 60px 20px;">
            <i class="fas fa-shopping-bag" style="font-size: 4rem; color: #ccc; margin-bottom: 20px;"></i>
            <h3 style="color: #7c943f;">Aucune commande</h3>
            <p style="color: #666; margin-bottom: 30px;">Vous n'avez pas encore passé de commande.</p>
            <a href="../products.php" class="btn-order" style="font-size: 1.2em; padding: 15px 30px;">
                <i class="fas fa-shopping-cart"></i> Découvrir nos produits
            </a>
        </div>
    <?php else: ?>
        <?php foreach ($orders as $order): ?>
            <div class="order-card">
                <div class="order-header">
                    <div>
                        <div class="order-number">Commande #<?php echo htmlspecialchars($order['order_number']); ?></div>
                        <small style="color: #666;">
                            Passée le <?php echo date('d/m/Y à H:i', strtotime($order['created_at'])); ?>
                        </small>
                    </div>
                    <div>
                        <?php
                        $status_classes = [
                            'pending' => 'status-pending',
                            'confirmed' => 'status-confirmed',
                            'declined' => 'status-declined',
                            'shipped' => 'status-shipped',
                            'delivered' => 'status-delivered'
                        ];
                        $status_labels = [
                            'pending' => 'En attente',
                            'confirmed' => 'Confirmée',
                            'declined' => 'Refusée',
                            'shipped' => 'Expédiée',
                            'delivered' => 'Livrée'
                        ];
                        $class = $status_classes[$order['status']] ?? 'status-pending';
                        $label = $status_labels[$order['status']] ?? $order['status'];
                        ?>
                        <span class="order-status <?php echo $class; ?>"><?php echo $label; ?></span>
                    </div>
                </div>
                
                <div class="order-details">
                    <div class="detail-item">
                        <div class="detail-label">Articles</div>
                        <div class="detail-value">
                            <i class="fas fa-box"></i> <?php echo $order['item_count']; ?> article(s)
                        </div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Montant total</div>
                        <div class="detail-value">
                            <i class="fas fa-euro-sign"></i> <?php echo formatPrice($order['total_amount']); ?>
                        </div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Paiement</div>
                        <div class="detail-value">
                            <?php
                            $payment_icons = [
                                'card' => 'fas fa-credit-card',
                                'paypal' => 'fab fa-paypal',
                                'transfer' => 'fas fa-university'
                            ];
                            $payment_labels = [
                                'card' => 'Carte bancaire',
                                'paypal' => 'PayPal',
                                'transfer' => 'Virement'
                            ];
                            $icon = $payment_icons[$order['payment_method']] ?? 'fas fa-credit-card';
                            $label = $payment_labels[$order['payment_method']] ?? $order['payment_method'];
                            ?>
                            <i class="<?php echo $icon; ?>"></i> <?php echo $label; ?>
                        </div>
                    </div>
                </div>
                
                <?php if ($order['notes']): ?>
                    <div style="background: #f8f9fa; padding: 15px; border-radius: 10px; margin-bottom: 20px;">
                        <strong style="color: #7c943f;">Notes :</strong><br>
                        <?php echo nl2br(htmlspecialchars($order['notes'])); ?>
                    </div>
                <?php endif; ?>
                
                <div style="text-align: center;">
                    <a href="order_detail.php?id=<?php echo $order['id']; ?>" class="btn-order">
                        <i class="fas fa-eye"></i> Voir les détails
                    </a>
                    
                    <?php if ($order['status'] === 'confirmed' || $order['status'] === 'shipped'): ?>
                        <span class="btn-order" style="background: #28a745; cursor: default;">
                            <i class="fas fa-check"></i> Commande validée
                        </span>
                    <?php elseif ($order['status'] === 'declined'): ?>
                        <span class="btn-order" style="background: #dc3545; cursor: default;">
                            <i class="fas fa-times"></i> Commande refusée
                        </span>
                    <?php elseif ($order['status'] === 'delivered'): ?>
                        <span class="btn-order" style="background: #17a2b8; cursor: default;">
                            <i class="fas fa-check-double"></i> Livrée
                        </span>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
        
        <div style="text-align: center; margin-top: 40px;">
            <a href="../products.php" class="btn-order" style="font-size: 1.1em; padding: 12px 25px;">
                <i class="fas fa-shopping-cart"></i> Continuer mes achats
            </a>
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
        <p><i class="fas fa-phone"></i> +33 1 23 45 67 89</p>
    </div>
    <div>
        <h3>Nous écrire</h3>
        <p><a href="mailto:contact@labeautebio.fr" style="color: white;"><i class="fas fa-envelope"></i> contact@labeautebio.fr</a></p>
    </div>
</footer>

<div class="bottom-bar">
    <a href="../index.php" style="color: white; text-decoration: none;">Retour à l'accueil</a> |
    <a href="../products.php" style="color: white; text-decoration: none;">Continuer mes achats</a>
</div>

<!-- JavaScript -->
<script src="../assets/js/cart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
