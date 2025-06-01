<?php
/**
 * Admin Order Detail Page
 * View detailed information about a specific order
 */

require_once '../config/database.php';
require_once '../config/session.php';
require_once '../includes/functions.php';

// Require admin access
requireAdmin();

$page_title = 'Détail de la Commande';

// Get order ID
$order_id = intval($_GET['id'] ?? 0);
if (!$order_id) {
    $_SESSION['error_message'] = 'Commande non trouvée.';
    header('Location: orders.php');
    exit();
}

// Get order data with user information
$stmt = $pdo->prepare("SELECT o.*, u.full_name, u.email, u.phone, u.address as user_address
                      FROM orders o 
                      LEFT JOIN users u ON o.user_id = u.id 
                      WHERE o.id = ?");
$stmt->execute([$order_id]);
$order = $stmt->fetch();

if (!$order) {
    $_SESSION['error_message'] = 'Commande non trouvée.';
    header('Location: orders.php');
    exit();
}

// Get order items
$stmt = $pdo->prepare("SELECT oi.*, p.image 
                      FROM order_items oi 
                      LEFT JOIN products p ON oi.product_id = p.id
                      WHERE oi.order_id = ?");
$stmt->execute([$order_id]);
$order_items = $stmt->fetchAll();
?>

<?php include '../includes/admin_header.php'; ?>

<div class="container-fluid">
    <div class="row">
        <?php include '../includes/admin_sidebar.php'; ?>
        
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">
                    <i class="fas fa-shopping-cart"></i> Commande #<?php echo htmlspecialchars($order['order_number']); ?>
                </h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <a href="orders.php" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> Retour aux commandes
                    </a>
                </div>
            </div>

            <div class="row">
                <!-- Order Information -->
                <div class="col-lg-8">
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="fas fa-info-circle"></i> Informations de la commande</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>Numéro :</strong> <?php echo htmlspecialchars($order['order_number']); ?></p>
                                    <p><strong>Date :</strong> <?php echo date('d/m/Y à H:i', strtotime($order['created_at'])); ?></p>
                                    <p><strong>Statut :</strong> 
                                        <?php
                                        $status_classes = [
                                            'pending' => 'warning',
                                            'confirmed' => 'success',
                                            'declined' => 'danger',
                                            'shipped' => 'info',
                                            'delivered' => 'success'
                                        ];
                                        $status_labels = [
                                            'pending' => 'En attente',
                                            'confirmed' => 'Confirmée',
                                            'declined' => 'Refusée',
                                            'shipped' => 'Expédiée',
                                            'delivered' => 'Livrée'
                                        ];
                                        $class = $status_classes[$order['status']] ?? 'secondary';
                                        $label = $status_labels[$order['status']] ?? $order['status'];
                                        ?>
                                        <span class="badge bg-<?php echo $class; ?>"><?php echo $label; ?></span>
                                    </p>
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
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Montant total :</strong> <span class="h5 text-success"><?php echo formatPrice($order['total_amount']); ?></span></p>
                                    <p><strong>Dernière mise à jour :</strong> <?php echo date('d/m/Y à H:i', strtotime($order['updated_at'])); ?></p>
                                </div>
                            </div>
                            
                            <?php if ($order['notes']): ?>
                                <hr>
                                <h6>Notes du client :</h6>
                                <div class="alert alert-info">
                                    <?php echo nl2br(htmlspecialchars($order['notes'])); ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Order Items -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="fas fa-box"></i> Articles commandés</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Produit</th>
                                            <th>Quantité</th>
                                            <th>Prix unitaire</th>
                                            <th>Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($order_items as $item): ?>
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <?php if ($item['image']): ?>
                                                            <img src="../assets/images/<?php echo htmlspecialchars($item['image']); ?>" 
                                                                 alt="<?php echo htmlspecialchars($item['product_name']); ?>"
                                                                 style="width: 50px; height: 50px; object-fit: cover; border-radius: 5px; margin-right: 10px;">
                                                        <?php endif; ?>
                                                        <strong><?php echo htmlspecialchars($item['product_name']); ?></strong>
                                                    </div>
                                                </td>
                                                <td><?php echo $item['quantity']; ?></td>
                                                <td><?php echo formatPrice($item['unit_price']); ?></td>
                                                <td><strong><?php echo formatPrice($item['total_price']); ?></strong></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th colspan="3">Total de la commande</th>
                                            <th><?php echo formatPrice($order['total_amount']); ?></th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Customer Information & Actions -->
                <div class="col-lg-4">
                    <!-- Customer Info -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="fas fa-user"></i> Informations client</h5>
                        </div>
                        <div class="card-body">
                            <p><strong>Nom :</strong> <?php echo htmlspecialchars($order['full_name']); ?></p>
                            <p><strong>Email :</strong> <?php echo htmlspecialchars($order['email']); ?></p>
                            <?php if ($order['phone']): ?>
                                <p><strong>Téléphone :</strong> <?php echo htmlspecialchars($order['phone']); ?></p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Addresses -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="fas fa-map-marker-alt"></i> Adresses</h5>
                        </div>
                        <div class="card-body">
                            <h6>Livraison :</h6>
                            <p class="small"><?php echo nl2br(htmlspecialchars($order['shipping_address'])); ?></p>
                            
                            <h6>Facturation :</h6>
                            <p class="small"><?php echo nl2br(htmlspecialchars($order['billing_address'])); ?></p>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="fas fa-cogs"></i> Actions</h5>
                        </div>
                        <div class="card-body">
                            <?php if ($order['status'] === 'pending'): ?>
                                <form method="POST" action="orders.php" class="mb-2">
                                    <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                                    <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                    <input type="hidden" name="action" value="confirm">
                                    <button type="submit" class="btn btn-success w-100 mb-2" 
                                            onclick="return confirm('Confirmer cette commande ?')">
                                        <i class="fas fa-check"></i> Confirmer la commande
                                    </button>
                                </form>
                                <form method="POST" action="orders.php">
                                    <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                                    <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                    <input type="hidden" name="action" value="decline">
                                    <button type="submit" class="btn btn-danger w-100" 
                                            onclick="return confirm('Refuser cette commande ?')">
                                        <i class="fas fa-times"></i> Refuser la commande
                                    </button>
                                </form>
                            <?php elseif ($order['status'] === 'confirmed'): ?>
                                <form method="POST" action="orders.php">
                                    <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                                    <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                    <input type="hidden" name="action" value="ship">
                                    <button type="submit" class="btn btn-info w-100" 
                                            onclick="return confirm('Marquer cette commande comme expédiée ?')">
                                        <i class="fas fa-shipping-fast"></i> Marquer comme expédiée
                                    </button>
                                </form>
                            <?php elseif ($order['status'] === 'shipped'): ?>
                                <form method="POST" action="orders.php">
                                    <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                                    <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                    <input type="hidden" name="action" value="deliver">
                                    <button type="submit" class="btn btn-success w-100" 
                                            onclick="return confirm('Marquer cette commande comme livrée ?')">
                                        <i class="fas fa-check-double"></i> Marquer comme livrée
                                    </button>
                                </form>
                            <?php else: ?>
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle"></i> 
                                    <?php
                                    if ($order['status'] === 'delivered') {
                                        echo 'Commande livrée';
                                    } elseif ($order['status'] === 'declined') {
                                        echo 'Commande refusée';
                                    } else {
                                        echo 'Aucune action disponible';
                                    }
                                    ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<?php include '../includes/admin_footer.php'; ?>
