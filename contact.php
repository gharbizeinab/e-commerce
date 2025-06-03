<?php
/**
 * Contact Page
 * Contact form and company information
 */

require_once 'config/database.php';
require_once 'config/session.php';
require_once 'includes/functions.php';

$page_title = 'Contact - La Beauté Bio';
$success = false;
$errors = [];

// Page de contact simple - Affichage des coordonnées uniquement
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
        .contact-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 40px 20px;
        }
        
        .contact-section {
            background: white;
            border-radius: 15px;
            box-shadow: 0 8px 16px rgba(0,0,0,0.08);
            padding: 40px;
            margin-bottom: 30px;
        }
        
        .contact-grid {
            display: flex;
            justify-content: center;
            margin-top: 30px;
        }
        
        .contact-info {
            background: #f8f9fa;
            padding: 30px;
            border-radius: 15px;
            max-width: 600px;
            width: 100%;
        }
        
        .contact-info h3 {
            color: #7c943f;
            margin-bottom: 20px;
        }
        
        .contact-info .info-item {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
            padding: 15px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }
        
        .contact-info .info-item i {
            font-size: 1.5rem;
            color: #7c943f;
            margin-right: 15px;
            width: 30px;
        }
        

        
        .map-section {
            text-align: center;
            padding: 40px;
            background: #f8f9fa;
            border-radius: 15px;
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
            .contact-grid {
                grid-template-columns: 1fr;
                gap: 20px;
            }
        }
    </style>
</head>
<body>

<!-- User Menu -->
<div class="user-menu">
    <?php if (isLoggedIn()): ?>
        <a href="client/profile.php"><i class="fas fa-user"></i> Mon Profil</a>
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
        <a href="contact.php" class="active">Contact</a>
        <a href="panier.php"><i class="fas fa-shopping-cart"></i> Panier</a>
    </nav>
</header>

<!-- Banner Section -->
<section class="banner-container">
    <img src="assets/images/savons.jpg" alt="Contact La Beauté Bio" class="banner-image" />
    <div class="bio-circle">
        <img src="assets/images/brand.png" style="width: 300px;" alt="Logo La Beauté Bio"/>
    </div>
</section>

<!-- Contact Content -->
<div class="contact-container">
    <div class="contact-section">
        <h1 style="text-align: center; color: #7c943f; margin-bottom: 20px;">
            Contactez-nous
        </h1>
        <p style="text-align: center; color: #666; font-size: 1.1em;">
            Une question, besoin d'informations ? Contactez-nous directement !
        </p>



        <div class="contact-grid">
            <!-- Contact Information -->
            <div class="contact-info">
                <h3><i class="fas fa-info-circle"></i> Nos Coordonnées</h3>
                
                <div class="info-item">
                    <i class="fas fa-map-marker-alt"></i>
                    <div>
                        <strong>Adresse</strong><br>
                        15 Avenue Habib Bourguiba<br>
                        1000 Tunis, Tunisie<br>
                        <small>Près de la Place de l'Indépendance</small>
                    </div>
                </div>
                
                <div class="info-item">
                    <i class="fas fa-phone"></i>
                    <div>
                        <strong>Téléphone</strong><br>
                        +216 71 123 456<br>
                        +216 98 765 432<br>
                        <small>Lun-Ven: 8h30-17h30</small>
                    </div>
                </div>
                
                <div class="info-item">
                    <i class="fas fa-envelope"></i>
                    <div>
                        <strong>Email</strong><br>
                        contact@labeautebio.tn<br>
                        <small>Réponse sous 24h</small>
                    </div>
                </div>
                
                <div class="info-item">
                    <i class="fas fa-clock"></i>
                    <div>
                        <strong>Horaires d'ouverture</strong><br>
                        Lundi - Vendredi: 9h00 - 18h00<br>
                        Samedi: 9h00 - 17h00<br>
                        Dimanche: 10h00 - 14h00<br>
                        <small>Fermé les jours fériés</small>
                    </div>
                </div>
            </div>


        </div>
    </div>

    <!-- Map Section -->
    <div class="map-section">
        <h3 style="color: #7c943f; margin-bottom: 20px;">
            <i class="fas fa-map"></i> Nous Trouver
        </h3>
        <p style="color: #666;">
            Venez nous rendre visite dans notre boutique parisienne pour découvrir tous nos produits 
            et bénéficier des conseils personnalisés de notre équipe.
        </p>
        <div style="background: #e9ecef; height: 300px; border-radius: 15px; display: flex; align-items: center; justify-content: center; margin-top: 20px;">
            <p style="color: #666;">
                <i class="fas fa-map-marker-alt" style="font-size: 3rem; color: #7c943f;"></i><br>
                Carte interactive à intégrer<br>
                <small>123 Rue de la Beauté, 75001 Paris</small>
            </p>
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
    <a href="index.php" style="color: white; text-decoration: none;">Retour à l'accueil</a> |
    <a href="products.php" style="color: white; text-decoration: none;">Nos produits</a> |
    <a href="a-propos.php" style="color: white; text-decoration: none;">À propos</a>
</div>

<!-- JavaScript -->
<script src="assets/js/cart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
