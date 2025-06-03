<?php
require_once '../config/database.php';
require_once '../config/session.php';
require_once '../includes/functions.php';

// Check if user is admin
if (!isLoggedIn() || !isAdmin()) {
    header('Location: ../client/login.php');
    exit();
}

$errors = [];
$success = false;

// Get product ID from URL
$product_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($product_id <= 0) {
    header('Location: products.php');
    exit();
}

// Get product data
$result = executeQuery("SELECT * FROM products WHERE id = $product_id");
if (mysqli_num_rows($result) == 0) {
    header('Location: products.php');
    exit();
}
$product = mysqli_fetch_assoc($result);

// Get categories
$result = executeQuery("SELECT * FROM categories ORDER BY name");
$categories = [];
while ($row = mysqli_fetch_assoc($result)) {
    $categories[] = $row;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = cleanInput($_POST['name'] ?? '');
    $description = cleanInput($_POST['description'] ?? '');
    $price = floatval($_POST['price'] ?? 0);
    $category_id = (int)($_POST['category_id'] ?? 0);
    $stock_quantity = (int)($_POST['stock_quantity'] ?? 0);
    $characteristics = cleanInput($_POST['characteristics'] ?? '');

    // Validation
    if (empty($name)) {
        $errors[] = 'Le nom du produit est requis.';
    }

    if (empty($description)) {
        $errors[] = 'La description est requise.';
    }

    if ($price <= 0) {
        $errors[] = 'Le prix doit être supérieur à 0.';
    }

    if ($category_id <= 0) {
        $errors[] = 'Veuillez sélectionner une catégorie.';
    }

    if ($stock_quantity < 0) {
        $errors[] = 'La quantité en stock ne peut pas être négative.';
    }

    // Handle image upload
    $image_path = $product['image']; // Keep existing image by default

    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = '../images/products/';
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $max_size = 5 * 1024 * 1024; // 5MB

        if (!in_array($_FILES['image']['type'], $allowed_types)) {
            $errors[] = 'Type de fichier non autorisé. Utilisez JPG, PNG, GIF ou WebP.';
        } elseif ($_FILES['image']['size'] > $max_size) {
            $errors[] = 'Le fichier est trop volumineux (max 5MB).';
        } else {
            $file_extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $new_filename = 'product_' . $product_id . '_' . time() . '.' . $file_extension;
            $upload_path = $upload_dir . $new_filename;

            if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
                // Delete old image if it exists and is different
                if ($product['image'] && $product['image'] !== 'images/products/' . $new_filename && file_exists('../' . $product['image'])) {
                    unlink('../' . $product['image']);
                }
                $image_path = 'images/products/' . $new_filename;
            } else {
                $errors[] = 'Erreur lors du téléchargement de l\'image.';
            }
        }
    }

    // Update product if no errors
    if (empty($errors)) {
        $sql = "UPDATE products SET
                name = '$name',
                description = '$description',
                price = $price,
                category_id = $category_id,
                stock_quantity = $stock_quantity,
                image = '$image_path',
                characteristics = '$characteristics',
                updated_at = NOW()
                WHERE id = $product_id";

        $result = executeQuery($sql);

        if ($result) {
            $success = true;
            $_SESSION['success_message'] = 'Produit modifié avec succès !';

            // Refresh product data
            $result = executeQuery("SELECT * FROM products WHERE id = $product_id");
            $product = mysqli_fetch_assoc($result);
        } else {
            $errors[] = 'Erreur lors de la modification du produit.';
        }
    }
}
?>

<?php include '../includes/admin_header.php'; ?>

<div class="container-fluid">
    <div class="row">
        <?php include '../includes/admin_sidebar.php'; ?>

        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2"><i class="fas fa-edit"></i> Modifier le produit</h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <a href="../product_detail.php?id=<?php echo $product['id']; ?>" class="btn btn-outline-info me-2">
                        <i class="fas fa-eye"></i> Voir le produit
                    </a>
                    <a href="products.php" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> Retour à la liste
                    </a>
                </div>
            </div>

        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Modifier: <?php echo htmlspecialchars($product['name']); ?></h5>
            </div>
            <div class="card-body">
                <?php if ($success): ?>
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i> Produit modifié avec succès ! 
                        <a href="products.php" class="alert-link">Retour à la liste</a>
                    </div>
                <?php endif; ?>

                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle"></i>
                        <ul class="mb-0">
                            <?php foreach ($errors as $error): ?>
                                <li><?php echo htmlspecialchars($error); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <form method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>

                    
                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label for="name" class="form-label">Nom du produit *</label>
                                <input type="text" class="form-control" id="name" name="name" 
                                       value="<?php echo htmlspecialchars($_POST['name'] ?? $product['name']); ?>" 
                                       required>
                                <div class="invalid-feedback">
                                    Veuillez saisir le nom du produit.
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="description" class="form-label">Description *</label>
                                <textarea class="form-control" id="description" name="description" 
                                          rows="4" required><?php echo htmlspecialchars($_POST['description'] ?? $product['description']); ?></textarea>
                                <div class="invalid-feedback">
                                    Veuillez saisir une description.
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="characteristics" class="form-label">Caractéristiques</label>
                                <textarea class="form-control" id="characteristics" name="characteristics" 
                                          rows="3" placeholder="Ingrédients, propriétés, conseils d'utilisation..."><?php echo htmlspecialchars($_POST['characteristics'] ?? $product['characteristics']); ?></textarea>
                                <div class="form-text">Détails techniques et caractéristiques du produit.</div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="image" class="form-label">Image du produit</label>
                                
                                <?php if ($product['image']): ?>
                                    <div class="mb-2">
                                        <img src="../<?php echo htmlspecialchars($product['image']); ?>"
                                             alt="Image actuelle" class="img-thumbnail" style="max-width: 200px;">
                                        <div class="form-text">Image actuelle</div>
                                    </div>
                                <?php endif; ?>
                                
                                <input type="file" class="form-control" id="image" name="image" 
                                       accept="image/*" data-preview="imagePreview">
                                <div class="form-text">Formats acceptés: JPG, PNG, GIF (max 5MB). Laissez vide pour conserver l'image actuelle.</div>
                                <img id="imagePreview" src="#" alt="Aperçu" 
                                     class="img-thumbnail mt-2" style="display: none; max-width: 200px;">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="category_id" class="form-label">Catégorie *</label>
                                <select class="form-select" id="category_id" name="category_id" required>
                                    <option value="">Sélectionner une catégorie</option>
                                    <?php foreach ($categories as $category): ?>
                                        <option value="<?php echo $category['id']; ?>" 
                                                <?php echo (($_POST['category_id'] ?? $product['category_id']) == $category['id']) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($category['name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="invalid-feedback">
                                    Veuillez sélectionner une catégorie.
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="price" class="form-label">Prix (TND) *</label>
                                <input type="number" class="form-control" id="price" name="price" 
                                       step="0.01" min="0.01" 
                                       value="<?php echo htmlspecialchars($_POST['price'] ?? $product['price']); ?>" 
                                       required>
                                <div class="invalid-feedback">
                                    Veuillez saisir un prix valide.
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="stock_quantity" class="form-label">Quantité en stock *</label>
                                <input type="number" class="form-control" id="stock_quantity" name="stock_quantity" 
                                       min="0" 
                                       value="<?php echo htmlspecialchars($_POST['stock_quantity'] ?? $product['stock_quantity']); ?>" 
                                       required>
                                <div class="invalid-feedback">
                                    Veuillez saisir une quantité valide.
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between">
                        <div>
                            <a href="products.php" class="btn btn-outline-secondary">
                                <i class="fas fa-times"></i> Annuler
                            </a>
                            <a href="delete_product.php?id=<?php echo $product['id']; ?>" 
                               class="btn btn-outline-danger ms-2 btn-delete" 
                               data-item-name="<?php echo htmlspecialchars($product['name']); ?>">
                                <i class="fas fa-trash"></i> Supprimer
                            </a>
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Enregistrer les modifications
                        </button>
                    </div>
                </form>
            </div>
        </div>
        </main>
    </div>
</div>

<?php include '../includes/admin_footer.php'; ?>
