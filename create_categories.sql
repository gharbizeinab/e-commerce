-- Création de la table categories
CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    image VARCHAR(255),
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Insertion de catégories par défaut
INSERT INTO categories (name, description, is_active) VALUES
('Savons Bio', 'Savons naturels et biologiques fabriqués en Tunisie', 1),
('Huiles Essentielles', 'Huiles essentielles pures et naturelles', 1),
('Crèmes et Soins', 'Crèmes hydratantes et soins du visage', 1),
('Parfums Naturels', 'Parfums à base d\'ingrédients naturels', 1),
('Produits Capillaires', 'Shampoings et soins pour cheveux bio', 1);

-- Mise à jour de la table products pour ajouter les colonnes manquantes
ALTER TABLE products 
ADD COLUMN IF NOT EXISTS is_active TINYINT(1) DEFAULT 1,
ADD COLUMN IF NOT EXISTS is_featured TINYINT(1) DEFAULT 0,
ADD COLUMN IF NOT EXISTS characteristics TEXT;

-- Mise à jour des produits existants pour les rendre actifs
UPDATE products SET is_active = 1 WHERE is_active IS NULL;
UPDATE products SET is_featured = 0 WHERE is_featured IS NULL;
