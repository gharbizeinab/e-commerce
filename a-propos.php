<?php
/**
 * About Us Page - À propos
 * Information about La Beauté Bio company
 */

require_once 'config/database.php';
require_once 'config/session.php';
require_once 'includes/functions.php';

$page_title = 'À propos - La Beauté Bio';
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
        .about-content {
            max-width: 1000px;
            margin: 0 auto;
            padding: 40px 20px;
        }
        
        .about-section {
            background: white;
            border-radius: 15px;
            box-shadow: 0 8px 16px rgba(0,0,0,0.08);
            padding: 40px;
            margin-bottom: 30px;
        }
        
        .about-section h2 {
            color: #7c943f;
            margin-bottom: 20px;
            font-size: 2em;
        }
        
        .about-section h3 {
            color: #5c7045;
            margin: 25px 0 15px 0;
            font-size: 1.5em;
        }
        
        .about-section p {
            line-height: 1.8;
            color: #666;
            margin-bottom: 15px;
        }
        
        .values-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 30px;
            margin-top: 30px;
        }
        
        .value-card {
            text-align: center;
            padding: 30px 20px;
            background: #f8f9fa;
            border-radius: 15px;
            transition: transform 0.3s;
        }
        
        .value-card:hover {
            transform: translateY(-5px);
        }
        
        .value-card i {
            font-size: 3rem;
            color: #7c943f;
            margin-bottom: 20px;
        }
        
        .value-card h4 {
            color: #5c7045;
            margin-bottom: 15px;
        }
        
        .team-section {
            text-align: center;
            margin-top: 40px;
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
        <a href="a-propos.php" class="active">À propos</a>
        <a href="products.php">Nos produits</a>
        <a href="contact.php">Contact</a>
        <a href="panier.php"><i class="fas fa-shopping-cart"></i> Panier</a>
    </nav>
</header>

<!-- Banner Section -->
<section class="banner-container">
    <img src="assets/images/savons.jpg" alt="Savons Naturels" class="banner-image" />
    <div class="bio-circle">
        <img src="assets/images/brand.png" style="width: 300px;" alt="Logo La Beauté Bio"/>
    </div>
</section>

<!-- About Content -->
<div class="about-content">
    <div class="about-section">
        <h2><i class="fas fa-leaf"></i> Notre Histoire</h2>
        <p>
            <strong>La Beauté Bio Tunisie</strong> est née d'une passion pour la beauté naturelle et le respect de l'environnement.
            Fondée en 2018 à Tunis, notre entreprise tunisienne s'est donnée pour mission de proposer des produits cosmétiques 100% naturels,
            fabriqués avec amour et dans le respect des traditions artisanales méditerranéennes.
        </p>
        <p>
            Tout a commencé dans un petit atelier familial au cœur de la médina de Tunis, où nous avons développé nos premières recettes de savons
            à base d'ingrédients biologiques locaux comme l'huile d'olive tunisienne et les plantes aromatiques du terroir.
            Aujourd'hui, nous sommes fiers de proposer une gamme complète de produits cosmétiques qui allient efficacité, douceur et respect de la nature.
        </p>
    </div>

    <div class="about-section">
        <h2><i class="fas fa-heart"></i> Notre Philosophie</h2>
        <p>
            Chez La Beauté Bio Tunisie, nous croyons fermement que la beauté véritable vient de l'harmonie entre l'être humain et la nature méditerranéenne.
            C'est pourquoi tous nos produits sont formulés exclusivement à partir d'ingrédients d'origine naturelle,
            en privilégiant les trésors de la terre tunisienne, sans additifs chimiques nocifs.
        </p>
        
        <h3>Nos Engagements</h3>
        <ul style="color: #666; line-height: 1.8;">
            <li><strong>100% Naturel :</strong> Aucun produit chimique de synthèse</li>
            <li><strong>Éco-responsable :</strong> Emballages recyclables et biodégradables</li>
            <li><strong>Artisanal :</strong> Fabrication traditionnelle en petites quantités</li>
            <li><strong>Éthique :</strong> Commerce équitable et respect des producteurs</li>
            <li><strong>Non testé sur les animaux :</strong> Respect de la vie animale</li>
        </ul>
    </div>

    <div class="about-section">
        <h2><i class="fas fa-star"></i> Nos Valeurs</h2>
        <div class="values-grid">
            <div class="value-card">
                <i class="fas fa-leaf"></i>
                <h4>Naturel</h4>
                <p>Ingrédients 100% naturels et biologiques, respectueux de votre peau et de l'environnement.</p>
            </div>
            <div class="value-card">
                <i class="fas fa-heart"></i>
                <h4>Passion</h4>
                <p>Chaque produit est créé avec amour et passion pour vous offrir le meilleur de la nature.</p>
            </div>
            <div class="value-card">
                <i class="fas fa-shield-alt"></i>
                <h4>Qualité</h4>
                <p>Contrôle rigoureux de la qualité à chaque étape de la fabrication pour votre satisfaction.</p>
            </div>
            <div class="value-card">
                <i class="fas fa-recycle"></i>
                <h4>Écologie</h4>
                <p>Engagement pour la protection de l'environnement et le développement durable.</p>
            </div>
        </div>
    </div>

    <div class="about-section">
        <h2><i class="fas fa-award"></i> Nos Certifications</h2>
        <p>
            La Beauté Bio est certifiée par plusieurs organismes reconnus pour garantir la qualité 
            et l'authenticité de nos produits :
        </p>
        <ul style="color: #666; line-height: 1.8;">
            <li><strong>Ecocert :</strong> Certification biologique européenne</li>
            <li><strong>Cosmébio :</strong> Label français pour les cosmétiques biologiques</li>
            <li><strong>Cruelty Free :</strong> Aucun test sur les animaux</li>
            <li><strong>Vegan Society :</strong> Produits 100% végans</li>
        </ul>
    </div>

    <div class="about-section team-section">
        <h2><i class="fas fa-users"></i> Notre Équipe</h2>
        <p>
            Notre équipe passionnée travaille chaque jour pour vous proposer des produits d'exception. 
            Composée d'artisans savonniers, de chimistes spécialisés en cosmétique naturelle et 
            d'experts en développement durable, notre équipe partage les mêmes valeurs de respect 
            de la nature et de qualité.
        </p>
        <p style="margin-top: 30px;">
            <strong>Merci de faire confiance à La Beauté Bio pour prendre soin de votre beauté naturelle !</strong>
        </p>
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
    <a href="products.php" style="color: white; text-decoration: none;">Nos produits</a> |
    <a href="contact.php" style="color: white; text-decoration: none;">Contact</a>
</div>

<!-- JavaScript -->
<script src="assets/js/cart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
