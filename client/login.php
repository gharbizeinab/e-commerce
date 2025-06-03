<?php
require_once '../config/database.php';
require_once '../config/session.php';
require_once '../includes/functions.php';

$errors = [];

// Traitement du formulaire de connexion
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = cleanInput($_POST['login'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($login)) {
        $errors[] = 'L\'email ou nom d\'utilisateur est requis.';
    }
    
    if (empty($password)) {
        $errors[] = 'Le mot de passe est requis.';
    }
    
    if (empty($errors)) {
        // Chercher l'utilisateur par email
        $user = getUserByEmail($login);
        
        // Si pas trouvé par email, chercher par nom d'utilisateur
        if (!$user) {
            $login_clean = cleanInput($login);
            $sql = "SELECT * FROM users WHERE username = '$login_clean'";
            $result = executeQuery($sql);
            if (mysqli_num_rows($result) > 0) {
                $user = mysqli_fetch_assoc($result);
            }
        }
        
        if ($user && verifyPassword($password, $user['password'])) {
            // Connexion réussie
            loginUser($user);
            
            // Redirection selon le rôle
            if ($user['role'] === 'admin') {
                header('Location: ../admin/index.php');
            } else {
                header('Location: ../index.php');
            }
            exit();
        } else {
            $errors[] = 'Email/nom d\'utilisateur ou mot de passe incorrect.';
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
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #7c943f, #5c7045);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .login-container {
            background: white;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            width: 100%;
            max-width: 400px;
        }
        
        h1 {
            color: #7c943f;
            text-align: center;
            margin-bottom: 30px;
            font-size: 2rem;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: bold;
        }
        
        input[type="text"],
        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 15px;
            border: 2px solid #ddd;
            border-radius: 8px;
            font-size: 16px;
            background-color: white;
            color: #333;
        }
        
        input:focus {
            border-color: #7c943f;
            outline: none;
            background-color: #f9f9f9;
        }
        
        .btn {
            width: 100%;
            padding: 15px;
            background: #7c943f;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            margin-top: 10px;
        }
        
        .btn:hover {
            background: #5d722e;
        }
        
        .error {
            background: #f8d7da;
            color: #721c24;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            border: 1px solid #f5c6cb;
        }
        
        .links {
            text-align: center;
            margin-top: 20px;
        }
        
        .links a {
            color: #7c943f;
            text-decoration: none;
            margin: 0 10px;
        }
        
        .links a:hover {
            text-decoration: underline;
        }
        

    </style>
</head>
<body>
    <div class="login-container">
        <h1>🔑 Connexion</h1>
        
        <?php if (!empty($errors)): ?>
            <div class="error">
                <strong>❌ Erreur(s) :</strong>
                <ul style="margin: 10px 0 0 20px;">
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo htmlspecialchars($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="form-group">
                <label for="login">📧 Email ou nom d'utilisateur :</label>
                <input type="text" 
                       id="login" 
                       name="login" 
                       value="<?php echo htmlspecialchars($_POST['login'] ?? ''); ?>"
                       placeholder="Votre email ou nom d'utilisateur"
                       required>
            </div>
            
            <div class="form-group">
                <label for="password">🔒 Mot de passe :</label>
                <input type="password" 
                       id="password" 
                       name="password" 
                       placeholder="Votre mot de passe"
                       required>
            </div>
            
            <button type="submit" class="btn">Se connecter</button>
        </form>

        <div style="background: #f8f9fa; padding: 15px; border-radius: 8px; margin-top: 20px; text-align: center; font-size: 14px;">
            <p style="color: #6c757d; margin: 0;">
                <i class="fas fa-info-circle"></i>
                Nouveau client ? Créez votre compte pour profiter de nos produits bio tunisiens.
            </p>
        </div>

        <div class="links">
            <a href="register.php">📝 Créer un compte</a> |
            <a href="../index.php">🏠 Retour à l'accueil</a>
        </div>
    </div>
</body>
</html>
