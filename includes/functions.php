<?php
/**
 * Utility Functions
 * Common functions used throughout the application
 */

/**
 * Sanitize input data
 */
function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

/**
 * Validate email format
 */
function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

/**
 * Hash password
 */
function hashPassword($password) {
    return password_hash($password, PASSWORD_DEFAULT);
}

/**
 * Verify password
 */
function verifyPassword($password, $hash) {
    return password_verify($password, $hash);
}

/**
 * Format price for display
 */
function formatPrice($price) {
    return number_format($price, 2, ',', ' ') . ' €';
}

/**
 * Get all categories
 */
function getCategories($pdo) {
    $stmt = $pdo->query("SELECT * FROM categories ORDER BY name");
    return $stmt->fetchAll();
}

/**
 * Get products by category
 */
function getProductsByCategory($pdo, $category_id = null) {
    if ($category_id) {
        $stmt = $pdo->prepare("SELECT p.*, c.name as category_name FROM products p 
                              LEFT JOIN categories c ON p.category_id = c.id 
                              WHERE p.category_id = ? ORDER BY p.name");
        $stmt->execute([$category_id]);
    } else {
        $stmt = $pdo->query("SELECT p.*, c.name as category_name FROM products p 
                            LEFT JOIN categories c ON p.category_id = c.id 
                            ORDER BY p.name");
    }
    return $stmt->fetchAll();
}

/**
 * Get single product by ID
 */
function getProductById($pdo, $id) {
    $stmt = $pdo->prepare("SELECT p.*, c.name as category_name FROM products p 
                          LEFT JOIN categories c ON p.category_id = c.id 
                          WHERE p.id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch();
}

/**
 * Get user by email
 */
function getUserByEmail($pdo, $email) {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    return $stmt->fetch();
}

/**
 * Get user by username
 */
function getUserByUsername($pdo, $username) {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    return $stmt->fetch();
}

/**
 * Create new user
 */
function createUser($pdo, $data) {
    $stmt = $pdo->prepare("INSERT INTO users (username, email, password, full_name, phone, address, role) 
                          VALUES (?, ?, ?, ?, ?, ?, ?)");
    return $stmt->execute([
        $data['username'],
        $data['email'],
        $data['password'],
        $data['full_name'],
        $data['phone'] ?? null,
        $data['address'] ?? null,
        $data['role'] ?? 'client'
    ]);
}

/**
 * Update user profile
 */
function updateUser($pdo, $user_id, $data) {
    $stmt = $pdo->prepare("UPDATE users SET full_name = ?, phone = ?, address = ?, updated_at = NOW() 
                          WHERE id = ?");
    return $stmt->execute([
        $data['full_name'],
        $data['phone'],
        $data['address'],
        $user_id
    ]);
}

/**
 * Add new product
 */
function addProduct($pdo, $data) {
    $stmt = $pdo->prepare("INSERT INTO products (name, description, price, category_id, image, stock_quantity, characteristics) 
                          VALUES (?, ?, ?, ?, ?, ?, ?)");
    return $stmt->execute([
        $data['name'],
        $data['description'],
        $data['price'],
        $data['category_id'],
        $data['image'],
        $data['stock_quantity'],
        $data['characteristics']
    ]);
}

/**
 * Update product
 */
function updateProduct($pdo, $product_id, $data) {
    $stmt = $pdo->prepare("UPDATE products SET name = ?, description = ?, price = ?, category_id = ?, 
                          image = ?, stock_quantity = ?, characteristics = ?, updated_at = NOW() 
                          WHERE id = ?");
    return $stmt->execute([
        $data['name'],
        $data['description'],
        $data['price'],
        $data['category_id'],
        $data['image'],
        $data['stock_quantity'],
        $data['characteristics'],
        $product_id
    ]);
}

/**
 * Delete product
 */
function deleteProduct($pdo, $product_id) {
    $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
    return $stmt->execute([$product_id]);
}

/**
 * Upload image file
 */
function uploadImage($file, $upload_dir = 'assets/images/') {
    if (!isset($file['error']) || is_array($file['error'])) {
        return false;
    }

    // Check upload errors
    switch ($file['error']) {
        case UPLOAD_ERR_OK:
            break;
        case UPLOAD_ERR_NO_FILE:
            return false;
        case UPLOAD_ERR_INI_SIZE:
        case UPLOAD_ERR_FORM_SIZE:
            return false;
        default:
            return false;
    }

    // Check file size (max 5MB)
    if ($file['size'] > 5000000) {
        return false;
    }

    // Check file type
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
    if (!in_array($file['type'], $allowed_types)) {
        return false;
    }

    // Generate unique filename
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = uniqid() . '.' . $extension;
    $filepath = $upload_dir . $filename;

    // Create directory if it doesn't exist
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }

    // Move uploaded file
    if (move_uploaded_file($file['tmp_name'], $filepath)) {
        return $filename;
    }

    return false;
}
?>
