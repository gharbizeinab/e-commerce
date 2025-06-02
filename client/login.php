<?php
/**
 * Client Login Page
 * Authentication for clients and admins
 */

require_once '../config/database.php';
require_once '../config/session.php';
require_once '../includes/functions.php';

$page_title = 'Connexion';
$client_path = '';
$home_path = '../';
$css_path = '../assets/css/';
$js_path = '../assets/js/';

// Redirect if already logged in
if (isLoggedIn()) {
    if (isAdmin()) {
        header('Location: ../admin/index.php');
    } else {
        header('Location: ../index.php');
    }
    exit();
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate CSRF token
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $errors[] = 'Token de sécurité invalide.';
    } else {
        $login = sanitizeInput($_POST['login'] ?? '');
        $password = $_POST['password'] ?? '';
        $remember_me = isset($_POST['remember_me']);

        // Validation
        if (empty($login)) {
            $errors[] = 'L\'email ou nom d\'utilisateur est requis.';
        }

        if (empty($password)) {
            $errors[] = 'Le mot de passe est requis.';
        }

        if (empty($errors)) {
            // Try to find user by email or username
            $user = getUserByEmail($login);
            if (!$user) {
                $user = getUserByUsername($connection, $login);
            }

            if ($user && verifyPassword($password, $user['password'])) {
                // Login successful
                loginUser($user);

                // Set remember me cookie if requested
                if ($remember_me) {
                    $token = bin2hex(random_bytes(32));
                    setcookie('remember_token', $token, time() + (30 * 24 * 60 * 60), '/', '', false, true);
                    // In a real application, you would store this token in the database
                }

                // Redirect based on role
                if ($user['role'] === 'admin') {
                    $_SESSION['success_message'] = 'Connexion réussie ! Bienvenue dans l\'administration.';
                    header('Location: ../admin/index.php');
                } else {
                    $_SESSION['success_message'] = 'Connexion réussie ! Bienvenue ' . htmlspecialchars($user['full_name']) . '.';
                    header('Location: ../index.php');
                }
                exit();
            } else {
                $errors[] = 'Email/nom d\'utilisateur ou mot de passe incorrect.';
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
    <title>Connexion - La Beauté Bio</title>

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
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .login-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            overflow: hidden;
            max-width: 900px;
            width: 100%;
            margin: 20px;
        }

        .login-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            min-height: 600px;
        }

        .login-image {
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

        .login-image h2 {
            font-size: 2.5rem;
            margin-bottom: 20px;
            font-weight: bold;
        }

        .login-image p {
            font-size: 1.1rem;
            line-height: 1.6;
            opacity: 0.9;
        }

        .login-form {
            padding: 60px 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .login-form h1 {
            color: #7c943f;
            font-size: 2rem;
            margin-bottom: 10px;
            text-align: center;
        }

        .login-form .subtitle {
            color: #666;
            text-align: center;
            margin-bottom: 40px;
        }

        .form-group {
            margin-bottom: 25px;
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
            padding: 15px 20px;
            font-size: 1rem;
            transition: all 0.3s;
            background-color: #f8f9fa;
        }

        .form-control:focus {
            border-color: #7c943f;
            box-shadow: 0 0 0 0.2rem rgba(124, 148, 63, 0.25);
            background-color: white;
        }

        .btn-login {
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

        .btn-login:hover {
            background: linear-gradient(135deg, #5c7045, #4a5a37);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.2);
        }

        .divider {
            text-align: center;
            margin: 30px 0;
            position: relative;
        }

        .divider::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            height: 1px;
            background: #e9ecef;
        }

        .divider span {
            background: white;
            padding: 0 20px;
            color: #666;
        }

        .register-link {
            text-align: center;
            margin-top: 20px;
        }

        .register-link a {
            color: #7c943f;
            text-decoration: none;
            font-weight: bold;
        }

        .register-link a:hover {
            text-decoration: underline;
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
        }

        .back-home:hover {
            background: #7c943f;
            color: white;
        }

        .demo-info {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 15px;
            margin-top: 20px;
            font-size: 0.9rem;
        }

        .demo-info h6 {
            color: #7c943f;
            margin-bottom: 10px;
        }

        @media (max-width: 768px) {
            .login-grid {
                grid-template-columns: 1fr;
            }

            .login-image {
                min-height: 200px;
            }

            .login-form {
                padding: 40px 30px;
            }
        }
    </style>
</head>
<body>

<a href="../index.php" class="back-home">
    <i class="fas fa-home"></i> Retour à l'accueil
</a>

<div class="login-container">
    <div class="login-grid">
        <!-- Left Side - Image and Branding -->
        <div class="login-image">
            <h2>La Beauté Bio</h2>
            <p>Découvrez notre collection de produits cosmétiques naturels et bio. Connectez-vous pour accéder à votre espace personnel et profiter d'une expérience d'achat unique.</p>
            <div style="margin-top: 30px;">
                <i class="fas fa-leaf" style="font-size: 3rem; opacity: 0.7;"></i>
            </div>
        </div>

        <!-- Right Side - Login Form -->
        <div class="login-form">
            <h1><i class="fas fa-sign-in-alt"></i> Connexion</h1>
            <p class="subtitle">Accédez à votre espace personnel</p>

            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger" style="border-radius: 15px;">
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

                <div class="form-group">
                    <label for="login"><i class="fas fa-user"></i> Email ou nom d'utilisateur</label>
                    <input type="text" class="form-control" id="login" name="login"
                           value="<?php echo htmlspecialchars($_POST['login'] ?? ''); ?>"
                           placeholder="Votre email ou nom d'utilisateur"
                           required autofocus>
                </div>

                <div class="form-group">
                    <label for="password"><i class="fas fa-lock"></i> Mot de passe</label>
                    <input type="password" class="form-control" id="password" name="password"
                           placeholder="Votre mot de passe"
                           required>
                </div>

                <div class="form-check" style="margin-bottom: 20px;">
                    <input type="checkbox" class="form-check-input" id="remember_me" name="remember_me">
                    <label class="form-check-label" for="remember_me" style="color: #666;">
                        Se souvenir de moi
                    </label>
                </div>

                <button type="submit" class="btn-login">
                    <i class="fas fa-sign-in-alt"></i> Se connecter
                </button>
            </form>

            <div class="divider">
                <span>ou</span>
            </div>

            <div class="register-link">
                <p>Pas encore de compte ?</p>
                <a href="register.php">
                    <i class="fas fa-user-plus"></i> Créer un compte
                </a>
            </div>

            <!-- Demo Info -->
            <div class="demo-info">
                <h6><i class="fas fa-info-circle"></i> Compte de démonstration</h6>
                <p style="margin: 5px 0;"><strong>Admin:</strong> admin@cosmetics.com / admin123</p>
                <p style="margin: 0;"><strong>Ou créez</strong> votre propre compte client</p>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript -->
<script src="../assets/js/cart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
