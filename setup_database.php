<?php
require_once 'config/database.php';

echo "<h2>Configuration de la base de données</h2>";

// Création de la table categories
$sql = "CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    image VARCHAR(255),
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)";

if (mysqli_query($connection, $sql)) {
    echo "<p>✅ Table categories créée avec succès</p>";
} else {
    echo "<p>❌ Erreur création table categories: " . mysqli_error($connection) . "</p>";
}

// Vérification si des catégories existent déjà
$result = executeQuery("SELECT COUNT(*) as count FROM categories");
$row = mysqli_fetch_assoc($result);

if ($row['count'] == 0) {
    // Insertion de catégories par défaut
    $categories = [
        ['Savons Bio', 'Savons naturels et biologiques fabriqués en Tunisie'],
        ['Huiles Essentielles', 'Huiles essentielles pures et naturelles'],
        ['Crèmes et Soins', 'Crèmes hydratantes et soins du visage'],
        ['Parfums Naturels', 'Parfums à base d\'ingrédients naturels'],
        ['Produits Capillaires', 'Shampoings et soins pour cheveux bio']
    ];

    foreach ($categories as $cat) {
        $name = cleanInput($cat[0]);
        $description = cleanInput($cat[1]);
        $sql = "INSERT INTO categories (name, description, is_active) VALUES ('$name', '$description', 1)";
        
        if (mysqli_query($connection, $sql)) {
            echo "<p>✅ Catégorie '$name' ajoutée</p>";
        } else {
            echo "<p>❌ Erreur ajout catégorie '$name': " . mysqli_error($connection) . "</p>";
        }
    }
} else {
    echo "<p>ℹ️ Catégories déjà existantes (" . $row['count'] . " trouvées)</p>";
}

// Ajout des colonnes manquantes à la table products
$columns_to_add = [
    'is_active' => 'TINYINT(1) DEFAULT 1',
    'is_featured' => 'TINYINT(1) DEFAULT 0',
    'characteristics' => 'TEXT'
];

foreach ($columns_to_add as $column => $definition) {
    $sql = "SHOW COLUMNS FROM products LIKE '$column'";
    $result = mysqli_query($connection, $sql);
    
    if (mysqli_num_rows($result) == 0) {
        $sql = "ALTER TABLE products ADD COLUMN $column $definition";
        if (mysqli_query($connection, $sql)) {
            echo "<p>✅ Colonne '$column' ajoutée à la table products</p>";
        } else {
            echo "<p>❌ Erreur ajout colonne '$column': " . mysqli_error($connection) . "</p>";
        }
    } else {
        echo "<p>ℹ️ Colonne '$column' existe déjà</p>";
    }
}

// Mise à jour des produits existants
$sql = "UPDATE products SET is_active = 1 WHERE is_active IS NULL";
mysqli_query($connection, $sql);

$sql = "UPDATE products SET is_featured = 0 WHERE is_featured IS NULL";
mysqli_query($connection, $sql);

echo "<p>✅ Produits existants mis à jour</p>";

// Vérification des tables
echo "<h3>Vérification des tables :</h3>";

$tables = ['users', 'products', 'categories', 'orders'];
foreach ($tables as $table) {
    $result = executeQuery("SELECT COUNT(*) as count FROM $table");
    $row = mysqli_fetch_assoc($result);
    echo "<p>📊 Table '$table': " . $row['count'] . " enregistrements</p>";
}

echo "<h3>✅ Configuration terminée !</h3>";
echo "<p><a href='admin/simple.php'>Aller à l'administration</a></p>";
echo "<p><a href='index.php'>Retour à l'accueil</a></p>";
?>
