<?php
/**
 * Gestion des sessions simple pour débutants
 */

// Démarrer la session si elle n'est pas déjà démarrée
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

/**
 * Fonction simple pour vérifier si l'utilisateur est connecté
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/**
 * Fonction simple pour vérifier si l'utilisateur est admin
 */
function isAdmin() {
    return isLoggedIn() && isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

/**
 * Fonction simple pour vérifier si l'utilisateur est client
 */
function isClient() {
    return isLoggedIn() && isset($_SESSION['role']) && $_SESSION['role'] === 'client';
}

/**
 * Fonction simple pour exiger une connexion
 */
function requireLogin($redirect_to = 'login.php') {
    if (!isLoggedIn()) {
        header("Location: $redirect_to");
        exit();
    }
}

/**
 * Fonction simple pour exiger les droits admin
 */
function requireAdmin($redirect_to = '../index.php') {
    if (!isAdmin()) {
        header("Location: $redirect_to");
        exit();
    }
}

/**
 * Fonction simple pour connecter un utilisateur
 */
function loginUser($user) {
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['email'] = $user['email'];
    $_SESSION['full_name'] = $user['full_name'];
    $_SESSION['role'] = $user['role'];
}

/**
 * Fonction simple pour déconnecter un utilisateur
 */
function logoutUser() {
    session_unset();
    session_destroy();
}

/**
 * Fonction simple pour obtenir les infos de l'utilisateur actuel
 */
function getCurrentUser() {
    if (!isLoggedIn()) {
        return null;
    }

    // Retourner un tableau simple avec les infos de session
    return array(
        'id' => $_SESSION['user_id'],
        'username' => $_SESSION['username'],
        'email' => $_SESSION['email'],
        'full_name' => $_SESSION['full_name'],
        'role' => $_SESSION['role']
    );
}
?>
