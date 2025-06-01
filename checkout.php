<?php
/**
 * Checkout Page - La Beauté Bio
 * Process customer orders
 */

require_once 'config/database.php';
require_once 'config/session.php';
require_once 'includes/functions.php';

// Require client login
if (!isClient()) {
    $_SESSION['error_message'] = 'Veuillez vous connecter pour finaliser votre commande.';
    header('Location: client/login.php');
    exit();
}

$page_title = 'Finaliser la commande - La Beauté Bio';
$errors = [];
$success = isset($_GET['success']) && $_GET['success'] == '1';

$current_user = getCurrentUser();

// Handle order submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $errors[] = 'Token de sécurité invalide.';
    } else {
        // Get form data
        $shipping_address = sanitizeInput($_POST['shipping_address'] ?? '');
        $billing_address = sanitizeInput($_POST['billing_address'] ?? '');
        $payment_method = sanitizeInput($_POST['payment_method'] ?? '');
        $notes = sanitizeInput($_POST['notes'] ?? '');
        $cart_data = $_POST['cart_data'] ?? '';

        // Validation
        if (empty($shipping_address)) $errors[] = 'L\'adresse de livraison est requise.';
        if (empty($billing_address)) $errors[] = 'L\'adresse de facturation est requise.';
        if (empty($payment_method)) $errors[] = 'Veuillez choisir un mode de paiement.';
        if (empty($cart_data)) $errors[] = 'Votre panier est vide.';

        if (empty($errors)) {
            try {
                $cart_items = json_decode($cart_data, true);
                if (!$cart_items || empty($cart_items)) {
                    $errors[] = 'Données du panier invalides.';
                } else {
                    // Calculate total
                    $total_amount = 0;
                    foreach ($cart_items as $item) {
                        $total_amount += $item['quantite'] * $item['prix'];
                    }

                    // Generate order number
                    $order_number = 'ORD-' . date('Y') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);

                    // Create order
                    $stmt = $pdo->prepare("INSERT INTO orders (user_id, order_number, status, total_amount, shipping_address, billing_address, payment_method, notes) 
                                          VALUES (?, ?, 'pending', ?, ?, ?, ?, ?)");
                    
                    if ($stmt->execute([$current_user['id'], $order_number, $total_amount, $shipping_address, $billing_address, $payment_method, $notes])) {
                        $order_id = $pdo->lastInsertId();

                        // Add order items
                        $stmt_item = $pdo->prepare("INSERT INTO order_items (order_id, product_id, product_name, quantity, unit_price, total_price)
                                                   VALUES (?, ?, ?, ?, ?, ?)");

                        foreach ($cart_items as $item) {
                            $total_price = $item['quantite'] * $item['prix'];

                            // Get product_id from database by name if not provided
                            $product_id = $item['id'] ?? null;
                            if (!$product_id || $product_id == 0) {
                                $stmt_find = $pdo->prepare("SELECT id FROM products WHERE name = ? LIMIT 1");
                                $stmt_find->execute([$item['nom']]);
                                $product = $stmt_find->fetch();
                                $product_id = $product ? $product['id'] : null;
                            }

                            // Only add item if we have a valid product_id
                            if ($product_id) {
                                $stmt_item->execute([
                                    $order_id,
                                    $product_id,
                                    $item['nom'],
                                    $item['quantite'],
                                    $item['prix'],
                                    $total_price
                                ]);
                            }
                        }

                        $success = true;
                        $_SESSION['success_message'] = "Commande #{$order_number} créée avec succès ! En attente de confirmation.";
                        $_SESSION['order_number'] = $order_number;

                        // Redirect to prevent form resubmission
                        header('Location: checkout.php?success=1');
                        exit();
                    } else {
                        $errors[] = 'Erreur lors de la création de la commande.';
                    }
                }
            } catch (Exception $e) {
                $errors[] = 'Erreur lors du traitement de la commande: ' . $e->getMessage();
            }
        }
    }
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
        .checkout-container {
            max-width: 1000px;
            margin: 0 auto;
            padding: 40px 20px;
        }
        
        .checkout-section {
            background: white;
            border-radius: 15px;
            box-shadow: 0 8px 16px rgba(0,0,0,0.08);
            padding: 30px;
            margin-bottom: 30px;
        }
        
        .checkout-grid {
            display: grid;
            grid-template-columns: 1fr 400px;
            gap: 30px;
        }
        
        .form-section h3 {
            color: #7c943f;
            margin-bottom: 20px;
            border-bottom: 2px solid #7c943f;
            padding-bottom: 10px;
        }
        
        .form-control {
            border: 2px solid #e9ecef;
            border-radius: 8px;
            padding: 12px 15px;
            margin-bottom: 15px;
        }
        
        .form-control:focus {
            border-color: #7c943f;
            box-shadow: 0 0 0 0.2rem rgba(124, 148, 63, 0.25);
        }
        
        .payment-method {
            border: 2px solid #e9ecef;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 15px;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .payment-method:hover {
            border-color: #7c943f;
        }
        
        .payment-method.selected {
            border-color: #7c943f;
            background-color: #f8f9fa;
        }
        
        .order-summary {
            background: #f8f9fa;
            border-radius: 15px;
            padding: 25px;
            position: sticky;
            top: 20px;
        }
        
        .order-summary h3 {
            color: #7c943f;
            margin-bottom: 20px;
        }
        
        .summary-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            padding: 8px 0;
        }
        
        .summary-total {
            border-top: 2px solid #7c943f;
            padding-top: 15px;
            margin-top: 15px;
            font-size: 1.2em;
            font-weight: bold;
        }
        
        .btn-checkout {
            background-color: #7c943f;
            color: white;
            border: none;
            padding: 15px 30px;
            font-weight: bold;
            border-radius: 10px;
            cursor: pointer;
            transition: background 0.3s;
            width: 100%;
            font-size: 1.1em;
        }
        
        .btn-checkout:hover {
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
        
        @media (max-width: 768px) {
            .checkout-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>

<!-- User Menu -->
<div class="user-menu">
    <a href="client/profile.php"><i class="fas fa-user"></i> Mon Profil</a>
    <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Déconnexion</a>
</div>

<!-- Header -->
<header>
    <nav>
        <a href="index.php">Accueil</a>
        <a href="a-propos.php">À propos</a>
        <a href="products.php">Nos produits</a>
        <a href="contact.php">Contact</a>
        <a href="panier.php">Panier</a>
    </nav>
</header>

<!-- Checkout Content -->
<div class="checkout-container">
    <h1 style="text-align: center; color: #7c943f; margin-bottom: 40px;">
        <i class="fas fa-credit-card"></i> Finaliser votre commande
    </h1>

    <?php if ($success): ?>
        <div class="checkout-section" style="text-align: center;">
            <i class="fas fa-check-circle" style="font-size: 4rem; color: #28a745; margin-bottom: 20px;"></i>
            <h2 style="color: #28a745;">Commande créée avec succès !</h2>
            <p>Votre commande <strong>#<?php echo $_SESSION['order_number']; ?></strong> a été créée et est en attente de confirmation par notre équipe.</p>
            <p>Vous recevrez un email de confirmation une fois votre commande validée.</p>
            <div style="margin-top: 30px;">
                <a href="client/orders.php" class="btn-checkout" style="max-width: 300px; margin: 10px auto; display: block;">
                    <i class="fas fa-list"></i> Voir mes commandes
                </a>
                <a href="index.php" class="btn-checkout" style="max-width: 300px; margin: 10px auto; display: block; background: #6c757d;">
                    <i class="fas fa-home"></i> Retour à l'accueil
                </a>
            </div>
        </div>
        
        <script>
        // Clear cart after successful order
        localStorage.removeItem('panier');
        </script>
    <?php else: ?>
        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle"></i>
                <ul class="mb-0">
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo htmlspecialchars($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form method="POST" class="needs-validation" novalidate id="checkout-form">
            <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
            <input type="hidden" name="cart_data" id="cart_data">
            
            <div class="checkout-grid">
                <!-- Checkout Form -->
                <div>
                    <!-- Shipping Information -->
                    <div class="checkout-section">
                        <div class="form-section">
                            <h3><i class="fas fa-truck"></i> Adresse de livraison</h3>
                            
                            <textarea class="form-control" name="shipping_address" rows="4" 
                                      placeholder="Adresse complète de livraison *" required><?php echo htmlspecialchars($_POST['shipping_address'] ?? $current_user['address'] ?? ''); ?></textarea>
                        </div>
                    </div>

                    <!-- Billing Information -->
                    <div class="checkout-section">
                        <div class="form-section">
                            <h3><i class="fas fa-file-invoice"></i> Adresse de facturation</h3>
                            
                            <div class="form-check mb-3">
                                <input type="checkbox" class="form-check-input" id="same_address" checked>
                                <label class="form-check-label" for="same_address">
                                    Identique à l'adresse de livraison
                                </label>
                            </div>
                            
                            <textarea class="form-control" name="billing_address" id="billing_address" rows="4" 
                                      placeholder="Adresse de facturation *" required><?php echo htmlspecialchars($_POST['billing_address'] ?? $current_user['address'] ?? ''); ?></textarea>
                        </div>
                    </div>

                    <!-- Payment Method -->
                    <div class="checkout-section">
                        <div class="form-section">
                            <h3><i class="fas fa-credit-card"></i> Mode de paiement</h3>
                            
                            <div class="payment-method" onclick="selectPayment('card')">
                                <input type="radio" name="payment_method" value="card" id="payment_card" style="margin-right: 10px;">
                                <label for="payment_card" style="cursor: pointer;">
                                    <i class="fas fa-credit-card"></i> Carte bancaire
                                    <small style="display: block; color: #666;">Visa, Mastercard, American Express</small>
                                </label>
                            </div>
                            
                            <div class="payment-method" onclick="selectPayment('paypal')">
                                <input type="radio" name="payment_method" value="paypal" id="payment_paypal" style="margin-right: 10px;">
                                <label for="payment_paypal" style="cursor: pointer;">
                                    <i class="fab fa-paypal"></i> PayPal
                                    <small style="display: block; color: #666;">Paiement sécurisé via PayPal</small>
                                </label>
                            </div>
                            
                            <div class="payment-method" onclick="selectPayment('transfer')">
                                <input type="radio" name="payment_method" value="transfer" id="payment_transfer" style="margin-right: 10px;">
                                <label for="payment_transfer" style="cursor: pointer;">
                                    <i class="fas fa-university"></i> Virement bancaire
                                    <small style="display: block; color: #666;">Paiement par virement</small>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Notes -->
                    <div class="checkout-section">
                        <div class="form-section">
                            <h3><i class="fas fa-sticky-note"></i> Notes (optionnel)</h3>
                            <textarea class="form-control" name="notes" rows="3" 
                                      placeholder="Instructions spéciales pour la livraison..."><?php echo htmlspecialchars($_POST['notes'] ?? ''); ?></textarea>
                        </div>
                    </div>
                </div>

                <!-- Order Summary -->
                <div class="order-summary">
                    <h3><i class="fas fa-shopping-cart"></i> Récapitulatif</h3>
                    
                    <div id="order-items">
                        <!-- Items will be loaded by JavaScript -->
                    </div>
                    
                    <div class="summary-item">
                        <span>Sous-total:</span>
                        <span id="subtotal">0,00 €</span>
                    </div>
                    
                    <div class="summary-item">
                        <span>Livraison:</span>
                        <span>5,90 €</span>
                    </div>
                    
                    <div class="summary-item">
                        <span>TVA (20%):</span>
                        <span id="tax">0,00 €</span>
                    </div>
                    
                    <div class="summary-item summary-total">
                        <span>Total:</span>
                        <span id="total">5,90 €</span>
                    </div>
                    
                    <button type="submit" class="btn-checkout" style="margin-top: 20px;">
                        <i class="fas fa-lock"></i> Confirmer la commande
                    </button>
                    
                    <p style="text-align: center; margin-top: 15px; font-size: 0.9em; color: #666;">
                        <i class="fas fa-shield-alt"></i> Commande sécurisée
                    </p>
                </div>
            </div>
        </form>
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

<!-- JavaScript -->
<script src="assets/js/cart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Payment method selection
function selectPayment(method) {
    document.querySelectorAll('.payment-method').forEach(el => el.classList.remove('selected'));
    document.getElementById('payment_' + method).checked = true;
    document.getElementById('payment_' + method).closest('.payment-method').classList.add('selected');
}

// Same address checkbox
document.getElementById('same_address').addEventListener('change', function() {
    const shippingAddress = document.querySelector('textarea[name="shipping_address"]').value;
    const billingAddress = document.getElementById('billing_address');
    
    if (this.checked) {
        billingAddress.value = shippingAddress;
        billingAddress.readOnly = true;
        billingAddress.style.backgroundColor = '#f8f9fa';
    } else {
        billingAddress.readOnly = false;
        billingAddress.style.backgroundColor = 'white';
    }
});

// Update billing address when shipping address changes
document.querySelector('textarea[name="shipping_address"]').addEventListener('input', function() {
    if (document.getElementById('same_address').checked) {
        document.getElementById('billing_address').value = this.value;
    }
});

// Load cart summary and prepare form data
function loadOrderSummary() {
    let panier = JSON.parse(localStorage.getItem('panier')) || [];
    let orderItems = document.getElementById('order-items');
    let subtotal = 0;
    
    if (panier.length === 0) {
        orderItems.innerHTML = '<p style="color: #666;">Votre panier est vide</p>';
        document.querySelector('button[type="submit"]').disabled = true;
        return;
    }
    
    let itemsHTML = '';
    panier.forEach(item => {
        const itemTotal = item.quantite * item.prix;
        subtotal += itemTotal;
        itemsHTML += `
            <div class="summary-item">
                <span>${item.nom} (x${item.quantite})</span>
                <span>${itemTotal.toFixed(2)} €</span>
            </div>
        `;
    });
    
    orderItems.innerHTML = itemsHTML;
    
    const shipping = 5.90;
    const tax = subtotal * 0.20;
    const total = subtotal + shipping + tax;
    
    document.getElementById('subtotal').textContent = subtotal.toFixed(2) + ' €';
    document.getElementById('tax').textContent = tax.toFixed(2) + ' €';
    document.getElementById('total').textContent = total.toFixed(2) + ' €';
    
    // Set cart data for form submission
    document.getElementById('cart_data').value = JSON.stringify(panier);
}

// Load on page load
document.addEventListener('DOMContentLoaded', function() {
    loadOrderSummary();
    
    // Trigger same address functionality
    document.getElementById('same_address').dispatchEvent(new Event('change'));
});
</script>

</body>
</html>
