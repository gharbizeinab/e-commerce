<?php
/**
 * Admin Orders Management
 * View and manage customer orders
 */

require_once '../config/database.php';
require_once '../config/session.php';
require_once '../includes/functions.php';

// Require admin access
requireAdmin();

$page_title = 'Gestion des Commandes';

// Handle order status updates
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $_SESSION['error_message'] = 'Token de sécurité invalide.';
    } else {
        $order_id = intval($_POST['order_id'] ?? 0);
        $action = $_POST['action'];
        
        if ($order_id > 0) {
            try {
                switch ($action) {
                    case 'confirm':
                        $result = executeQuery("UPDATE orders SET status = 'confirmed' WHERE id = $order_id");
                        if ($result) {
                            $_SESSION['success_message'] = 'Commande confirmée avec succès.';
                        }
                        break;

                    case 'decline':
                        $result = executeQuery("UPDATE orders SET status = 'declined' WHERE id = $order_id");
                        if ($result) {
                            $_SESSION['success_message'] = 'Commande refusée.';
                        }
                        break;

                    case 'ship':
                        $result = executeQuery("UPDATE orders SET status = 'shipped' WHERE id = $order_id");
                        if ($result) {
                            $_SESSION['success_message'] = 'Commande marquée comme expédiée.';
                        }
                        break;

                    case 'deliver':
                        $result = executeQuery("UPDATE orders SET status = 'delivered' WHERE id = $order_id");
                        if ($result) {
                            $_SESSION['success_message'] = 'Commande marquée comme livrée.';
                        }
                        break;
                }
            } catch (Exception $e) {
                $_SESSION['error_message'] = 'Erreur lors de la mise à jour: ' . $e->getMessage();
            }
        }
        
        header('Location: orders.php');
        exit();
    }
}

// Get filter parameters
$status_filter = $_GET['status'] ?? '';
$search = $_GET['search'] ?? '';

// Build query
$where_conditions = [];

if ($status_filter) {
    $where_conditions[] = "o.status = '$status_filter'";
}

if ($search) {
    $search_param = "%{$search}%";
    $where_conditions[] = "(o.order_number LIKE '$search_param' OR u.full_name LIKE '$search_param' OR u.email LIKE '$search_param')";
}

$where_clause = !empty($where_conditions) ? 'WHERE ' . implode(' AND ', $where_conditions) : '';

// Get orders with user information
$sql = "SELECT o.*, u.full_name, u.email, u.phone,
               COUNT(oi.id) as item_count
        FROM orders o
        LEFT JOIN users u ON o.user_id = u.id
        LEFT JOIN order_items oi ON o.id = oi.order_id
        {$where_clause}
        GROUP BY o.id
        ORDER BY o.created_at DESC";

$result = executeQuery($sql);
$orders = array();
while ($row = mysqli_fetch_assoc($result)) {
    $orders[] = $row;
}

// Get order statistics
$stats_sql = "SELECT 
                COUNT(*) as total_orders,
                COUNT(CASE WHEN status = 'pending' THEN 1 END) as pending_orders,
                COUNT(CASE WHEN status = 'confirmed' THEN 1 END) as confirmed_orders,
                COUNT(CASE WHEN status = 'declined' THEN 1 END) as declined_orders,
                COUNT(CASE WHEN status = 'shipped' THEN 1 END) as shipped_orders,
                COUNT(CASE WHEN status = 'delivered' THEN 1 END) as delivered_orders,
                SUM(CASE WHEN status IN ('confirmed', 'shipped', 'delivered') THEN total_amount ELSE 0 END) as total_revenue
              FROM orders";
$result = executeQuery($stats_sql);
$stats = mysqli_fetch_assoc($result);
?>

<?php include '../includes/admin_header.php'; ?>

<div class="container-fluid">
    <div class="row">
        <?php include '../includes/admin_sidebar.php'; ?>
        
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2"><i class="fas fa-shopping-cart"></i> Gestion des Commandes</h1>
            </div>

            <!-- Statistics Cards -->
            <div class="row mb-4">
                <div class="col-md-2">
                    <div class="card text-center">
                        <div class="card-body">
                            <h5 class="card-title text-primary"><?php echo $stats['total_orders']; ?></h5>
                            <p class="card-text small">Total</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="card text-center">
                        <div class="card-body">
                            <h5 class="card-title text-warning"><?php echo $stats['pending_orders']; ?></h5>
                            <p class="card-text small">En attente</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="card text-center">
                        <div class="card-body">
                            <h5 class="card-title text-success"><?php echo $stats['confirmed_orders']; ?></h5>
                            <p class="card-text small">Confirmées</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="card text-center">
                        <div class="card-body">
                            <h5 class="card-title text-danger"><?php echo $stats['declined_orders']; ?></h5>
                            <p class="card-text small">Refusées</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="card text-center">
                        <div class="card-body">
                            <h5 class="card-title text-info"><?php echo $stats['shipped_orders']; ?></h5>
                            <p class="card-text small">Expédiées</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="card text-center">
                        <div class="card-body">
                            <h5 class="card-title text-success"><?php echo formatPrice($stats['total_revenue']); ?></h5>
                            <p class="card-text small">Chiffre d'affaires</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filters -->
            <div class="card mb-4">
                <div class="card-body">
                    <form method="GET" class="row g-3">
                        <div class="col-md-4">
                            <label for="status" class="form-label">Statut</label>
                            <select class="form-select" id="status" name="status">
                                <option value="">Tous les statuts</option>
                                <option value="pending" <?php echo $status_filter === 'pending' ? 'selected' : ''; ?>>En attente</option>
                                <option value="confirmed" <?php echo $status_filter === 'confirmed' ? 'selected' : ''; ?>>Confirmée</option>
                                <option value="declined" <?php echo $status_filter === 'declined' ? 'selected' : ''; ?>>Refusée</option>
                                <option value="shipped" <?php echo $status_filter === 'shipped' ? 'selected' : ''; ?>>Expédiée</option>
                                <option value="delivered" <?php echo $status_filter === 'delivered' ? 'selected' : ''; ?>>Livrée</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="search" class="form-label">Recherche</label>
                            <input type="text" class="form-control" id="search" name="search" 
                                   value="<?php echo htmlspecialchars($search); ?>" 
                                   placeholder="Numéro de commande, nom client, email...">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">&nbsp;</label>
                            <button type="submit" class="btn btn-primary d-block w-100">
                                <i class="fas fa-search"></i> Filtrer
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Orders Table -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Liste des Commandes</h5>
                </div>
                <div class="card-body">
                    <?php if (empty($orders)): ?>
                        <div class="text-center py-4">
                            <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
                            <h5>Aucune commande trouvée</h5>
                            <p class="text-muted">Aucune commande ne correspond à vos critères de recherche.</p>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Numéro</th>
                                        <th>Client</th>
                                        <th>Articles</th>
                                        <th>Montant</th>
                                        <th>Statut</th>
                                        <th>Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($orders as $order): ?>
                                        <tr>
                                            <td>
                                                <strong><?php echo htmlspecialchars($order['order_number']); ?></strong>
                                            </td>
                                            <td>
                                                <div>
                                                    <strong><?php echo htmlspecialchars($order['full_name']); ?></strong><br>
                                                    <small class="text-muted"><?php echo htmlspecialchars($order['email']); ?></small>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge bg-secondary"><?php echo $order['item_count']; ?> article(s)</span>
                                            </td>
                                            <td>
                                                <strong><?php echo formatPrice($order['total_amount']); ?></strong>
                                            </td>
                                            <td>
                                                <?php
                                                $status_classes = [
                                                    'pending' => 'warning',
                                                    'confirmed' => 'success',
                                                    'declined' => 'danger',
                                                    'shipped' => 'info',
                                                    'delivered' => 'success',
                                                    'cancelled' => 'secondary'
                                                ];
                                                $status_labels = [
                                                    'pending' => 'En attente',
                                                    'confirmed' => 'Confirmée',
                                                    'declined' => 'Refusée',
                                                    'shipped' => 'Expédiée',
                                                    'delivered' => 'Livrée',
                                                    'cancelled' => 'Annulée'
                                                ];
                                                $class = $status_classes[$order['status']] ?? 'secondary';
                                                $label = $status_labels[$order['status']] ?? $order['status'];
                                                ?>
                                                <span class="badge bg-<?php echo $class; ?>"><?php echo $label; ?></span>
                                            </td>
                                            <td>
                                                <small><?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?></small>
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm" role="group">
                                                    <a href="order_detail.php?id=<?php echo $order['id']; ?>" 
                                                       class="btn btn-outline-primary" title="Voir détails">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    
                                                    <?php if ($order['status'] === 'pending'): ?>
                                                        <form method="POST" style="display: inline;" onsubmit="return confirm('Confirmer cette commande ?')">
                                                            <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                                                            <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                                            <input type="hidden" name="action" value="confirm">
                                                            <button type="submit" class="btn btn-outline-success" title="Confirmer">
                                                                <i class="fas fa-check"></i>
                                                            </button>
                                                        </form>
                                                        <form method="POST" style="display: inline;" onsubmit="return confirm('Refuser cette commande ?')">
                                                            <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                                                            <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                                            <input type="hidden" name="action" value="decline">
                                                            <button type="submit" class="btn btn-outline-danger" title="Refuser">
                                                                <i class="fas fa-times"></i>
                                                            </button>
                                                        </form>
                                                    <?php elseif ($order['status'] === 'confirmed'): ?>
                                                        <form method="POST" style="display: inline;" onsubmit="return confirm('Marquer cette commande comme expédiée ?')">
                                                            <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                                                            <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                                            <input type="hidden" name="action" value="ship">
                                                            <button type="submit" class="btn btn-outline-info" title="Marquer comme expédiée">
                                                                <i class="fas fa-shipping-fast"></i>
                                                            </button>
                                                        </form>
                                                    <?php elseif ($order['status'] === 'shipped'): ?>
                                                        <form method="POST" style="display: inline;" onsubmit="return confirm('Marquer cette commande comme livrée ?')">
                                                            <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                                                            <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                                            <input type="hidden" name="action" value="deliver">
                                                            <button type="submit" class="btn btn-outline-success" title="Marquer comme livrée">
                                                                <i class="fas fa-check-double"></i>
                                                            </button>
                                                        </form>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>
</div>

<?php include '../includes/admin_footer.php'; ?>
