<?php
/**
 * Fonctions simples pour débutants
 */

/**
 * Fonction simple pour nettoyer les données
 */
function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

/**
 * Fonction simple pour valider un email
 */
function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

/**
 * Fonction simple pour hasher un mot de passe
 */
function hashPassword($password) {
    return password_hash($password, PASSWORD_DEFAULT);
}

/**
 * Fonction simple pour vérifier un mot de passe
 */
function verifyPassword($password, $hash) {
    return password_verify($password, $hash);
}

/**
 * Fonction simple pour formater les prix en TND
 */
function formatPrice($price) {
    return number_format($price, 3, ',', ' ') . ' TND';
}

/**
 * Fonction simple pour obtenir un utilisateur par email
 */
function getUserByEmail($email) {
    global $connection;
    $email = cleanInput($email);
    $sql = "SELECT * FROM users WHERE email = '$email'";
    $result = executeQuery($sql);

    if (mysqli_num_rows($result) > 0) {
        return mysqli_fetch_assoc($result);
    }
    return null;
}

/**
 * Fonction simple pour créer un nouvel utilisateur
 */
function createUser($data) {
    global $connection;

    $username = cleanInput($data['username']);
    $email = cleanInput($data['email']);
    $password = cleanInput($data['password']);
    $full_name = cleanInput($data['full_name']);
    $phone = isset($data['phone']) ? cleanInput($data['phone']) : '';
    $address = isset($data['address']) ? cleanInput($data['address']) : '';
    $role = isset($data['role']) ? cleanInput($data['role']) : 'client';

    $sql = "INSERT INTO users (username, email, password, full_name, phone, address, role)
            VALUES ('$username', '$email', '$password', '$full_name', '$phone', '$address', '$role')";

    $result = mysqli_query($connection, $sql);
    return $result;
}

/**
 * Fonction simple pour obtenir toutes les catégories
 */
function getCategories() {
    global $connection;
    $sql = "SELECT * FROM categories ORDER BY name";
    $result = executeQuery($sql);

    $categories = array();
    while ($row = mysqli_fetch_assoc($result)) {
        $categories[] = $row;
    }
    return $categories;
}

/**
 * Fonction simple pour obtenir les produits par catégorie
 */
function getProductsByCategory($category_id = null) {
    global $connection;

    if ($category_id) {
        $category_id = cleanInput($category_id);
        $sql = "SELECT p.*, c.name as category_name FROM products p
                LEFT JOIN categories c ON p.category_id = c.id
                WHERE p.category_id = '$category_id' AND p.is_active = 1
                ORDER BY p.name";
    } else {
        $sql = "SELECT p.*, c.name as category_name FROM products p
                LEFT JOIN categories c ON p.category_id = c.id
                WHERE p.is_active = 1
                ORDER BY p.name";
    }

    $result = executeQuery($sql);
    $products = array();
    while ($row = mysqli_fetch_assoc($result)) {
        $products[] = $row;
    }
    return $products;
}

/**
 * Fonction simple pour obtenir les produits en vedette
 */
function getFeaturedProducts($limit = 6) {
    global $connection;
    $limit = (int)$limit;
    $sql = "SELECT p.*, c.name as category_name FROM products p
            LEFT JOIN categories c ON p.category_id = c.id
            WHERE p.is_featured = 1 AND p.is_active = 1
            ORDER BY p.created_at DESC
            LIMIT $limit";

    $result = executeQuery($sql);
    $products = array();
    while ($row = mysqli_fetch_assoc($result)) {
        $products[] = $row;
    }
    return $products;
}

/**
 * Fonction simple pour obtenir un produit par ID
 */
function getProductById($id) {
    global $connection;
    $id = cleanInput($id);
    $sql = "SELECT p.*, c.name as category_name FROM products p
            LEFT JOIN categories c ON p.category_id = c.id
            WHERE p.id = '$id'";
    $result = executeQuery($sql);

    if (mysqli_num_rows($result) > 0) {
        return mysqli_fetch_assoc($result);
    }
    return null;
}

/**
 * Fonction simple pour ajouter un produit
 */
function addProduct($data) {
    global $connection;

    $name = cleanInput($data['name']);
    $description = cleanInput($data['description']);
    $price = cleanInput($data['price']);
    $category_id = cleanInput($data['category_id']);
    $image = cleanInput($data['image']);
    $stock_quantity = cleanInput($data['stock_quantity']);
    $characteristics = isset($data['characteristics']) ? cleanInput($data['characteristics']) : '';

    $sql = "INSERT INTO products (name, description, price, category_id, image, stock_quantity, characteristics, is_active, is_featured)
            VALUES ('$name', '$description', '$price', '$category_id', '$image', '$stock_quantity', '$characteristics', 1, 0)";

    $result = mysqli_query($connection, $sql);
    return $result;
}

/**
 * Fonction simple pour mettre à jour un produit
 */
function updateProduct($product_id, $data) {
    global $connection;

    $product_id = cleanInput($product_id);
    $name = cleanInput($data['name']);
    $description = cleanInput($data['description']);
    $price = cleanInput($data['price']);
    $category_id = cleanInput($data['category_id']);
    $image = cleanInput($data['image']);
    $stock_quantity = cleanInput($data['stock_quantity']);
    $characteristics = isset($data['characteristics']) ? cleanInput($data['characteristics']) : '';

    $sql = "UPDATE products SET
            name = '$name',
            description = '$description',
            price = '$price',
            category_id = '$category_id',
            image = '$image',
            stock_quantity = '$stock_quantity',
            characteristics = '$characteristics',
            updated_at = NOW()
            WHERE id = '$product_id'";

    $result = mysqli_query($connection, $sql);
    return $result;
}

/**
 * Fonction simple pour supprimer un produit
 */
function deleteProduct($product_id) {
    global $connection;
    $product_id = cleanInput($product_id);
    $sql = "DELETE FROM products WHERE id = '$product_id'";
    $result = mysqli_query($connection, $sql);
    return $result;
}

?>
