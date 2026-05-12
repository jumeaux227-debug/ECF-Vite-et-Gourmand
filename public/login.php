<?php require_once __DIR__ . '/../includes/header.php'; ?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card custom-card shadow">
                <div class="card-body p-5">
                    <h2 class="text-center mb-4" style="color: var(--main-orange);">Connexion</h2>
                    
                    <?php if(isset($_GET['registration']) && $_GET['registration'] == 'success'): ?>
                        <div class="alert alert-success">Compte créé ! Connectez-vous.</div>
                    <?php endif; ?>

                    <form action="login_process.php" method="POST">
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                        <div class="mb-4">
                            <label class="form-label">Mot de passe</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100 py-2">Se connecter</button>
                    </form>
                    <div class="text-center mt-3">
                        <a href="register.php" class="small text-muted">Pas encore de compte ? S'inscrire</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>