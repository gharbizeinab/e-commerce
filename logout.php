<?php
/**
 * Logout Page
 * Handle user logout and session cleanup
 */

require_once 'config/session.php';

// Logout user
logoutUser();

// Redirect to homepage with success message
session_start();
$_SESSION['success_message'] = 'Vous avez été déconnecté avec succès.';
header('Location: index.php');
exit();
?>
