<?php require_once __DIR__ . '/../includes/header.php'; ?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card custom-card shadow">
                <div class="card-body p-4">
                    <h2 class="text-center mb-4" style="color: var(--main-orange);">Créer un compte</h2>
                    
                    <form action="register_process.php" method="POST">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Prénom</label>
                                <input type="text" name="firstname" class="form-control" placeholder="Jean" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nom</label>
                                <input type="text" name="lastname" class="form-control" placeholder="Dupont" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Adresse Email</label>
                            <input type="email" name="email" class="form-control" placeholder="jean@exemple.fr" required>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Mot de passe</label>
                            <input type="password" name="password" class="form-control" placeholder="••••••••" required>
                            <div class="form-text">Le mot de passe sera sécurisé par hachage.</div>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 py-2 shadow-sm">S'inscrire</button>
                    </form>

                    <div class="text-center mt-4">
                        <p class="small text-muted">Déjà un compte ? <a href="login.php" style="color: var(--main-orange);">Connectez-vous</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>