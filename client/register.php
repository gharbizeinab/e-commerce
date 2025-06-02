<?php
require_once '../config/database.php';
require_once '../config/session.php';
require_once '../includes/functions.php';

$errors = [];
$success = false;

// Traitement du formulaire d'inscription
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = cleanInput($_POST['username'] ?? '');
    $email = cleanInput($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $full_name = cleanInput($_POST['full_name'] ?? '');
    $phone = cleanInput($_POST['phone'] ?? '');
    $address = cleanInput($_POST['address'] ?? '');
    
    // Validation
    if (empty($username)) {
        $errors[] = 'Le nom d\'utilisateur est requis.';
    } elseif (strlen($username) < 3) {
        $errors[] = 'Le nom d\'utilisateur doit contenir au moins 3 caractères.';
    }
    
    if (empty($email)) {
        $errors[] = 'L\'email est requis.';
    } elseif (!isValidEmail($email)) {
        $errors[] = 'L\'email n\'est pas valide.';
    }
    
    if (empty($full_name)) {
        $errors[] = 'Le nom complet est requis.';
    }
    
    if (empty($password)) {
        $errors[] = 'Le mot de passe est requis.';
    } elseif (strlen($password) < 6) {
        $errors[] = 'Le mot de passe doit contenir au moins 6 caractères.';
    }
    
    if ($password !== $confirm_password) {
        $errors[] = 'Les mots de passe ne correspondent pas.';
    }
    
    // Vérifier si l'email existe déjà
    if (empty($errors)) {
        $existing_user = getUserByEmail($email);
        if ($existing_user) {
            $errors[] = 'Cet email est déjà utilisé.';
        }
        
        // Vérifier si le nom d'utilisateur existe déjà
        $username_clean = cleanInput($username);
        $sql = "SELECT id FROM users WHERE username = '$username_clean'";
        $result = executeQuery($sql);
        if (mysqli_num_rows($result) > 0) {
            $errors[] = 'Ce nom d\'utilisateur est déjà utilisé.';
        }
    }
    
    // Créer le compte si pas d'erreurs
    if (empty($errors)) {
        $hashed_password = hashPassword($password);
        
        $user_data = [
            'username' => $username,
            'email' => $email,
            'password' => $hashed_password,
            'full_name' => $full_name,
            'phone' => $phone,
            'address' => $address,
            'role' => 'client'
        ];
        
        if (createUser($user_data)) {
            $success = true;
        } else {
            $errors[] = 'Erreur lors de la création du compte. Veuillez réessayer.';
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
            padding: 20px;
        }
        
        .register-container {
            background: white;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            width: 100%;
            max-width: 600px;
            margin: 0 auto;
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
        
        .form-row {
            display: flex;
            gap: 15px;
        }
        
        .form-row .form-group {
            flex: 1;
        }
        
        label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: bold;
        }
        
        input[type="text"],
        input[type="email"],
        input[type="password"],
        input[type="tel"],
        textarea {
            width: 100%;
            padding: 15px;
            border: 2px solid #ddd;
            border-radius: 8px;
            font-size: 16px;
            background-color: white;
            color: #333;
        }
        
        textarea {
            resize: vertical;
            min-height: 80px;
        }
        
        input:focus,
        textarea:focus {
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
        
        .success {
            background: #d1e7dd;
            color: #0f5132;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            border: 1px solid #badbcc;
            text-align: center;
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
        
        @media (max-width: 768px) {
            .form-row {
                flex-direction: column;
                gap: 0;
            }
        }
    </style>
</head>
<body>
    <div class="register-container">
        <h1>📝 Créer un compte</h1>
        
        <?php if ($success): ?>
            <div class="success">
                <strong>✅ Inscription réussie !</strong><br>
                Votre compte a été créé avec succès. Vous pouvez maintenant vous connecter.
            </div>
            <div class="links">
                <a href="login.php">🔑 Se connecter maintenant</a>
            </div>
        <?php else: ?>
            
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
                <div class="form-row">
                    <div class="form-group">
                        <label for="username">👤 Nom d'utilisateur * :</label>
                        <input type="text" 
                               id="username" 
                               name="username" 
                               value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>"
                               placeholder="Votre nom d'utilisateur"
                               required>
                    </div>
                    
                    <div class="form-group">
                        <label for="email">📧 Email * :</label>
                        <input type="email" 
                               id="email" 
                               name="email" 
                               value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>"
                               placeholder="votre@email.com"
                               required>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="full_name">🆔 Nom complet * :</label>
                    <input type="text" 
                           id="full_name" 
                           name="full_name" 
                           value="<?php echo htmlspecialchars($_POST['full_name'] ?? ''); ?>"
                           placeholder="Votre nom complet"
                           required>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="password">🔒 Mot de passe * :</label>
                        <input type="password" 
                               id="password" 
                               name="password" 
                               placeholder="Minimum 6 caractères"
                               required>
                    </div>
                    
                    <div class="form-group">
                        <label for="confirm_password">🔒 Confirmer mot de passe * :</label>
                        <input type="password" 
                               id="confirm_password" 
                               name="confirm_password" 
                               placeholder="Répétez votre mot de passe"
                               required>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="phone">📞 Téléphone :</label>
                        <input type="tel" 
                               id="phone" 
                               name="phone" 
                               value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>"
                               placeholder="+216 XX XXX XXX">
                    </div>
                    
                    <div class="form-group">
                        <label for="address">📍 Adresse :</label>
                        <textarea id="address" 
                                  name="address" 
                                  placeholder="Votre adresse complète"><?php echo htmlspecialchars($_POST['address'] ?? ''); ?></textarea>
                    </div>
                </div>
                
                <button type="submit" class="btn">Créer mon compte</button>
            </form>
            
            <div class="links">
                <a href="login.php">🔑 Déjà un compte ? Se connecter</a> |
                <a href="../index.php">🏠 Retour à l'accueil</a>
            </div>
            
        <?php endif; ?>
    </div>
</body>
</html>
