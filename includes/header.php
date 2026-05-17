<?php 
// On démarre la session au tout début du header pour qu'elle soit active sur toutes les pages
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/style.css">
    <title>Vite et Gourmand</title>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom">
    <div class="container px-5">
        <a class="navbar-brand fw-bold text-dark fs-4" href="index.php">Vite et Gourmand</a>
        
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav ms-auto mb-2 mb-lg-0 align-items-center">
                <li class="nav-item"><a class="nav-link text-dark fw-bold ms-2" href="index.php">Accueil</a></li>
                <li class="nav-item"><a class="nav-link text-dark fw-bold ms-2" href="menus.php">Nos Menus</a></li>

                <?php if (isset($_SESSION['role_id'])): ?>
                    <?php if($_SESSION['role_id'] == 1): ?>
                        <li class="nav-item"><a class="nav-link text-danger fw-bold ms-2" href="admin_users.php">Panel Admin</a></li>
                    <?php endif; ?>

                    <?php if($_SESSION['role_id'] == 1 || $_SESSION['role_id'] == 2): ?>
                        <li class="nav-item"><a class="nav-link text-warning fw-bold ms-2" href="admin_reviews.php">Modération Avis</a></li>
                    <?php endif; ?>

                    <?php if($_SESSION['role_id'] == 3): ?>
                        <li class="nav-item"><a class="nav-link text-success fw-bold ms-2" href="add_review.php">Laisser un avis</a></li>
                    <?php endif; ?>

                    <li class="nav-item ms-3 text-muted small">Bonjour, <strong><?= htmlspecialchars($_SESSION['prenom'] ?? 'Utilisateur') ?></strong></li>
                    <li class="nav-item ms-2"><a class="btn btn-outline-danger btn-sm" href="logout.php">Déconnexion</a></li>

                <?php else: ?>
                    <li class="nav-item ms-2"><a class="btn btn-outline-primary btn-sm" href="login.php">Connexion</a></li>
                    <li class="nav-item ms-2"><a class="btn btn-primary btn-sm text-white" href="register.php">Inscription</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>