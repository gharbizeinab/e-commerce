<?php
/**
 * Client Registration Page
 * Allow new users to create an account
 */

require_once '../config/database.php';
require_once '../config/session.php';
require_once '../includes/functions.php';

$page_title = 'Inscription';
$client_path = '';
$home_path = '../';
$css_path = '../assets/css/';
$js_path = '../assets/js/';

// Redirect if already logged in
if (isLoggedIn()) {
    header('Location: ../index.php');
    exit();
}

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate CSRF token
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $errors[] = 'Token de sécurité invalide.';
    } else {
        // Sanitize input
        $username = sanitizeInput($_POST['username'] ?? '');
        $email = sanitizeInput($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';
        $full_name = sanitizeInput($_POST['full_name'] ?? '');
        $phone = sanitizeInput($_POST['phone'] ?? '');
        $address = sanitizeInput($_POST['address'] ?? '');

        // Validation
        if (empty($username)) {
            $errors[] = 'Le nom d\'utilisateur est requis.';
        } elseif (strlen($username) < 3) {
            $errors[] = 'Le nom d\'utilisateur doit contenir au moins 3 caractères.';
        } elseif (getUserByUsername($pdo, $username)) {
            $errors[] = 'Ce nom d\'utilisateur est déjà utilisé.';
        }

        if (empty($email)) {
            $errors[] = 'L\'adresse email est requise.';
        } elseif (!isValidEmail($email)) {
            $errors[] = 'L\'adresse email n\'est pas valide.';
        } elseif (getUserByEmail($pdo, $email)) {
            $errors[] = 'Cette adresse email est déjà utilisée.';
        }

        if (empty($password)) {
            $errors[] = 'Le mot de passe est requis.';
        } elseif (strlen($password) < 6) {
            $errors[] = 'Le mot de passe doit contenir au moins 6 caractères.';
        }

        if ($password !== $confirm_password) {
            $errors[] = 'Les mots de passe ne correspondent pas.';
        }

        if (empty($full_name)) {
            $errors[] = 'Le nom complet est requis.';
        }

        // Create user if no errors
        if (empty($errors)) {
            $user_data = [
                'username' => $username,
                'email' => $email,
                'password' => hashPassword($password),
                'full_name' => $full_name,
                'phone' => $phone,
                'address' => $address,
                'role' => 'client'
            ];

            if (createUser($pdo, $user_data)) {
                $success = true;
                $_SESSION['success_message'] = 'Inscription réussie ! Vous pouvez maintenant vous connecter.';
            } else {
                $errors[] = 'Erreur lors de la création du compte. Veuillez réessayer.';
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
    <title>Inscription - La Beauté Bio</title>

    <!-- Frontend CSS -->
    <link rel="stylesheet" href="../assets/css/frontend.css">
    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <style>
        body {
            background: linear-gradient(135deg, #7c943f, #5c7045);
            min-height: 100vh;
            padding: 20px 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .register-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            overflow: hidden;
            max-width: 1000px;
            width: 100%;
            margin: 0 auto;
        }

        .register-grid {
            display: grid;
            grid-template-columns: 1fr 1.2fr;
            min-height: 700px;
        }

        .register-image {
            background: linear-gradient(135deg, rgba(124, 148, 63, 0.9), rgba(92, 112, 69, 0.9)),
                        url('../assets/images/savons.jpg');
            background-size: cover;
            background-position: center;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            color: white;
            text-align: center;
            padding: 40px;
        }

        .register-image h2 {
            font-size: 2.5rem;
            margin-bottom: 20px;
            font-weight: bold;
        }

        .register-image p {
            font-size: 1.1rem;
            line-height: 1.6;
            opacity: 0.9;
        }

        .register-form {
            padding: 40px;
            overflow-y: auto;
        }

        .register-form h1 {
            color: #7c943f;
            font-size: 2rem;
            margin-bottom: 10px;
            text-align: center;
        }

        .register-form .subtitle {
            color: #666;
            text-align: center;
            margin-bottom: 30px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            color: #7c943f;
            font-weight: bold;
            margin-bottom: 8px;
            display: block;
        }

        .form-control {
            border: 2px solid #e9ecef;
            border-radius: 15px;
            padding: 12px 18px;
            font-size: 1rem;
            transition: all 0.3s;
            background-color: #f8f9fa;
        }

        .form-control:focus {
            border-color: #7c943f;
            box-shadow: 0 0 0 0.2rem rgba(124, 148, 63, 0.25);
            background-color: white;
        }

        .btn-register {
            background: linear-gradient(135deg, #7c943f, #5c7045);
            color: white;
            border: none;
            padding: 15px 30px;
            font-weight: bold;
            border-radius: 15px;
            cursor: pointer;
            transition: all 0.3s;
            width: 100%;
            font-size: 1.1rem;
            margin-top: 20px;
        }

        .btn-register:hover {
            background: linear-gradient(135deg, #5c7045, #4a5a37);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.2);
        }

        .back-home {
            position: absolute;
            top: 20px;
            left: 20px;
            background: rgba(255,255,255,0.9);
            color: #7c943f;
            padding: 10px 20px;
            border-radius: 25px;
            text-decoration: none;
            font-weight: bold;
            transition: all 0.3s;
            z-index: 1000;
        }

        .back-home:hover {
            background: #7c943f;
            color: white;
        }

        .login-link {
            text-align: center;
            margin-top: 20px;
        }

        .login-link a {
            color: #7c943f;
            text-decoration: none;
            font-weight: bold;
        }

        .login-link a:hover {
            text-decoration: underline;
        }

        .success-message {
            background: linear-gradient(135deg, #28a745, #20c997);
            color: white;
            padding: 30px;
            border-radius: 15px;
            text-align: center;
            margin: 20px 0;
        }

        .success-message h3 {
            margin-bottom: 15px;
        }

        .success-message a {
            background: white;
            color: #28a745;
            padding: 10px 25px;
            border-radius: 25px;
            text-decoration: none;
            font-weight: bold;
            display: inline-block;
            margin-top: 15px;
        }

        @media (max-width: 768px) {
            .register-grid {
                grid-template-columns: 1fr;
            }

            .register-image {
                min-height: 200px;
            }

            .register-form {
                padding: 30px 20px;
            }
        }
    </style>
</head>
<body>

<a href="../index.php" class="back-home">
    <i class="fas fa-home"></i> Retour à l'accueil
</a>

<div class="register-container">
    <div class="register-grid">
        <!-- Left Side - Image and Branding -->
        <div class="register-image">
            <h2>Rejoignez-nous</h2>
            <p>Créez votre compte pour découvrir notre univers de cosmétiques naturels et bio. Profitez d'une expérience d'achat personnalisée et de nos offres exclusives.</p>
            <div style="margin-top: 30px;">
                <i class="fas fa-user-plus" style="font-size: 3rem; opacity: 0.7;"></i>
            </div>
        </div>

        <!-- Right Side - Register Form -->
        <div class="register-form">
            <h1><i class="fas fa-user-plus"></i> Créer un compte</h1>
            <p class="subtitle">Rejoignez la communauté La Beauté Bio</p>
            <?php if ($success): ?>
                <div class="success-message">
                    <h3><i class="fas fa-check-circle"></i> Inscription réussie !</h3>
                    <p>Votre compte a été créé avec succès. Vous pouvez maintenant vous connecter.</p>
                    <a href="login.php">Se connecter maintenant</a>
                </div>
            <?php else: ?>
                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger" style="border-radius: 15px; margin-bottom: 25px;">
                        <i class="fas fa-exclamation-circle"></i>
                        <ul class="mb-0" style="padding-left: 20px;">
                            <?php foreach ($errors as $error): ?>
                                <li><?php echo htmlspecialchars($error); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <form method="POST" class="needs-validation" novalidate>
                    <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="username"><i class="fas fa-user"></i> Nom d'utilisateur *</label>
                                <input type="text" class="form-control" id="username" name="username"
                                       value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>"
                                       placeholder="Votre nom d'utilisateur"
                                       required minlength="3">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="email"><i class="fas fa-envelope"></i> Adresse email *</label>
                                <input type="email" class="form-control" id="email" name="email"
                                       value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>"
                                       placeholder="votre@email.com"
                                       required>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="full_name"><i class="fas fa-id-card"></i> Nom complet *</label>
                        <input type="text" class="form-control" id="full_name" name="full_name"
                               value="<?php echo htmlspecialchars($_POST['full_name'] ?? ''); ?>"
                               placeholder="Votre nom complet"
                               required>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="password"><i class="fas fa-lock"></i> Mot de passe *</label>
                                <input type="password" class="form-control" id="password" name="password"
                                       placeholder="Minimum 6 caractères"
                                       required minlength="6">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="confirm_password"><i class="fas fa-lock"></i> Confirmer le mot de passe *</label>
                                <input type="password" class="form-control" id="confirm_password" name="confirm_password"
                                       placeholder="Répétez votre mot de passe"
                                       required minlength="6">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="phone"><i class="fas fa-phone"></i> Téléphone</label>
                                <input type="tel" class="form-control" id="phone" name="phone"
                                       value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>"
                                       placeholder="Votre numéro de téléphone">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="address"><i class="fas fa-map-marker-alt"></i> Adresse</label>
                                <textarea class="form-control" id="address" name="address" rows="2"
                                          placeholder="Votre adresse complète"><?php echo htmlspecialchars($_POST['address'] ?? ''); ?></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="form-check" style="margin: 20px 0;">
                        <input type="checkbox" class="form-check-input" id="terms" required>
                        <label class="form-check-label" for="terms" style="color: #666;">
                            J'accepte les <a href="#" style="color: #7c943f;">conditions d'utilisation</a> et la
                            <a href="#" style="color: #7c943f;">politique de confidentialité</a> *
                        </label>
                    </div>

                    <button type="submit" class="btn-register">
                        <i class="fas fa-user-plus"></i> Créer mon compte
                    </button>
                </form>
            <?php endif; ?>

            <div class="login-link">
                <p>Vous avez déjà un compte ?</p>
                <a href="login.php">
                    <i class="fas fa-sign-in-alt"></i> Se connecter
                </a>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript -->
<script src="../assets/js/cart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Password confirmation validation
document.getElementById('confirm_password').addEventListener('input', function() {
    const password = document.getElementById('password').value;
    const confirmPassword = this.value;

    if (password !== confirmPassword) {
        this.setCustomValidity('Les mots de passe ne correspondent pas.');
        this.style.borderColor = '#dc3545';
    } else {
        this.setCustomValidity('');
        this.style.borderColor = '#7c943f';
    }
});

// Real-time password strength indicator
document.getElementById('password').addEventListener('input', function() {
    const password = this.value;
    const strength = password.length >= 6 ? 'Fort' : 'Faible';
    const color = password.length >= 6 ? '#28a745' : '#dc3545';

    this.style.borderColor = color;
});
</script>

</body>
</html>
