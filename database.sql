-- =====================================================
-- DATABASE SCHEMA FOR COSMETICS E-COMMERCE PROJECT
-- =====================================================
-- Project: Cosmétiques Élégance
-- Description: Complete e-commerce solution for cosmetics products
-- Author: Generated for PHP E-commerce Project
-- Date: 2024
-- =====================================================

-- Create database
CREATE DATABASE IF NOT EXISTS cosmetics_ecommerce CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE cosmetics_ecommerce;

-- =====================================================
-- USERS TABLE
-- =====================================================
-- Stores both clients and administrators
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    phone VARCHAR(20),
    address TEXT,
    role ENUM('client', 'admin') DEFAULT 'client',
    is_active BOOLEAN DEFAULT TRUE,
    email_verified BOOLEAN DEFAULT FALSE,
    last_login TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_email (email),
    INDEX idx_username (username),
    INDEX idx_role (role),
    INDEX idx_created_at (created_at)
);

-- =====================================================
-- CATEGORIES TABLE
-- =====================================================
-- Product categories (Savons, Parfums, etc.)
CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) UNIQUE NOT NULL,
    description TEXT,
    image VARCHAR(255),
    is_active BOOLEAN DEFAULT TRUE,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_slug (slug),
    INDEX idx_active (is_active),
    INDEX idx_sort_order (sort_order)
);

-- =====================================================
-- PRODUCTS TABLE
-- =====================================================
-- Main products table with all product information
CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(200) NOT NULL,
    slug VARCHAR(200) UNIQUE NOT NULL,
    description TEXT,
    short_description VARCHAR(500),
    price DECIMAL(10,2) NOT NULL,
    sale_price DECIMAL(10,2) NULL,
    category_id INT,
    image VARCHAR(255),
    gallery TEXT, -- JSON array of additional images
    stock_quantity INT DEFAULT 0,
    min_stock_level INT DEFAULT 5,
    characteristics TEXT,
    ingredients TEXT,
    usage_instructions TEXT,
    weight DECIMAL(8,2), -- in grams
    dimensions VARCHAR(50), -- LxWxH in cm
    sku VARCHAR(50) UNIQUE,
    barcode VARCHAR(50),
    is_active BOOLEAN DEFAULT TRUE,
    is_featured BOOLEAN DEFAULT FALSE,
    meta_title VARCHAR(200),
    meta_description VARCHAR(500),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL,
    INDEX idx_slug (slug),
    INDEX idx_category (category_id),
    INDEX idx_price (price),
    INDEX idx_stock (stock_quantity),
    INDEX idx_active (is_active),
    INDEX idx_featured (is_featured),
    INDEX idx_sku (sku),
    INDEX idx_created_at (created_at)
);

-- =====================================================
-- PRODUCT IMAGES TABLE
-- =====================================================
-- Additional images for products
CREATE TABLE product_images (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    image_path VARCHAR(255) NOT NULL,
    alt_text VARCHAR(200),
    sort_order INT DEFAULT 0,
    is_primary BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    INDEX idx_product (product_id),
    INDEX idx_primary (is_primary),
    INDEX idx_sort_order (sort_order)
);

-- =====================================================
-- ORDERS TABLE
-- =====================================================
-- Customer orders
CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_number VARCHAR(50) UNIQUE NOT NULL,
    user_id INT NOT NULL,
    status ENUM('pending', 'confirmed', 'processing', 'shipped', 'delivered', 'cancelled', 'refunded') DEFAULT 'pending',
    total_amount DECIMAL(10,2) NOT NULL,
    subtotal DECIMAL(10,2) NOT NULL,
    tax_amount DECIMAL(10,2) DEFAULT 0,
    shipping_amount DECIMAL(10,2) DEFAULT 0,
    discount_amount DECIMAL(10,2) DEFAULT 0,

    -- Billing information
    billing_first_name VARCHAR(50) NOT NULL,
    billing_last_name VARCHAR(50) NOT NULL,
    billing_email VARCHAR(100) NOT NULL,
    billing_phone VARCHAR(20),
    billing_address TEXT NOT NULL,
    billing_city VARCHAR(50) NOT NULL,
    billing_postal_code VARCHAR(20) NOT NULL,
    billing_country VARCHAR(50) NOT NULL,

    -- Shipping information
    shipping_first_name VARCHAR(50),
    shipping_last_name VARCHAR(50),
    shipping_address TEXT,
    shipping_city VARCHAR(50),
    shipping_postal_code VARCHAR(20),
    shipping_country VARCHAR(50),

    -- Order tracking
    payment_method VARCHAR(50),
    payment_status ENUM('pending', 'paid', 'failed', 'refunded') DEFAULT 'pending',
    shipping_method VARCHAR(50),
    tracking_number VARCHAR(100),
    notes TEXT,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    shipped_at TIMESTAMP NULL,
    delivered_at TIMESTAMP NULL,

    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_order_number (order_number),
    INDEX idx_user (user_id),
    INDEX idx_status (status),
    INDEX idx_payment_status (payment_status),
    INDEX idx_created_at (created_at)
);

-- =====================================================
-- ORDER ITEMS TABLE
-- =====================================================
-- Items within each order
CREATE TABLE order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    product_name VARCHAR(200) NOT NULL, -- Store name at time of order
    product_sku VARCHAR(50),
    quantity INT NOT NULL,
    unit_price DECIMAL(10,2) NOT NULL,
    total_price DECIMAL(10,2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE RESTRICT,
    INDEX idx_order (order_id),
    INDEX idx_product (product_id)
);

-- =====================================================
-- SHOPPING CART TABLE
-- =====================================================
-- Persistent shopping cart for logged-in users
CREATE TABLE cart_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    UNIQUE KEY unique_user_product (user_id, product_id),
    INDEX idx_user (user_id)
);

-- =====================================================
-- WISHLIST TABLE
-- =====================================================
-- User wishlist/favorites
CREATE TABLE wishlist (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    UNIQUE KEY unique_user_product (user_id, product_id),
    INDEX idx_user (user_id)
);

-- =====================================================
-- COUPONS TABLE
-- =====================================================
-- Discount coupons
CREATE TABLE coupons (
    id INT AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(50) UNIQUE NOT NULL,
    description VARCHAR(200),
    type ENUM('percentage', 'fixed_amount') NOT NULL,
    value DECIMAL(10,2) NOT NULL,
    minimum_amount DECIMAL(10,2) DEFAULT 0,
    maximum_discount DECIMAL(10,2) NULL,
    usage_limit INT NULL,
    used_count INT DEFAULT 0,
    is_active BOOLEAN DEFAULT TRUE,
    starts_at TIMESTAMP NULL,
    expires_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    INDEX idx_code (code),
    INDEX idx_active (is_active),
    INDEX idx_expires (expires_at)
);

-- =====================================================
-- REVIEWS TABLE
-- =====================================================
-- Product reviews and ratings
CREATE TABLE reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    user_id INT NOT NULL,
    rating INT NOT NULL CHECK (rating >= 1 AND rating <= 5),
    title VARCHAR(200),
    comment TEXT,
    is_approved BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_user_product (user_id, product_id),
    INDEX idx_product (product_id),
    INDEX idx_rating (rating),
    INDEX idx_approved (is_approved)
);

-- =====================================================
-- NEWSLETTER SUBSCRIBERS TABLE
-- =====================================================
-- Email newsletter subscriptions
CREATE TABLE newsletter_subscribers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(100) UNIQUE NOT NULL,
    name VARCHAR(100),
    is_active BOOLEAN DEFAULT TRUE,
    subscribed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    unsubscribed_at TIMESTAMP NULL,

    INDEX idx_email (email),
    INDEX idx_active (is_active)
);

-- =====================================================
-- CONTACT MESSAGES TABLE
-- =====================================================
-- Contact form submissions
CREATE TABLE contact_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    subject VARCHAR(200) NOT NULL,
    message TEXT NOT NULL,
    is_read BOOLEAN DEFAULT FALSE,
    replied_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    INDEX idx_read (is_read),
    INDEX idx_created_at (created_at)
);

-- =====================================================
-- SETTINGS TABLE
-- =====================================================
-- Site configuration settings
CREATE TABLE settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100) UNIQUE NOT NULL,
    setting_value TEXT,
    setting_type ENUM('string', 'number', 'boolean', 'json') DEFAULT 'string',
    description VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_key (setting_key)
);

-- =====================================================
-- SAMPLE DATA INSERTION
-- =====================================================

-- Insert default admin user (password: admin123)
-- Password hash for 'admin123': $2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi
INSERT INTO users (username, email, password, full_name, role, is_active, email_verified) VALUES
('admin', 'admin@cosmetics.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrator', 'admin', TRUE, TRUE);

-- Insert sample client users
INSERT INTO users (username, email, password, full_name, phone, address, role, is_active, email_verified) VALUES
('marie_dupont', 'marie.dupont@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Marie Dupont', '0123456789', '123 Rue de la Paix, 75001 Paris', 'client', TRUE, TRUE),
('jean_martin', 'jean.martin@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Jean Martin', '0987654321', '456 Avenue des Champs, 69000 Lyon', 'client', TRUE, TRUE),
('sophie_bernard', 'sophie.bernard@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Sophie Bernard', '0147258369', '789 Boulevard Saint-Germain, 33000 Bordeaux', 'client', TRUE, FALSE);

-- Insert product categories
INSERT INTO categories (name, slug, description, is_active, sort_order) VALUES
('Savons', 'savons', 'Savons naturels et parfumés pour tous types de peau. Fabriqués artisanalement avec des ingrédients biologiques.', TRUE, 1),
('Parfums', 'parfums', 'Parfums et eaux de toilette pour homme et femme. Fragrances exclusives et raffinées.', TRUE, 2),
('Body Splash', 'body-splash', 'Brumes corporelles rafraîchissantes aux senteurs exotiques et florales.', TRUE, 3),
('Crèmes', 'cremes', 'Crèmes hydratantes et soins du visage pour tous types de peau.', TRUE, 4),
('Maquillage', 'maquillage', 'Produits de maquillage et cosmétiques pour sublimer votre beauté naturelle.', TRUE, 5),
('Soins Capillaires', 'soins-capillaires', 'Shampoings, après-shampoings et masques pour cheveux sains et brillants.', TRUE, 6);

-- Insert sample products
INSERT INTO products (name, slug, description, short_description, price, sale_price, category_id, image, stock_quantity, min_stock_level, characteristics, ingredients, usage_instructions, weight, sku, is_active, is_featured) VALUES

-- Savons
('Savon à la Rose Bio', 'savon-rose-bio', 'Savon artisanal à la rose, hydratant et parfumé. Fabriqué avec des pétales de rose biologiques et des huiles essentielles naturelles. Ce savon délicat nettoie en douceur tout en laissant un parfum floral subtil sur votre peau.', 'Savon artisanal à la rose, hydratant et parfumé', 45.500, 38.500, 1, 'savon_rose.jpg', 50, 10, 'Ingrédients 100% naturels, sans parabènes, sans sulfates, convient à tous types de peau, pH neutre', 'Huile d\'olive bio, huile de coco, beurre de karité, pétales de rose bio, huile essentielle de rose, glycérine végétale', 'Faire mousser entre les mains humides et appliquer sur peau mouillée. Rincer abondamment à l\'eau claire.', 100.00, 'SAV-ROSE-001', TRUE, TRUE),

('Savon au Miel et Avoine', 'savon-miel-avoine', 'Savon exfoliant doux au miel et à l\'avoine. Parfait pour éliminer les cellules mortes tout en nourrissant la peau. Le miel apporte ses propriétés antibactériennes naturelles.', 'Savon exfoliant doux au miel et à l\'avoine', 38.500, NULL, 1, 'savon_miel.jpg', 35, 10, 'Exfoliant naturel, propriétés antibactériennes, hydratant, convient aux peaux sensibles', 'Miel bio, flocons d\'avoine, huile d\'amande douce, cire d\'abeille, huile essentielle de lavande', 'Masser délicatement sur peau humide en mouvements circulaires. Rincer à l\'eau tiède.', 110.00, 'SAV-MIEL-002', TRUE, FALSE),

-- Parfums
('Parfum Élégance Florale', 'parfum-elegance-florale', 'Parfum floral pour femme aux notes de jasmin, vanille et bergamote. Une fragrance sophistiquée qui révèle votre féminité avec élégance. Tenue longue durée garantie.', 'Parfum floral sophistiqué aux notes de jasmin et vanille', 255.500, 225.500, 2, 'parfum_elegance.jpg', 25, 5, 'Eau de parfum 50ml, tenue 8-10h, notes florales et vanillées, flacon rechargeable', 'Alcool, parfum, aqua, benzyl salicylate, linalool, limonene, citronellol', 'Vaporiser sur les points de pulsation : poignets, cou, derrière les oreilles.', 50.00, 'PAR-ELEG-001', TRUE, TRUE),

('Eau de Toilette Homme Intense', 'edt-homme-intense', 'Eau de toilette masculine aux notes boisées et épicées. Un parfum moderne et viril qui accompagne l\'homme d\'aujourd\'hui dans tous ses défis.', 'Eau de toilette masculine aux notes boisées', 185.000, NULL, 2, 'parfum_homme.jpg', 18, 5, 'Eau de toilette 75ml, notes boisées et épicées, tenue 6-8h', 'Alcool, parfum, aqua, coumarin, eugenol, geraniol', 'Vaporiser à 15cm de la peau sur le torse et le cou.', 75.00, 'PAR-HOMM-002', TRUE, FALSE),

-- Body Splash
('Body Splash Tropical Paradise', 'body-splash-tropical', 'Brume corporelle aux fruits tropicaux : mangue, ananas et fruit de la passion. Une explosion de fraîcheur qui vous transporte sous les tropiques.', 'Brume corporelle aux fruits tropicaux rafraîchissante', 72.500, 62.500, 3, 'body_splash_tropical.jpg', 40, 8, 'Formule rafraîchissante, parfum fruité longue durée, 200ml, vaporisateur fin', 'Aqua, alcohol denat, parfum, glycerin, aloe barbadensis leaf juice', 'Vaporiser généreusement sur tout le corps après la douche.', 200.00, 'BSP-TROP-001', TRUE, TRUE),

-- Crèmes
('Crème Hydratante Aloe Vera', 'creme-aloe-vera', 'Crème visage à l\'aloe vera pour peaux sensibles et déshydratées. Formule ultra-douce qui apaise, hydrate et protège votre peau au quotidien.', 'Crème visage apaisante à l\'aloe vera pour peaux sensibles', 91.000, NULL, 4, 'creme_aloe.jpg', 30, 8, 'Aloe vera bio 95%, sans alcool, texture légère non grasse, 50ml, tous types de peau', 'Aloe barbadensis leaf juice, aqua, glycerin, cetearyl alcohol, shea butter', 'Appliquer matin et soir sur visage propre en massant délicatement.', 50.00, 'CRE-ALOE-001', TRUE, FALSE),

('Crème Anti-Âge Régénérante', 'creme-anti-age', 'Crème anti-âge enrichie en collagène et acide hyaluronique. Réduit visiblement les rides et redonne fermeté et éclat à votre peau.', 'Crème anti-âge au collagène et acide hyaluronique', 167.500, 142.000, 4, 'creme_antiage.jpg', 22, 5, 'Collagène marin, acide hyaluronique, vitamines A et E, action anti-rides', 'Aqua, collagen, hyaluronic acid, retinol, vitamin E, peptides', 'Appliquer le soir sur visage et cou nettoyés en évitant le contour des yeux.', 50.00, 'CRE-ANTI-002', TRUE, TRUE),

-- Maquillage
('Rouge à Lèvres Mat Longue Tenue', 'rouge-levres-mat', 'Rouge à lèvres mat longue tenue couleur rouge passion. Formule enrichie en vitamines qui nourrit vos lèvres tout en offrant une couleur intense.', 'Rouge à lèvres mat longue tenue couleur intense', 53.250, NULL, 5, 'rouge_levres.jpg', 60, 15, 'Formule mate, résistant à l\'eau, couleur intense 12h, enrichi vitamines E', 'Cires végétales, pigments naturels, vitamine E, huile de jojoba', 'Appliquer directement sur les lèvres en partant du centre vers les commissures.', 4.00, 'MAQ-ROUG-001', TRUE, FALSE),

('Mascara Volume Intense', 'mascara-volume', 'Mascara volumisant et allongeant pour des cils spectaculaires. Brosse ergonomique qui sépare et définit chaque cil sans paquets.', 'Mascara volumisant et allongeant pour cils spectaculaires', 70.750, 56.500, 5, 'mascara_volume.jpg', 45, 10, 'Effet volume et longueur, résistant à l\'eau, brosse ergonomique, 10ml', 'Cires naturelles, pigments noirs, panthénol, vitamine B5', 'Appliquer de la racine vers la pointe des cils en zigzag.', 10.00, 'MAQ-MASC-002', TRUE, TRUE);

-- Insert sample settings
INSERT INTO settings (setting_key, setting_value, setting_type, description) VALUES
('site_name', 'La Beauté Bio Tunisie', 'string', 'Nom du site web'),
('site_description', 'Votre destination pour les meilleurs produits cosmétiques naturels en Tunisie', 'string', 'Description du site'),
('contact_email', 'contact@labeautebio.tn', 'string', 'Email de contact principal'),
('contact_phone', '+216 71 123 456', 'string', 'Téléphone de contact'),
('contact_address', 'Avenue Habib Bourguiba, Centre Ville, 1001 Tunis, Tunisie', 'string', 'Adresse physique'),
('currency', 'TND', 'string', 'Devise utilisée'),
('tax_rate', '19.00', 'number', 'Taux de TVA en pourcentage'),
('shipping_cost', '15.000', 'number', 'Coût de livraison standard'),
('free_shipping_threshold', '150.000', 'number', 'Montant minimum pour livraison gratuite'),
('items_per_page', '12', 'number', 'Nombre de produits par page'),
('enable_reviews', 'true', 'boolean', 'Activer les avis clients'),
('enable_wishlist', 'true', 'boolean', 'Activer la liste de souhaits'),
('maintenance_mode', 'false', 'boolean', 'Mode maintenance du site');

-- Insert sample newsletter subscribers
INSERT INTO newsletter_subscribers (email, name, is_active) VALUES
('marie.dupont@email.com', 'Marie Dupont', TRUE),
('jean.martin@email.com', 'Jean Martin', TRUE),
('newsletter@example.com', 'Client Newsletter', TRUE);

-- Insert sample reviews
INSERT INTO reviews (product_id, user_id, rating, title, comment, is_approved) VALUES
(1, 2, 5, 'Excellent savon !', 'Je recommande vivement ce savon à la rose. Il sent divinement bon et laisse la peau très douce.', TRUE),
(1, 3, 4, 'Très satisfaite', 'Produit de qualité, emballage soigné. Le parfum de rose est délicat et naturel.', TRUE),
(3, 2, 5, 'Parfum d\'été parfait', 'Cette eau de toilette est parfaite pour l\'été. Très rafraîchissante et l\'odeur tient bien.', TRUE),
(5, 3, 5, 'Ma crème préférée', 'J\'ai la peau sensible et cette crème à l\'aloe vera est parfaite. Elle apaise et hydrate sans irriter.', TRUE);

-- =====================================================
-- TRIGGERS AND PROCEDURES
-- =====================================================

-- Trigger to update product stock after order
DELIMITER //
CREATE TRIGGER update_stock_after_order
AFTER INSERT ON order_items
FOR EACH ROW
BEGIN
    UPDATE products
    SET stock_quantity = stock_quantity - NEW.quantity
    WHERE id = NEW.product_id;
END//
DELIMITER ;

-- Trigger to generate order number
DELIMITER //
CREATE TRIGGER generate_order_number
BEFORE INSERT ON orders
FOR EACH ROW
BEGIN
    IF NEW.order_number IS NULL OR NEW.order_number = '' THEN
        SET NEW.order_number = CONCAT('ORD-', YEAR(NOW()), MONTH(NOW()), '-', LPAD(LAST_INSERT_ID(), 6, '0'));
    END IF;
END//
DELIMITER ;

-- =====================================================
-- VIEWS FOR REPORTING
-- =====================================================

-- View for product statistics
CREATE VIEW product_stats AS
SELECT
    p.id,
    p.name,
    p.price,
    p.stock_quantity,
    c.name as category_name,
    COALESCE(AVG(r.rating), 0) as avg_rating,
    COUNT(r.id) as review_count,
    COALESCE(SUM(oi.quantity), 0) as total_sold
FROM products p
LEFT JOIN categories c ON p.category_id = c.id
LEFT JOIN reviews r ON p.id = r.product_id AND r.is_approved = TRUE
LEFT JOIN order_items oi ON p.id = oi.product_id
GROUP BY p.id, p.name, p.price, p.stock_quantity, c.name;

-- View for order summary
CREATE VIEW order_summary AS
SELECT
    o.id,
    o.order_number,
    o.created_at,
    o.status,
    o.total_amount,
    u.full_name as customer_name,
    u.email as customer_email,
    COUNT(oi.id) as item_count
FROM orders o
JOIN users u ON o.user_id = u.id
LEFT JOIN order_items oi ON o.id = oi.order_id
GROUP BY o.id, o.order_number, o.created_at, o.status, o.total_amount, u.full_name, u.email;

-- =====================================================
-- INDEXES FOR PERFORMANCE
-- =====================================================

-- Additional indexes for better performance
CREATE INDEX idx_products_price_range ON products(price, is_active);
CREATE INDEX idx_orders_date_status ON orders(created_at, status);
CREATE INDEX idx_users_role_active ON users(role, is_active);

-- =====================================================
-- COMPLETION MESSAGE
-- =====================================================
-- Database schema created successfully!
-- Default admin credentials:
-- Username: admin
-- Email: admin@cosmetics.com
-- Password: admin123
-- =====================================================
