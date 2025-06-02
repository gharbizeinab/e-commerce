<?php
/**
 * Shopping Cart Page
 * Display and manage shopping cart items
 */

require_once 'config/database.php';
require_once 'config/session.php';
require_once 'includes/functions.php';

$page_title = 'Panier - La Beauté Bio';
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
        /* User Menu */
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
            display: inline-block;
        }

        .user-menu a:hover {
            background-color: #7c943f;
            color: white;
        }

        .cart-container {
            max-width: 1000px;
            margin: 0 auto;
            padding: 40px 20px;
        }

        .cart-table {
            background: white;
            border-radius: 15px;
            box-shadow: 0 8px 16px rgba(0,0,0,0.08);
            overflow: hidden;
            margin-bottom: 30px;
        }

        .cart-table table {
            width: 100%;
            margin: 0;
        }

        .cart-table th {
            background-color: #7c943f;
            color: white;
            padding: 15px;
            font-weight: bold;
        }

        .cart-table td {
            padding: 15px;
            border-bottom: 1px solid #eee;
            vertical-align: middle;
        }

        .cart-total {
            background: white;
            border-radius: 15px;
            box-shadow: 0 8px 16px rgba(0,0,0,0.08);
            padding: 30px;
            text-align: center;
        }

        .btn-cart {
            background-color: #7c943f;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            margin: 5px;
            transition: background 0.3s;
            font-size: 14px;
        }

        .btn-cart:hover {
            background-color: #5d722e;
            color: white;
        }

        .btn-cart.btn-sm {
            padding: 5px 10px;
            font-size: 12px;
        }

        .btn-danger {
            background-color: #dc3545;
        }

        .btn-danger:hover {
            background-color: #c82333;
        }

        .empty-cart {
            text-align: center;
            padding: 60px 20px;
            background: white;
            border-radius: 15px;
            box-shadow: 0 8px 16px rgba(0,0,0,0.08);
        }

        .empty-cart i {
            font-size: 4rem;
            color: #ccc;
            margin-bottom: 20px;
        }

        /* Responsive design */
        @media (max-width: 768px) {
            .cart-table {
                overflow-x: auto;
            }

            .cart-table table {
                min-width: 600px;
            }

            .user-menu {
                position: relative;
                top: auto;
                right: auto;
                text-align: center;
                margin-bottom: 20px;
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
        <a href="panier.php" class="active"><i class="fas fa-shopping-cart"></i> Panier</a>
    </nav>
</header>

<!-- Cart Content -->
<div class="cart-container">
    <h1 style="text-align: center; color: #7c943f; margin-bottom: 40px;">
        <i class="fas fa-shopping-cart"></i> Mon Panier
    </h1>
    
    <div id="cart-content">
        <!-- Cart items will be loaded here by JavaScript -->
    </div>
    
    <div class="empty-cart" id="empty-cart" style="display: none;">
        <i class="fas fa-shopping-cart"></i>
        <h3>Votre panier est vide</h3>
        <p>Découvrez nos magnifiques produits cosmétiques naturels</p>
        <a href="products.php" class="btn-cart" style="font-size: 1.2em; padding: 15px 30px;">
            <i class="fas fa-shopping-bag"></i> Découvrir nos produits
        </a>
    </div>
    
    <div class="cart-total" id="cart-total" style="display: none;">
        <h3>Récapitulatif</h3>
        <div style="font-size: 1.5em; margin: 20px 0;">
            <strong>Total: <span id="total-amount">0,00 €</span></strong>
        </div>
        
        <?php if (isLoggedIn()): ?>
            <button class="btn-cart" style="font-size: 1.2em; padding: 15px 30px;" onclick="proceedToCheckout()">
                <i class="fas fa-credit-card"></i> Procéder au paiement
            </button>
        <?php else: ?>
            <p style="margin: 20px 0;">Connectez-vous pour finaliser votre commande</p>
            <a href="client/login.php" class="btn-cart" style="font-size: 1.2em; padding: 15px 30px;">
                <i class="fas fa-sign-in-alt"></i> Se connecter
            </a>
        <?php endif; ?>
        
        <br>
        <a href="products.php" class="btn-cart" style="background-color: #6c757d;">
            <i class="fas fa-arrow-left"></i> Continuer mes achats
        </a>
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
    <a href="products.php" style="color: white; text-decoration: none;">Nos produits</a>
</div>

<!-- JavaScript -->
<script src="assets/js/cart.js"></script>
<script>
// Enhanced cart display function
function afficherPanier() {
    try {
        let panier = JSON.parse(localStorage.getItem('panier')) || [];
        let cartContent = document.getElementById('cart-content');
        let emptyCart = document.getElementById('empty-cart');
        let cartTotal = document.getElementById('cart-total');

        // Check if elements exist
        if (!cartContent || !emptyCart || !cartTotal) {
            console.error('Cart elements not found');
            return;
        }

        if (panier.length === 0) {
            cartContent.innerHTML = '';
            emptyCart.style.display = 'block';
            cartTotal.style.display = 'none';
            return;
        }

        emptyCart.style.display = 'none';
        cartTotal.style.display = 'block';

        let tableHTML = `
            <div class="cart-table">
                <table>
                    <thead>
                        <tr>
                            <th>Produit</th>
                            <th>Prix unitaire</th>
                            <th>Quantité</th>
                            <th>Total</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
        `;

        let total = 0;
        panier.forEach((item, i) => {
            // Validate item data
            if (!item.nom || !item.prix || !item.quantite) {
                console.warn('Invalid cart item:', item);
                return;
            }

            const itemTotal = item.quantite * item.prix;
            total += itemTotal;

            tableHTML += `
                <tr>
                    <td><strong>${escapeHtml(item.nom)}</strong></td>
                    <td>${parseFloat(item.prix).toFixed(3)} TND</td>
                    <td>
                        <button class="btn-cart btn-sm" onclick="updateQuantity(${i}, -1)" title="Diminuer la quantité">-</button>
                        <span style="margin: 0 10px; font-weight: bold;">${item.quantite}</span>
                        <button class="btn-cart btn-sm" onclick="updateQuantity(${i}, 1)" title="Augmenter la quantité">+</button>
                    </td>
                    <td><strong>${itemTotal.toFixed(3)} TND</strong></td>
                    <td>
                        <button class="btn-cart btn-danger btn-sm" onclick="retirerDuPanier(${i})" title="Retirer du panier">
                            <i class="fas fa-trash"></i> Retirer
                        </button>
                    </td>
                </tr>
            `;
        });

        tableHTML += `
                    </tbody>
                </table>
            </div>
        `;

        cartContent.innerHTML = tableHTML;

        // Update total amount
        const totalElement = document.getElementById('total-amount');
        if (totalElement) {
            totalElement.textContent = total.toFixed(3) + ' TND';
        }

    } catch (error) {
        console.error('Error displaying cart:', error);
        // Show error message to user
        const cartContent = document.getElementById('cart-content');
        if (cartContent) {
            cartContent.innerHTML = `
                <div style="text-align: center; padding: 40px; background: white; border-radius: 15px; box-shadow: 0 8px 16px rgba(0,0,0,0.08);">
                    <i class="fas fa-exclamation-triangle" style="font-size: 3rem; color: #dc3545; margin-bottom: 20px;"></i>
                    <h3>Erreur lors du chargement du panier</h3>
                    <p>Une erreur s'est produite. Veuillez rafraîchir la page.</p>
                    <button class="btn-cart" onclick="location.reload()">Rafraîchir</button>
                </div>
            `;
        }
    }
}

// Utility function to escape HTML
function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// Update quantity function
function updateQuantity(index, change) {
    try {
        let panier = JSON.parse(localStorage.getItem('panier')) || [];
        if (panier[index]) {
            panier[index].quantite += change;
            if (panier[index].quantite <= 0) {
                panier.splice(index, 1);
            }
            localStorage.setItem('panier', JSON.stringify(panier));
            afficherPanier();
        }
    } catch (error) {
        console.error('Error updating quantity:', error);
        alert('Erreur lors de la mise à jour de la quantité');
    }
}

// Enhanced remove function
function retirerDuPanier(index) {
    try {
        let panier = JSON.parse(localStorage.getItem('panier')) || [];
        if (confirm('Êtes-vous sûr de vouloir retirer cet article du panier ?')) {
            panier.splice(index, 1);
            localStorage.setItem('panier', JSON.stringify(panier));
            afficherPanier();
        }
    } catch (error) {
        console.error('Error removing item:', error);
        alert('Erreur lors de la suppression de l\'article');
    }
}

// Checkout function
function proceedToCheckout() {
    try {
        let panier = JSON.parse(localStorage.getItem('panier')) || [];
        if (panier.length === 0) {
            alert('Votre panier est vide !');
            return;
        }

        // Validate cart items before checkout
        let validItems = panier.filter(item => item.nom && item.prix && item.quantite > 0);
        if (validItems.length !== panier.length) {
            console.warn('Some invalid items found in cart, cleaning up...');
            localStorage.setItem('panier', JSON.stringify(validItems));
            afficherPanier();
        }

        if (validItems.length === 0) {
            alert('Votre panier ne contient aucun article valide !');
            return;
        }

        // Redirect to checkout page
        window.location.href = 'checkout.php';
    } catch (error) {
        console.error('Error proceeding to checkout:', error);
        alert('Erreur lors de la redirection vers le paiement');
    }
}

// Clear cart function
function clearCart() {
    if (confirm('Êtes-vous sûr de vouloir vider complètement votre panier ?')) {
        localStorage.removeItem('panier');
        afficherPanier();
    }
}

// Load cart on page load
document.addEventListener('DOMContentLoaded', function() {
    console.log('Cart page loaded');
    afficherPanier();

    // Add clear cart button if cart has items
    setTimeout(() => {
        const panier = JSON.parse(localStorage.getItem('panier')) || [];
        if (panier.length > 0) {
            const cartTotal = document.getElementById('cart-total');
            if (cartTotal) {
                const clearButton = document.createElement('button');
                clearButton.className = 'btn-cart';
                clearButton.style.backgroundColor = '#6c757d';
                clearButton.style.marginTop = '10px';
                clearButton.innerHTML = '<i class="fas fa-trash-alt"></i> Vider le panier';
                clearButton.onclick = clearCart;
                cartTotal.appendChild(clearButton);
            }
        }
    }, 100);
});
</script>

</body>
</html>
