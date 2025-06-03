<?php
require_once 'config/session.php';
require_once 'includes/functions.php';

// Déconnecter l'utilisateur
logoutUser();

// Redémarrer une nouvelle session pour le message
session_start();
$_SESSION['success_message'] = 'Vous avez été déconnecté avec succès.';

// Redirection vers la page d'accueil
header('Location: index.php');
exit();
?>
