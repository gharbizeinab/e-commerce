<?php
/**
 * Admin Add Product Page
 * Form to add new products
 */

require_once '../config/database.php';
require_once '../config/session.php';
require_once '../includes/functions.php';

// Require admin access
if (!isLoggedIn() || !isAdmin()) {
    header('Location: ../client/login.php');
    exit();
}

$page_title = 'Ajouter un produit';

$errors = [];
$success = false;

// Get categories
$categories = getCategories();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize input
    $name = sanitizeInput($_POST['name'] ?? '');
    $description = sanitizeInput($_POST['description'] ?? '');
    $price = floatval($_POST['price'] ?? 0);
    $category_id = intval($_POST['category_id'] ?? 0);
    $stock_quantity = intval($_POST['stock_quantity'] ?? 0);
    $characteristics = sanitizeInput($_POST['characteristics'] ?? '');

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

    // Simple image handling
    $image_filename = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $image_filename = 'default.jpg'; // Simplified for now
    }

    // Create product if no errors
    if (empty($errors)) {
        $product_data = [
            'name' => $name,
            'description' => $description,
            'price' => $price,
            'category_id' => $category_id,
            'image' => $image_filename,
            'stock_quantity' => $stock_quantity,
            'characteristics' => $characteristics
        ];

        if (addProduct($product_data)) {
            $success = true;
            $_SESSION['success_message'] = 'Produit ajouté avec succès !';

            // Clear form data
            $_POST = [];
        } else {
            $errors[] = 'Erreur lors de l\'ajout du produit. Veuillez réessayer.';
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
                <h1 class="h2"><i class="fas fa-plus"></i> Ajouter un produit</h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <a href="products.php" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> Retour à la liste
                    </a>
                </div>
            </div>

        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Informations du produit</h5>
            </div>
            <div class="card-body">
                <?php if ($success): ?>
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i> Produit ajouté avec succès ! 
                        <a href="products.php" class="alert-link">Voir la liste des produits</a> ou 
                        <a href="add_product.php" class="alert-link">ajouter un autre produit</a>
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
                                       value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>" 
                                       required>
                                <div class="invalid-feedback">
                                    Veuillez saisir le nom du produit.
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="description" class="form-label">Description *</label>
                                <textarea class="form-control" id="description" name="description" 
                                          rows="4" required><?php echo htmlspecialchars($_POST['description'] ?? ''); ?></textarea>
                                <div class="invalid-feedback">
                                    Veuillez saisir une description.
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="characteristics" class="form-label">Caractéristiques</label>
                                <textarea class="form-control" id="characteristics" name="characteristics" 
                                          rows="3" placeholder="Ingrédients, propriétés, conseils d'utilisation..."><?php echo htmlspecialchars($_POST['characteristics'] ?? ''); ?></textarea>
                                <div class="form-text">Détails techniques et caractéristiques du produit.</div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="image" class="form-label">Image du produit</label>
                                <input type="file" class="form-control" id="image" name="image" 
                                       accept="image/*" data-preview="imagePreview">
                                <div class="form-text">Formats acceptés: JPG, PNG, GIF (max 5MB)</div>
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
                                                <?php echo (isset($_POST['category_id']) && $_POST['category_id'] == $category['id']) ? 'selected' : ''; ?>>
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
                                <label for="price" class="form-label">Prix (€) *</label>
                                <input type="number" class="form-control" id="price" name="price" 
                                       step="0.01" min="0.01" 
                                       value="<?php echo htmlspecialchars($_POST['price'] ?? ''); ?>" 
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
                                       value="<?php echo htmlspecialchars($_POST['stock_quantity'] ?? '0'); ?>" 
                                       required>
                                <div class="invalid-feedback">
                                    Veuillez saisir une quantité valide.
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="products.php" class="btn btn-outline-secondary">
                            <i class="fas fa-times"></i> Annuler
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Ajouter le produit
                        </button>
                    </div>
                </form>
            </div>
        </div>
        </main>
    </div>
</div>

<?php include '../includes/admin_footer.php'; ?>
