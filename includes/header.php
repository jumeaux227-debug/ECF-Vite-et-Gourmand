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
<nav class="navbar navbar-expand-lg navbar-light bg-white sticky-top">
    <div class="container">
        <a class="navbar-brand fw-bold" href="index.php" style="color: var(--main-orange);">Vite et Gourmand</a>
        
        <div class="navbar-nav ms-auto align-items-center">
            <a class="nav-link text-dark fw-bold ms-2" href="index.php">Accueil</a>
            <a class="nav-link text-dark fw-bold ms-2" href="menus.php">Nos Menus</a>

            <?php if (isset($_SESSION['role_id'])): ?>
                
                <?php if($_SESSION['role_id'] == 1): ?>
                    <a class="nav-link text-danger fw-bold ms-2" href="admin_users.php">Panel Admin</a>
                <?php endif; ?>

                <?php if($_SESSION['role_id'] == 1 || $_SESSION['role_id'] == 2): ?>
                    <a class="nav-link text-warning fw-bold ms-2" href="admin_reviews.php">Modération Avis</a>
                <?php endif; ?>

                <?php if($_SESSION['role_id'] == 3): ?>
                    <a class="nav-link text-success fw-bold ms-2" href="add_review.php">Laisser un avis</a>
                <?php endif; ?>

                <span class="navbar-text ms-3">Bonjour, <strong><?= htmlspecialchars($_SESSION['prenom'] ?? 'Utilisateur') ?></strong></span>
                <a class="btn btn-outline-danger btn-sm ms-2" href="logout.php">Déconnexion</a>

            <?php else: ?>
                <a class="btn btn-outline-primary btn-sm ms-2" href="login.php">Connexion</a>
                <a class="btn btn-primary btn-sm text-white ms-2" href="register.php">Inscription</a>
            <?php endif; ?>
        </div>
    </div>
</nav>