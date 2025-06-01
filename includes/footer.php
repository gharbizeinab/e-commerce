    </main>

    <footer class="bg-dark text-light mt-5 py-4">
        <div class="container">
            <div class="row">
                <div class="col-md-4">
                    <h5><i class="fas fa-spa"></i> Cosmétiques Élégance</h5>
                    <p>Votre destination pour les meilleurs produits cosmétiques naturels et de qualité.</p>
                </div>
                <div class="col-md-4">
                    <h5>Nos Produits</h5>
                    <ul class="list-unstyled">
                        <li><a href="products.php?category=1" class="text-light text-decoration-none">Savons</a></li>
                        <li><a href="products.php?category=2" class="text-light text-decoration-none">Parfums</a></li>
                        <li><a href="products.php?category=3" class="text-light text-decoration-none">Body Splash</a></li>
                        <li><a href="products.php?category=4" class="text-light text-decoration-none">Crèmes</a></li>
                        <li><a href="products.php?category=5" class="text-light text-decoration-none">Maquillage</a></li>
                    </ul>
                </div>
                <div class="col-md-4">
                    <h5>Contact</h5>
                    <p>
                        <i class="fas fa-envelope"></i> contact@cosmetics-elegance.com<br>
                        <i class="fas fa-phone"></i> +33 1 23 45 67 89<br>
                        <i class="fas fa-map-marker-alt"></i> 123 Rue de la Beauté, Paris
                    </p>
                    <div class="social-links">
                        <a href="#" class="text-light me-3"><i class="fab fa-facebook"></i></a>
                        <a href="#" class="text-light me-3"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="text-light me-3"><i class="fab fa-twitter"></i></a>
                    </div>
                </div>
            </div>
            <hr class="my-4">
            <div class="row">
                <div class="col-md-6">
                    <p>&copy; <?php echo date('Y'); ?> Cosmétiques Élégance. Tous droits réservés.</p>
                </div>
                <div class="col-md-6 text-end">
                    <a href="#" class="text-light text-decoration-none me-3">Mentions légales</a>
                    <a href="#" class="text-light text-decoration-none">Politique de confidentialité</a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Custom JS -->
    <script src="<?php echo isset($js_path) ? $js_path : 'assets/js/'; ?>main.js"></script>
</body>
</html>
