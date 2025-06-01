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
        }
        
        .btn-cart:hover {
            background-color: #5d722e;
            color: white;
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
        }
        
        .empty-cart i {
            font-size: 4rem;
            color: #ccc;
            margin-bottom: 20px;
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
        <p><i class="fas fa-phone"></i> +33 1 23 45 67 89</p>
    </div>
    <div>
        <h3>Nous écrire</h3>
        <p><a href="mailto:contact@labeautebio.fr" style="color: white;"><i class="fas fa-envelope"></i> contact@labeautebio.fr</a></p>
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
    let panier = JSON.parse(localStorage.getItem('panier')) || [];
    let cartContent = document.getElementById('cart-content');
    let emptyCart = document.getElementById('empty-cart');
    let cartTotal = document.getElementById('cart-total');
    
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
        const itemTotal = item.quantite * item.prix;
        total += itemTotal;
        
        tableHTML += `
            <tr>
                <td><strong>${item.nom}</strong></td>
                <td>${item.prix.toFixed(2)} €</td>
                <td>
                    <button class="btn-cart btn-sm" onclick="updateQuantity(${i}, -1)">-</button>
                    <span style="margin: 0 10px; font-weight: bold;">${item.quantite}</span>
                    <button class="btn-cart btn-sm" onclick="updateQuantity(${i}, 1)">+</button>
                </td>
                <td><strong>${itemTotal.toFixed(2)} €</strong></td>
                <td>
                    <button class="btn-cart btn-danger btn-sm" onclick="retirerDuPanier(${i})">
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
    document.getElementById('total-amount').textContent = total.toFixed(2) + ' €';
}

// Update quantity function
function updateQuantity(index, change) {
    let panier = JSON.parse(localStorage.getItem('panier')) || [];
    if (panier[index]) {
        panier[index].quantite += change;
        if (panier[index].quantite <= 0) {
            panier.splice(index, 1);
        }
        localStorage.setItem('panier', JSON.stringify(panier));
        afficherPanier();
    }
}

// Enhanced remove function
function retirerDuPanier(index) {
    let panier = JSON.parse(localStorage.getItem('panier')) || [];
    if (confirm('Êtes-vous sûr de vouloir retirer cet article du panier ?')) {
        panier.splice(index, 1);
        localStorage.setItem('panier', JSON.stringify(panier));
        afficherPanier();
    }
}

// Checkout function
function proceedToCheckout() {
    let panier = JSON.parse(localStorage.getItem('panier')) || [];
    if (panier.length === 0) {
        alert('Votre panier est vide !');
        return;
    }

    // Redirect to checkout page
    window.location.href = 'checkout.php';
}

// Load cart on page load
document.addEventListener('DOMContentLoaded', function() {
    afficherPanier();
});
</script>

</body>
</html>
