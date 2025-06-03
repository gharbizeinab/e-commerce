<?php
require_once '../config/database.php';
require_once '../config/session.php';
require_once '../includes/functions.php';

// Redirect if not logged in
if (!isLoggedIn()) {
    header('Location: login.php');
    exit();
}

$order_id = isset($_GET['id']) ? (int)$_GET['id'] : 1;
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Détail Commande #<?php echo $order_id; ?> - La Beauté Bio</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            border-radius: 15px;
            padding: 40px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }

        h1 {
            color: #7c943f;
            text-align: center;
            margin-bottom: 30px;
        }

        .demo-notice {
            background: #e7f3ff;
            border: 1px solid #b3d9ff;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 30px;
            text-align: center;
        }

        .btn {
            background: #7c943f;
            color: white;
            padding: 12px 25px;
            border: none;
            border-radius: 8px;
            text-decoration: none;
            font-weight: bold;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s;
            margin: 10px;
        }

        .btn:hover {
            background: #5d722e;
            color: white;
            text-decoration: none;
        }

        .actions {
            text-align: center;
            margin-top: 30px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1><i class="fas fa-receipt"></i> Détail de la Commande #<?php echo $order_id; ?></h1>

        <div class="demo-notice">
            <h3 style="color: #0066cc; margin-bottom: 10px;">📋 Page de Démonstration</h3>
            <p style="color: #333;">Cette page affiche les détails d'une commande. Les vraies commandes apparaîtront ici une fois que vous aurez passé des commandes sur le site.</p>
        </div>

        <div style="background: #f8f9fa; padding: 20px; border-radius: 10px; margin-bottom: 20px;">
            <h3 style="color: #7c943f; margin-bottom: 15px;">Informations de la commande</h3>
            <p><strong>Numéro :</strong> #<?php echo $order_id; ?></p>
            <p><strong>Date :</strong> <?php echo date('d/m/Y à H:i'); ?></p>
            <p><strong>Statut :</strong> <span style="background: #fff3cd; color: #856404; padding: 4px 8px; border-radius: 15px; font-size: 0.9rem;">En attente</span></p>
            <p><strong>Total :</strong> <?php echo formatPrice(25.500); ?></p>
        </div>

        <div style="background: #f8f9fa; padding: 20px; border-radius: 10px;">
            <h3 style="color: #7c943f; margin-bottom: 15px;">Articles commandés</h3>
            <div style="display: flex; align-items: center; padding: 15px; background: white; border-radius: 8px;">
                <div style="width: 60px; height: 60px; background: #e9ecef; border-radius: 8px; display: flex; align-items: center; justify-content: center; margin-right: 15px;">
                    <i class="fas fa-image" style="color: #ccc;"></i>
                </div>
                <div style="flex: 1;">
                    <div style="font-weight: bold; margin-bottom: 5px;">Produit de démonstration</div>
                    <div style="color: #7c943f;">25,500 TND × 1 = <strong>25,500 TND</strong></div>
                </div>
            </div>
        </div>

        <div class="actions">
            <a href="orders.php" class="btn">
                <i class="fas fa-arrow-left"></i> Retour à mes commandes
            </a>
            <a href="../products.php" class="btn" style="background: transparent; color: #7c943f; border: 2px solid #7c943f;">
                <i class="fas fa-shopping-cart"></i> Continuer mes achats
            </a>
        </div>
    </div>
</body>
</html>
