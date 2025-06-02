<?php
/**
 * Configuration de base de données simple pour débutants
 */

// Paramètres de connexion
$host = "localhost";
$username = "root";
$password = "";
$database = "cosmetics_ecommerce";

// Connexion à la base de données avec mysqli (simple pour débutants)
$connection = mysqli_connect($host, $username, $password, $database);

// Vérifier la connexion
if (!$connection) {
    die("Erreur de connexion : " . mysqli_connect_error());
}

// Définir l'encodage
mysqli_set_charset($connection, "utf8");

/**
 * Fonction simple pour nettoyer les données (sécurité de base)
 */
function cleanInput($data) {
    global $connection;
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    $data = mysqli_real_escape_string($connection, $data);
    return $data;
}

/**
 * Fonction simple pour exécuter une requête
 */
function executeQuery($sql) {
    global $connection;
    $result = mysqli_query($connection, $sql);
    if (!$result) {
        die("Erreur SQL : " . mysqli_error($connection));
    }
    return $result;
}
?>
