<?php
/**
 * Menu utilisateur standardisé
 * À inclure sur toutes les pages publiques
 */
?>

<!-- User Menu -->
<div class="user-menu">
    <?php if (isLoggedIn()): ?>
        <a href="<?php echo isset($client_path) ? $client_path : 'client/'; ?>profile.php">
            <i class="fas fa-user"></i> Mon Profil
        </a>
        <a href="<?php echo isset($home_path) ? $home_path : ''; ?>logout.php">
            <i class="fas fa-sign-out-alt"></i> Déconnexion
        </a>
    <?php else: ?>
        <a href="<?php echo isset($client_path) ? $client_path : 'client/'; ?>login.php">
            <i class="fas fa-sign-in-alt"></i> Connexion
        </a>
        <a href="<?php echo isset($client_path) ? $client_path : 'client/'; ?>register.php">
            <i class="fas fa-user-plus"></i> Inscription
        </a>
    <?php endif; ?>
</div>

<style>
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

@media (max-width: 768px) {
    .user-menu {
        position: relative;
        top: auto;
        right: auto;
        text-align: center;
        margin-bottom: 20px;
    }
    
    .user-menu a {
        margin: 5px;
        display: inline-block;
    }
}
</style>
