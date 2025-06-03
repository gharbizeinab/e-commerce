<?php
/**
 * Client Profile Page
 * Allow clients to view and update their profile information
 */

require_once '../config/database.php';
require_once '../config/session.php';
require_once '../includes/functions.php';

// Require client login
requireLogin('../client/login.php');

$page_title = 'Mon Profil';
$client_path = '';
$home_path = '../';
$css_path = '../assets/css/';
$js_path = '../assets/js/';

// Generate CSRF token
function generateCSRFToken() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

// Verify CSRF token
function verifyCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

$errors = [];
$success = false;

// Get current user data
$user_id = $_SESSION['user_id'];
$result = executeQuery("SELECT * FROM users WHERE id = '$user_id'");
$user = mysqli_fetch_assoc($result);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate CSRF token
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $errors[] = 'Token de sécurité invalide.';
    } else {
        // Sanitize input
        $full_name = cleanInput($_POST['full_name'] ?? '');
        $phone = cleanInput($_POST['phone'] ?? '');
        $address = cleanInput($_POST['address'] ?? '');
        $current_password = $_POST['current_password'] ?? '';
        $new_password = $_POST['new_password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';

        // Validation
        if (empty($full_name)) {
            $errors[] = 'Le nom complet est requis.';
        }

        // Password change validation
        if (!empty($new_password)) {
            if (empty($current_password)) {
                $errors[] = 'Veuillez saisir votre mot de passe actuel.';
            } elseif (!verifyPassword($current_password, $user['password'])) {
                $errors[] = 'Mot de passe actuel incorrect.';
            }

            if (strlen($new_password) < 6) {
                $errors[] = 'Le nouveau mot de passe doit contenir au moins 6 caractères.';
            }

            if ($new_password !== $confirm_password) {
                $errors[] = 'Les nouveaux mots de passe ne correspondent pas.';
            }
        }

        // Update profile if no errors
        if (empty($errors)) {
            $update_data = [
                'full_name' => $full_name,
                'phone' => $phone,
                'address' => $address
            ];

            // Update user data manually
            $full_name_clean = cleanInput($full_name);
            $phone_clean = cleanInput($phone);
            $address_clean = cleanInput($address);

            $sql = "UPDATE users SET full_name = '$full_name_clean', phone = '$phone_clean', address = '$address_clean', updated_at = NOW() WHERE id = '$user_id'";
            $result = executeQuery($sql);

            if ($result) {
                // Update password if provided
                if (!empty($new_password)) {
                    $hashed_password = hashPassword($new_password);
                    $sql_password = "UPDATE users SET password = '$hashed_password', updated_at = NOW() WHERE id = '$user_id'";
                    executeQuery($sql_password);
                }

                $success = true;
                $_SESSION['success_message'] = 'Profil mis à jour avec succès !';
                $_SESSION['full_name'] = $full_name; // Update session data

                // Refresh user data
                $result = executeQuery("SELECT * FROM users WHERE id = '$user_id'");
                $user = mysqli_fetch_assoc($result);
            } else {
                $errors[] = 'Erreur lors de la mise à jour du profil. Veuillez réessayer.';
            }
        }
    }
}

// Get user's orders
$result = executeQuery("SELECT o.*, COUNT(oi.id) as item_count FROM orders o
                      LEFT JOIN order_items oi ON o.id = oi.order_id
                      WHERE o.user_id = '$user_id'
                      GROUP BY o.id
                      ORDER BY o.created_at DESC LIMIT 5");
$recent_orders = array();
while ($row = mysqli_fetch_assoc($result)) {
    $recent_orders[] = $row;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?> - La Beauté Bio</title>

    <!-- Frontend CSS -->
    <link rel="stylesheet" href="../assets/css/frontend.css">
    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <style>
        .profile-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 40px 20px;
        }

        .profile-header {
            background: linear-gradient(135deg, #7c943f 0%, #5d722e 100%);
            border-radius: 20px;
            padding: 40px;
            color: white;
            margin-bottom: 40px;
            position: relative;
            overflow: hidden;
        }

        .profile-header::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200%;
            height: 200%;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><circle cx="50" cy="50" r="2" fill="rgba(255,255,255,0.1)"/></svg>') repeat;
            animation: float 20s infinite linear;
        }

        @keyframes float {
            0% { transform: translateX(-50px) translateY(-50px); }
            100% { transform: translateX(50px) translateY(50px); }
        }

        .profile-avatar {
            width: 120px;
            height: 120px;
            background: rgba(255,255,255,0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 3rem;
            margin-bottom: 20px;
            border: 4px solid rgba(255,255,255,0.3);
        }

        .profile-section {
            background: white;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            padding: 30px;
            margin-bottom: 30px;
            transition: transform 0.3s ease;
        }

        .profile-section:hover {
            transform: translateY(-5px);
        }

        .section-title {
            color: #7c943f;
            font-size: 1.5rem;
            font-weight: bold;
            margin-bottom: 25px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .section-title::after {
            content: '';
            flex: 1;
            height: 2px;
            background: linear-gradient(to right, #7c943f, transparent);
        }

        .form-control {
            border: 2px solid #e9ecef;
            border-radius: 12px;
            padding: 12px 15px;
            margin-bottom: 20px;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: #7c943f;
            box-shadow: 0 0 0 0.2rem rgba(124, 148, 63, 0.25);
        }

        .btn-profile {
            background: linear-gradient(135deg, #7c943f 0%, #5d722e 100%);
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 25px;
            font-weight: bold;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-profile:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(124, 148, 63, 0.4);
            color: white;
        }

        .btn-outline-profile {
            background: transparent;
            color: #7c943f;
            border: 2px solid #7c943f;
            padding: 12px 30px;
            border-radius: 25px;
            font-weight: bold;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-outline-profile:hover {
            background: #7c943f;
            color: white;
            transform: translateY(-2px);
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-radius: 15px;
            padding: 25px;
            text-align: center;
            border: 2px solid transparent;
            transition: all 0.3s ease;
        }

        .stat-card:hover {
            border-color: #7c943f;
            transform: translateY(-3px);
        }

        .stat-number {
            font-size: 2.5rem;
            font-weight: bold;
            color: #7c943f;
            margin-bottom: 5px;
        }

        .stat-label {
            color: #6c757d;
            font-weight: 500;
        }

        .order-card {
            background: #f8f9fa;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 15px;
            border-left: 4px solid #7c943f;
            transition: all 0.3s ease;
        }

        .order-card:hover {
            background: #e9ecef;
            transform: translateX(5px);
        }

        .status-badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: bold;
        }

        .status-pending { background: #fff3cd; color: #856404; }
        .status-confirmed { background: #d1ecf1; color: #0c5460; }
        .status-shipped { background: #d4edda; color: #155724; }
        .status-delivered { background: #d1e7dd; color: #0f5132; }
        .status-cancelled { background: #f8d7da; color: #721c24; }

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
    <a href="orders.php"><i class="fas fa-shopping-bag"></i> Mes Commandes</a>
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

<!-- Profile Content -->
<div class="profile-container">
    <!-- Profile Header -->
    <div class="profile-header">
        <div class="row align-items-center">
            <div class="col-md-3 text-center">
                <div class="profile-avatar">
                    <i class="fas fa-user"></i>
                </div>
            </div>
            <div class="col-md-9">
                <h1 style="margin-bottom: 10px;"><?php echo htmlspecialchars($user['full_name']); ?></h1>
                <p style="margin-bottom: 5px; opacity: 0.9;"><i class="fas fa-envelope"></i> <?php echo htmlspecialchars($user['email']); ?></p>
                <p style="margin-bottom: 0; opacity: 0.8;"><i class="fas fa-calendar"></i> Membre depuis le <?php echo date('d/m/Y', strtotime($user['created_at'])); ?></p>
            </div>
        </div>
    </div>

    <!-- Statistics -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-number"><?php echo count($recent_orders); ?></div>
            <div class="stat-label">Commandes</div>
        </div>
        <div class="stat-card">
            <div class="stat-number">0</div>
            <div class="stat-label">Favoris</div>
        </div>
        <div class="stat-card">
            <div class="stat-number">
                <?php
                $total_spent = 0;
                foreach($recent_orders as $order) {
                    $total_spent += $order['total_amount'];
                }
                echo formatPrice($total_spent);
                ?>
            </div>
            <div class="stat-label">Total dépensé</div>
        </div>
    </div>

    <!-- Profile Form Section -->
    <div class="profile-section">
        <div class="section-title">
            <i class="fas fa-edit"></i>
            Modifier mon profil
        </div>

        <?php if ($success): ?>
            <div class="alert alert-success" style="border-radius: 12px; border: none; background: #d1e7dd; color: #0f5132;">
                <i class="fas fa-check-circle"></i> Profil mis à jour avec succès !
            </div>
        <?php endif; ?>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger" style="border-radius: 12px; border: none; background: #f8d7da; color: #721c24;">
                <i class="fas fa-exclamation-circle"></i>
                <ul class="mb-0" style="margin-left: 20px;">
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo htmlspecialchars($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form method="POST" class="needs-validation" novalidate>
            <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">

            <h6 style="color: #7c943f; font-weight: bold; margin-bottom: 20px; padding-bottom: 10px; border-bottom: 2px solid #e9ecef;">
                <i class="fas fa-user"></i> Informations personnelles
            </h6>

            <div class="row">
                <div class="col-md-6">
                    <label for="full_name" style="color: #495057; font-weight: 600; margin-bottom: 8px; display: block;">Nom complet *</label>
                    <input type="text" class="form-control" id="full_name" name="full_name"
                           value="<?php echo htmlspecialchars($_POST['full_name'] ?? $user['full_name']); ?>"
                           required>
                </div>
                <div class="col-md-6">
                    <label for="email" style="color: #495057; font-weight: 600; margin-bottom: 8px; display: block;">Adresse email</label>
                    <input type="email" class="form-control" id="email"
                           value="<?php echo htmlspecialchars($user['email']); ?>"
                           disabled style="background-color: #f8f9fa; opacity: 0.7;">
                    <small style="color: #6c757d; font-size: 0.875rem;">L'adresse email ne peut pas être modifiée.</small>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <label for="phone" style="color: #495057; font-weight: 600; margin-bottom: 8px; display: block;">Téléphone</label>
                    <input type="tel" class="form-control" id="phone" name="phone"
                           value="<?php echo htmlspecialchars($_POST['phone'] ?? $user['phone']); ?>">
                </div>
                <div class="col-md-6">
                    <label for="address" style="color: #495057; font-weight: 600; margin-bottom: 8px; display: block;">Adresse</label>
                    <textarea class="form-control" id="address" name="address" rows="3" style="resize: vertical;"><?php echo htmlspecialchars($_POST['address'] ?? $user['address']); ?></textarea>
                </div>
            </div>

            <h6 style="color: #7c943f; font-weight: bold; margin: 30px 0 20px 0; padding-bottom: 10px; border-bottom: 2px solid #e9ecef;">
                <i class="fas fa-lock"></i> Changer le mot de passe
            </h6>
            <p style="color: #6c757d; font-size: 0.9rem; margin-bottom: 20px;">Laissez vide si vous ne souhaitez pas changer votre mot de passe.</p>

            <div class="row">
                <div class="col-md-4">
                    <label for="current_password" style="color: #495057; font-weight: 600; margin-bottom: 8px; display: block;">Mot de passe actuel</label>
                    <input type="password" class="form-control" id="current_password" name="current_password">
                </div>
                <div class="col-md-4">
                    <label for="new_password" style="color: #495057; font-weight: 600; margin-bottom: 8px; display: block;">Nouveau mot de passe</label>
                    <input type="password" class="form-control" id="new_password" name="new_password" minlength="6">
                </div>
                <div class="col-md-4">
                    <label for="confirm_password" style="color: #495057; font-weight: 600; margin-bottom: 8px; display: block;">Confirmer le nouveau mot de passe</label>
                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" minlength="6">
                </div>
            </div>

            <div style="margin-top: 40px; display: flex; justify-content: space-between; align-items: center;">
                <a href="../index.php" class="btn-outline-profile">
                    <i class="fas fa-arrow-left"></i> Retour à l'accueil
                </a>
                <button type="submit" class="btn-profile">
                    <i class="fas fa-save"></i> Enregistrer les modifications
                </button>
            </div>
        </form>
    </div>

    <!-- Recent Orders -->
    <?php if (!empty($recent_orders)): ?>
        <div class="profile-section">
            <div class="section-title">
                <i class="fas fa-shopping-bag"></i>
                Mes commandes récentes
            </div>

            <?php foreach ($recent_orders as $order): ?>
                <div class="order-card">
                    <div class="row align-items-center">
                        <div class="col-md-2">
                            <strong style="color: #7c943f;">#<?php echo $order['order_number'] ?? $order['id']; ?></strong>
                        </div>
                        <div class="col-md-2">
                            <small style="color: #6c757d;">
                                <?php echo date('d/m/Y', strtotime($order['created_at'])); ?>
                            </small>
                        </div>
                        <div class="col-md-2">
                            <small style="color: #6c757d;">
                                <?php echo $order['item_count']; ?> article(s)
                            </small>
                        </div>
                        <div class="col-md-2">
                            <strong style="color: #7c943f;">
                                <?php echo formatPrice($order['total_amount']); ?>
                            </strong>
                        </div>
                        <div class="col-md-2">
                            <span class="status-badge status-<?php echo $order['status']; ?>">
                                <?php
                                switch($order['status']) {
                                    case 'pending': echo 'En attente'; break;
                                    case 'confirmed': echo 'Confirmée'; break;
                                    case 'shipped': echo 'Expédiée'; break;
                                    case 'delivered': echo 'Livrée'; break;
                                    case 'cancelled': echo 'Annulée'; break;
                                    default: echo ucfirst($order['status']);
                                }
                                ?>
                            </span>
                        </div>
                        <div class="col-md-2 text-end">
                            <a href="order_detail.php?id=<?php echo $order['id']; ?>" class="btn-outline-profile" style="padding: 6px 12px; font-size: 0.8rem;">
                                <i class="fas fa-eye"></i> Voir
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>

            <div style="text-align: center; margin-top: 20px;">
                <a href="orders.php" class="btn-profile">
                    <i class="fas fa-shopping-bag"></i> Voir toutes mes commandes
                </a>
            </div>
        </div>
    <?php endif; ?>

</div>

<!-- Footer -->
<footer style="background: linear-gradient(135deg, #7c943f 0%, #5d722e 100%); color: white; text-align: center; padding: 40px 20px; margin-top: 60px;">
    <div style="max-width: 1200px; margin: 0 auto;">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 30px; margin-bottom: 30px;">
            <div>
                <h4 style="margin-bottom: 15px;">La Beauté Bio</h4>
                <p style="opacity: 0.9;">Votre destination pour des produits de beauté naturels et biologiques.</p>
            </div>
            <div>
                <h5 style="margin-bottom: 15px;">Navigation</h5>
                <div style="display: flex; flex-direction: column; gap: 8px;">
                    <a href="../index.php" style="color: white; text-decoration: none; opacity: 0.9;">Accueil</a>
                    <a href="../products.php" style="color: white; text-decoration: none; opacity: 0.9;">Nos produits</a>
                    <a href="../contact.php" style="color: white; text-decoration: none; opacity: 0.9;">Contact</a>
                </div>
            </div>
            <div>
                <h5 style="margin-bottom: 15px;">Mon Compte</h5>
                <div style="display: flex; flex-direction: column; gap: 8px;">
                    <a href="orders.php" style="color: white; text-decoration: none; opacity: 0.9;">Mes commandes</a>
                    <a href="profile.php" style="color: white; text-decoration: none; opacity: 0.9;">Mon profil</a>
                    <a href="../logout.php" style="color: white; text-decoration: none; opacity: 0.9;">Déconnexion</a>
                </div>
            </div>
        </div>
        <div style="border-top: 1px solid rgba(255,255,255,0.2); padding-top: 20px; opacity: 0.8;">
            <p>&copy; 2024 La Beauté Bio. Tous droits réservés.</p>
        </div>
    </div>
</footer>

<script>
// Password confirmation validation
document.getElementById('confirm_password').addEventListener('input', function() {
    const newPassword = document.getElementById('new_password').value;
    const confirmPassword = this.value;

    if (newPassword !== confirmPassword) {
        this.setCustomValidity('Les mots de passe ne correspondent pas.');
        this.style.borderColor = '#dc3545';
    } else {
        this.setCustomValidity('');
        this.style.borderColor = '#7c943f';
    }
});

// Form validation styling
document.querySelectorAll('.form-control').forEach(input => {
    input.addEventListener('focus', function() {
        this.style.borderColor = '#7c943f';
        this.style.boxShadow = '0 0 0 0.2rem rgba(124, 148, 63, 0.25)';
    });

    input.addEventListener('blur', function() {
        if (this.value) {
            this.style.borderColor = '#7c943f';
        } else {
            this.style.borderColor = '#e9ecef';
        }
        this.style.boxShadow = 'none';
    });
});

// Smooth animations
document.querySelectorAll('.profile-section').forEach(section => {
    section.addEventListener('mouseenter', function() {
        this.style.transform = 'translateY(-5px)';
    });

    section.addEventListener('mouseleave', function() {
        this.style.transform = 'translateY(0)';
    });
});
</script>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
