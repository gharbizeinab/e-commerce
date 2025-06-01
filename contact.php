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

// Handle contact form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $errors[] = 'Token de sécurité invalide.';
    } else {
        $name = sanitizeInput($_POST['name'] ?? '');
        $email = sanitizeInput($_POST['email'] ?? '');
        $subject = sanitizeInput($_POST['subject'] ?? '');
        $message = sanitizeInput($_POST['message'] ?? '');

        // Validation
        if (empty($name)) {
            $errors[] = 'Le nom est requis.';
        }
        if (empty($email)) {
            $errors[] = 'L\'email est requis.';
        } elseif (!isValidEmail($email)) {
            $errors[] = 'L\'email n\'est pas valide.';
        }
        if (empty($subject)) {
            $errors[] = 'Le sujet est requis.';
        }
        if (empty($message)) {
            $errors[] = 'Le message est requis.';
        }

        // Save message if no errors
        if (empty($errors)) {
            try {
                $stmt = $pdo->prepare("INSERT INTO contact_messages (name, email, subject, message) VALUES (?, ?, ?, ?)");
                if ($stmt->execute([$name, $email, $subject, $message])) {
                    $success = true;
                    $_SESSION['success_message'] = 'Votre message a été envoyé avec succès ! Nous vous répondrons dans les plus brefs délais.';
                    // Clear form data
                    $_POST = [];
                } else {
                    $errors[] = 'Erreur lors de l\'envoi du message. Veuillez réessayer.';
                }
            } catch (Exception $e) {
                $errors[] = 'Erreur lors de l\'envoi du message. Veuillez réessayer.';
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
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
            margin-top: 30px;
        }
        
        .contact-info {
            background: #f8f9fa;
            padding: 30px;
            border-radius: 15px;
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
        
        .contact-form {
            background: white;
        }
        
        .contact-form h3 {
            color: #7c943f;
            margin-bottom: 20px;
        }
        
        .form-control {
            border: 2px solid #e9ecef;
            border-radius: 10px;
            padding: 12px 15px;
            margin-bottom: 20px;
            transition: border-color 0.3s;
        }
        
        .form-control:focus {
            border-color: #7c943f;
            box-shadow: 0 0 0 0.2rem rgba(124, 148, 63, 0.25);
        }
        
        .btn-contact {
            background-color: #7c943f;
            color: white;
            border: none;
            padding: 12px 30px;
            font-weight: bold;
            border-radius: 10px;
            cursor: pointer;
            transition: background 0.3s;
            text-decoration: none;
            display: inline-block;
        }
        
        .btn-contact:hover {
            background-color: #5d722e;
            color: white;
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
            <i class="fas fa-envelope"></i> Contactez-nous
        </h1>
        <p style="text-align: center; color: #666; font-size: 1.1em;">
            Une question, une suggestion, un mot doux ? Nous sommes là pour vous écouter !
        </p>

        <?php if ($success): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> Votre message a été envoyé avec succès ! 
                Nous vous répondrons dans les plus brefs délais.
            </div>
        <?php endif; ?>

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

        <div class="contact-grid">
            <!-- Contact Information -->
            <div class="contact-info">
                <h3><i class="fas fa-info-circle"></i> Nos Coordonnées</h3>
                
                <div class="info-item">
                    <i class="fas fa-map-marker-alt"></i>
                    <div>
                        <strong>Adresse</strong><br>
                        123 Rue de la Beauté<br>
                        75001 Paris, France
                    </div>
                </div>
                
                <div class="info-item">
                    <i class="fas fa-phone"></i>
                    <div>
                        <strong>Téléphone</strong><br>
                        +33 1 23 45 67 89<br>
                        <small>Lun-Ven: 9h-18h</small>
                    </div>
                </div>
                
                <div class="info-item">
                    <i class="fas fa-envelope"></i>
                    <div>
                        <strong>Email</strong><br>
                        contact@labeautebio.fr<br>
                        <small>Réponse sous 24h</small>
                    </div>
                </div>
                
                <div class="info-item">
                    <i class="fas fa-clock"></i>
                    <div>
                        <strong>Horaires</strong><br>
                        Lundi - Vendredi: 9h - 18h<br>
                        Samedi: 10h - 16h<br>
                        Dimanche: Fermé
                    </div>
                </div>
            </div>

            <!-- Contact Form -->
            <div class="contact-form">
                <h3><i class="fas fa-paper-plane"></i> Envoyez-nous un message</h3>
                
                <form method="POST" class="needs-validation" novalidate>
                    <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                    
                    <div class="row">
                        <div class="col-md-6">
                            <input type="text" class="form-control" name="name" placeholder="Votre nom *" 
                                   value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>" required>
                        </div>
                        <div class="col-md-6">
                            <input type="email" class="form-control" name="email" placeholder="Votre email *" 
                                   value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" required>
                        </div>
                    </div>
                    
                    <input type="text" class="form-control" name="subject" placeholder="Sujet de votre message *" 
                           value="<?php echo htmlspecialchars($_POST['subject'] ?? ''); ?>" required>
                    
                    <textarea class="form-control" name="message" rows="6" placeholder="Votre message *" required><?php echo htmlspecialchars($_POST['message'] ?? ''); ?></textarea>
                    
                    <button type="submit" class="btn-contact">
                        <i class="fas fa-paper-plane"></i> Envoyer le message
                    </button>
                </form>
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
