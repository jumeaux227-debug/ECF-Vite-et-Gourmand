<?php 
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/db_connect.php';

if (!isset($_SESSION['role_id']) || $_SESSION['role_id'] != 1) {
    header('Location: index.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = trim($_POST['nom']);
    $prenom = trim($_POST['prenom']);
    $email = trim($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role_id = 2; // Rôle forcé à Employé conformément au cahier des charges RH
    $statut_compte = 1; // Compte actif par défaut

    // Vérification si l'email existe déjà
    $check = $pdo->prepare("SELECT utilisateur_id FROM utilisateur WHERE email = ?");
    $check->execute([$email]);
    
    if ($check->fetch()) {
        $error = "Cet adresse email est déjà attribuée à un autre compte.";
    } else {
        $query = "INSERT INTO utilisateur (nom, prenom, email, mot_de_passe, role_id, statut_compte) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($query);
        if ($stmt->execute([$nom, $prenom, $email, $password, $role_id, $statut_compte])) {
            header('Location: admin_rh.php?success=1');
            exit();
        } else {
            $error = "Une erreur technique est survenue.";
        }
    }
}
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="mb-3"><a href="admin_rh.php" class="btn btn-outline-secondary btn-sm">← Annuler</a></div>
            <div class="card shadow-sm border-0 p-4">
                <h3 class="fw-bold mb-3">Créer un profil Employé</h3>
                
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger small text-center"><?= $error ?></div>
                <?php endif; ?>

                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Nom de famille</label>
                        <input type="text" name="nom" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Prénom</label>
                        <input type="text" name="prenom" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Adresse Email Professionnelle</label>
                        <input type="email" name="email" class="form-control" placeholder="collaborateur@julie-jose.fr" required>
                    </div>
                    <div class="mb-4">
                        <label class="form-label small fw-bold">Mot de passe initial</label>
                        <input type="password" name="password" class="form-control" required>
                        <div class="form-text small text-muted">L'employé pourra modifier son mot de passe lors de sa première session.</div>
                    </div>
                    <button type="submit" class="btn btn-primary text-white fw-bold w-100">Enregistrer l'employé</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>