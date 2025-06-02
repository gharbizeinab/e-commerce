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
$query = "SELECT o.*, u.full_name, u.email, u.phone, u.address as user_address
          FROM orders o 
          LEFT JOIN users u ON o.user_id = u.id 
          WHERE o.id = $order_id";
$result = $connection->query($query);
$order = $result->fetch();

if (!$order) {
    $_SESSION['error_message'] = 'Commande non trouvée.';
    header('Location: orders.php');
    exit();
}

// Get order items
$query_items = "SELECT oi.*, p.image 
                FROM order_items oi 
                LEFT JOIN products p ON oi.product_id = p.id
                WHERE oi.order_id = $order_id";
$result_items = $connection->query($query_items);
$order_items = $result_items->fetchAll();

include '../includes/admin_header.php';
?>

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
                    <!-- Informations de la commande -->
                    <!-- Articles commandés -->
                </div>

                <!-- Customer Information & Actions -->
                <div class="col-lg-4">
                    <!-- Informations client -->
                    <!-- Adresses -->
                    <!-- Actions -->
                </div>
            </div>
        </main>
    </div>
</div>

<?php include '../includes/admin_footer.php'; ?>